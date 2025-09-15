<?php
declare(strict_types=1);

namespace Yw\YwT3sbootstrapAddon\DataProcessing;

use TYPO3\CMS\Core\Context\Context;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Database\Query\Restriction\DeletedRestriction;
use TYPO3\CMS\Core\Database\Query\Restriction\HiddenRestriction;
use TYPO3\CMS\Core\Database\Query\Restriction\StartTimeRestriction;
use TYPO3\CMS\Core\Database\Query\Restriction\EndTimeRestriction;
use TYPO3\CMS\Core\Database\Query\Restriction\FrontendGroupRestriction;
use TYPO3\CMS\Core\Resource\FileReference;
use TYPO3\CMS\Core\Resource\FileRepository;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;
use TYPO3\CMS\Frontend\ContentObject\DataProcessorInterface;

final class PagesCarouselProcessor implements DataProcessorInterface
{
    public function process(ContentObjectRenderer $cObj, array $conf, array $processorConf, array $processedData): array
    {
        $data = $processedData['data'] ?? [];
        $ttContentUid = (int)($data['uid'] ?? 0);

        // NEW: read the toggle + limit
        $showLatest = ((int)($data['tx_yw_show_latest'] ?? 0) === 1);
        $limit = (int)($data['tx_yw_latest_limit'] ?? 5);
        if ($limit < 1) { $limit = 1; }
        if ($limit > 100) { $limit = 100; }

        // Common resources
        $fileRepo = GeneralUtility::makeInstance(FileRepository::class);

        // Prepare a base QB for pages with FE enable-field restrictions
        $qbBase = GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable('pages');
        $qbBase->getRestrictions()->removeAll();
        $qbBase->getRestrictions()
            ->add(GeneralUtility::makeInstance(DeletedRestriction::class))
            ->add(GeneralUtility::makeInstance(HiddenRestriction::class))
            ->add(GeneralUtility::makeInstance(StartTimeRestriction::class))
            ->add(GeneralUtility::makeInstance(EndTimeRestriction::class))
            ->add(GeneralUtility::makeInstance(FrontendGroupRestriction::class));

        // Language context (for label overlay)
        [$langId, $contentId] = $this->getLanguageIds();
        $effectiveLang = $contentId > 0 ? $contentId : $langId;

        $items = [];


// === MODE A: Latest pages (restrict to current page language,
//             exclude hidden-in-menu, title starts with "ym-") ===
if ($showLatest) {
    // 1) fetch recent *default-language* pages (doktype=1)
    $qb = clone $qbBase;
    $qb->select('uid', 'title', 'subtitle', 'nav_title', 'description', 'tstamp', 'sys_language_uid')
       ->from('pages')
       ->where(
           $qb->expr()->eq('doktype', $qb->createNamedParameter(1, \PDO::PARAM_INT)),
           $qb->expr()->eq('sys_language_uid', $qb->createNamedParameter(0, \PDO::PARAM_INT))
       );

    // Exclude hidden-in-menu if the column exists
    if ($this->hasColumn('pages', 'nav_hide')) {
        $qb->andWhere(
            $qb->expr()->eq('nav_hide', $qb->createNamedParameter(0, \PDO::PARAM_INT))
        );
    }

    // Title must start with "ym-"
    $qb->andWhere(
        $qb->expr()->like('title', $qb->createNamedParameter('ym-%'))
    );

    // grab more than needed; we will keep only those with a translation
    $candidateRows = $qb->orderBy('tstamp', 'DESC')
        ->setMaxResults(max($limit * 5, $limit))
        ->executeQuery()
        ->fetchAllAssociative() ?: [];

    $selected = [];
    $overlayCache = [];

    if ($effectiveLang > 0) {
        // 2) keep only those that *have* a translation in current FE language
        foreach ($candidateRows as $row) {
            $overlayRow = $this->fetchTranslatedPageRow($qbBase, (int)$row['uid'], $effectiveLang);
            if (is_array($overlayRow)) {
                $overlayCache[(int)$row['uid']] = $overlayRow;
                $selected[] = $row;
                if (count($selected) >= $limit) {
                    break;
                }
            }
        }
    } else {
        // default language – just take the newest
        $selected = array_slice($candidateRows, 0, $limit);
    }

    // 3) build items from the filtered list
    foreach ($selected as $row) {
        $overlayRow = $overlayCache[(int)$row['uid']] ?? null;
        $labelRow   = $this->mergeLabelFields($row, $overlayRow);

        // Prefer translated image, else default
        $image = null;
        if (is_array($overlayRow) && isset($overlayRow['uid'])) {
            $image = $this->firstImageByRelationsFromPage($fileRepo, (int)$overlayRow['uid'], [
                'tx_seo_twitter_image', 'twitter_image', 'og_image', 'tx_csseo_tw_image',
            ]);
        }
        if ($image === null) {
            $image = $this->firstImageByRelationsFromPage($fileRepo, (int)$row['uid'], [
                'tx_seo_twitter_image', 'twitter_image', 'og_image', 'tx_csseo_tw_image',
            ]);
        }

        // HREF uses DEFAULT-language nav_title only (no fallback)
        $defaultNavTitleHref = (string)($row['nav_title'] ?? '');

        $items[] = [
            'uid'         => (int)$row['uid'],
            'title'       => ($labelRow['subtitle'] ?? '') !== '' ? (string)$labelRow['subtitle'] : (string)($labelRow['title'] ?? ''),
            'linkText'    => $defaultNavTitleHref, // href content
            'linkLabel'   => ($labelRow['nav_title'] ?? '') !== '' ? (string)$labelRow['nav_title'] : (string)($labelRow['title'] ?? ''),
            'description' => (string)($labelRow['description'] ?? ''),
            'image'       => $image,
        ];
    }

    $processedData['ywCarouselItems'] = $items;
    return $processedData;
}

        // === MODE B: Manual selection (MM first, CSV fallback) ===
        $pageUids = $this->uidsFromMm($ttContentUid, 'tx_yw_bs_carousel_pages_mm', 'uid_foreign');
        if (!$pageUids) {
            $csv = (string)($data['tx_yw_selected_pages'] ?? '');
            if ($csv !== '') {
                $pageUids = $this->uidsFromCsv($csv);
            }
        }
        if (!$pageUids) {
            $processedData['ywCarouselItems'] = [];
            return $processedData;
        }

        foreach ($pageUids as $pid) {
            $pid = (int)$pid;
            if ($pid <= 0) {
                continue;
            }

            $qb = clone $qbBase;
            $row = $qb->select('uid', 'title', 'subtitle', 'nav_title', 'description')
                ->from('pages')
                ->where($qb->expr()->eq('uid', $qb->createNamedParameter($pid, \PDO::PARAM_INT)))
                ->setMaxResults(1)
                ->executeQuery()
                ->fetchAssociative();

            if (!$row) {
                continue;
            }

            $overlayRow = null;
            if ($effectiveLang > 0) {
                $overlayRow = $this->fetchTranslatedPageRow($qbBase, (int)$row['uid'], $effectiveLang);
            }
            $labelRow = $this->mergeLabelFields($row, $overlayRow);

            $image = null;
            if (is_array($overlayRow) && isset($overlayRow['uid'])) {
                $image = $this->firstImageByRelationsFromPage($fileRepo, (int)$overlayRow['uid'], [
                    'tx_seo_twitter_image', 'twitter_image', 'og_image', 'tx_csseo_tw_image',
                ]);
            }
            if ($image === null) {
                $image = $this->firstImageByRelationsFromPage($fileRepo, (int)$row['uid'], [
                    'tx_seo_twitter_image', 'twitter_image', 'og_image', 'tx_csseo_tw_image',
                ]);
            }

            // IMPORTANT: href = DEFAULT-language nav_title only
            $defaultNavTitleHref = (string)($row['nav_title'] ?? '');

            $items[] = [
                'uid'         => (int)$row['uid'],
                'title'       => ($labelRow['subtitle'] ?? '') !== '' ? (string)$labelRow['subtitle'] : (string)($labelRow['title'] ?? ''),
                'linkText'    => $defaultNavTitleHref, // <-- href content
                'linkLabel'   => ($labelRow['nav_title'] ?? '') !== '' ? (string)$labelRow['nav_title'] : (string)($labelRow['title'] ?? ''),
                'description' => (string)($labelRow['description'] ?? ''),
                'image'       => $image,
            ];
        }

        $processedData['ywCarouselItems'] = $items;
        return $processedData;
    }

    // ===== Localization / helpers =====

    private function getLanguageIds(): array
    {
        try {
            /** @var Context $ctx */
            $ctx = GeneralUtility::makeInstance(Context::class);
            $aspect = $ctx->getAspect('language');
            $langId = (int)$aspect->getId();
            $contentId = method_exists($aspect, 'getContentId') ? (int)$aspect->getContentId() : $langId;
            return [$langId, $contentId];
        } catch (\Throwable $e) {
            return [0, 0];
        }
    }

    private function fetchTranslatedPageRow($qbBase, int $parentUid, int $langId): ?array
    {
        $qb = clone $qbBase;

        // Only include parent column(s) that exist
        $hasL10n = $this->hasColumn('pages', 'l10n_parent');
        $hasL18n = $this->hasColumn('pages', 'l18n_parent');

        if (!$hasL10n && !$hasL18n) {
            return null;
        }

        $constraints = [];
        if ($hasL10n) {
            $constraints[] = $qb->expr()->eq('l10n_parent', $qb->createNamedParameter($parentUid, \PDO::PARAM_INT));
        }
        if ($hasL18n) {
            $constraints[] = $qb->expr()->eq('l18n_parent', $qb->createNamedParameter($parentUid, \PDO::PARAM_INT));
        }

        $parentConstraint = count($constraints) === 2
            ? (method_exists($qb->expr(), 'or') ? $qb->expr()->or($constraints[0], $constraints[1]) : $qb->expr()->orX($constraints[0], $constraints[1]))
            : $constraints[0];

        $row = $qb->select('uid', 'title', 'subtitle', 'nav_title', 'description', 'sys_language_uid')
            ->from('pages')
            ->where(
                $parentConstraint,
                $qb->expr()->eq('sys_language_uid', $qb->createNamedParameter($langId, \PDO::PARAM_INT))
            )
            ->setMaxResults(1)
            ->executeQuery()
            ->fetchAssociative();

        return $row ?: null;
    }

    private function mergeLabelFields(array $base, ?array $overlay): array
    {
        if (!is_array($overlay)) {
            return $base;
        }
        foreach (['title', 'subtitle', 'nav_title', 'description'] as $f) {
            if (isset($overlay[$f]) && $overlay[$f] !== '') {
                $base[$f] = $overlay[$f];
            }
        }
        return $base;
    }

    private function firstImageByRelationsFromPage(FileRepository $fileRepo, int $pageUid, array $fieldNames): ?FileReference
    {
        foreach ($fieldNames as $field) {
            try {
                $refs = $fileRepo->findByRelation('pages', $field, $pageUid);
            } catch (\Throwable $e) {
                $refs = [];
            }
            if (!empty($refs) && $refs[0] instanceof FileReference) {
                return $refs[0];
            }
        }
        return null;
    }

    private function uidsFromMm(int $localUid, string $mmTable, string $foreignField = 'uid_foreign'): array
    {
        if ($localUid <= 0) {
            return [];
        }
        $conn = GeneralUtility::makeInstance(ConnectionPool::class)->getConnectionForTable($mmTable);
        $rows = $conn->select(
            [$foreignField],
            $mmTable,
            ['uid_local' => $localUid],
            [],
            ['sorting' => 'ASC']
        )->fetchAllAssociative();

        if (!$rows) {
            return [];
        }
        return array_values(array_map('intval', array_column($rows, $foreignField)));
    }

    private function uidsFromCsv(string $csv): array
    {
        $csv = trim($csv);
        if ($csv === '') {
            return [];
        }
        $out = [];
        foreach (explode(',', $csv) as $t) {
            $t = trim($t);
            if ($t === '') {
                continue;
            }
            if (ctype_digit($t)) {
                $id = (int)$t;
                if ($id > 0) {
                    $out[] = $id;
                }
            }
        }
        return $out;
    }

    private function hasColumn(string $table, string $column): bool
    {
        try {
            $conn = GeneralUtility::makeInstance(ConnectionPool::class)->getConnectionForTable($table);
            if (!method_exists($conn, 'getSchemaManager')) {
                return true;
            }
            $sm = $conn->getSchemaManager();
            $cols = $sm->listTableColumns($table);
            $keys = array_map('strtolower', array_keys($cols));
            return in_array(strtolower($column), $keys, true);
        } catch (\Throwable $e) {
            return false;
        }
    }
}

