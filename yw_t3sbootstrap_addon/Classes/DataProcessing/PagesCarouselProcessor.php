<?php
declare(strict_types=1);

namespace Yw\YwT3sbootstrapAddon\DataProcessing;

use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Database\Query\Restriction\DeletedRestriction;
use TYPO3\CMS\Core\Resource\FileRepository;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;
use TYPO3\CMS\Frontend\ContentObject\DataProcessorInterface;

final class PagesCarouselProcessor implements DataProcessorInterface
{
    public function process(ContentObjectRenderer $cObj, array $conf, array $processorConf, array $processedData): array
    {
        $ttContentUid = (int)$processedData['data']['uid'];

        // 1) Read MM (ordered)
        $mmConn = GeneralUtility::makeInstance(ConnectionPool::class)
            ->getConnectionForTable('tx_yw_bs_carousel_pages_mm');

        $mmRows = $mmConn->select(
            ['uid_foreign'],
            'tx_yw_bs_carousel_pages_mm',
            ['uid_local' => $ttContentUid],
            [],
            ['sorting' => 'ASC']
        )->fetchAllAssociative();

        $pageUids = array_map('intval', array_column($mmRows, 'uid_foreign'));

        // 2) CSV fallback (if ever needed)
        if (!$pageUids) {
            $csv = (string)($processedData['data']['tx_yw_selected_pages'] ?? '');
            if ($csv !== '') {
                $pageUids = array_values(array_filter(array_map('intval', explode(',', $csv))));
            }
        }

        if (!$pageUids) {
            $processedData['ywCarouselItems'] = [];
            return $processedData;
        }

        $fileRepo = GeneralUtility::makeInstance(FileRepository::class);
        $items = [];

        foreach ($pageUids as $pid) {
            // Fresh QB per loop to avoid residual state
            $qb = GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable('pages');
            $qb->getRestrictions()->removeAll()->add(GeneralUtility::makeInstance(DeletedRestriction::class));

            $page = $qb->select('uid', 'title', 'subtitle', 'nav_title', 'description')
                ->from('pages')
                ->where($qb->expr()->eq('uid', $qb->createNamedParameter($pid, \PDO::PARAM_INT)))
                ->executeQuery()
                ->fetchAssociative();

            if (!$page) {
                continue;
            }

            // First available social image
            $img = $this->firstImageByRelations($fileRepo, (int)$page['uid'], [
                'tx_seo_twitter_image',
                'twitter_image',
                'og_image',
                'tx_csseo_tw_image',
            ]);

            $items[] = [
                'uid'         => (int)$page['uid'],
                'title'       => $page['subtitle'] ?: $page['title'],
                'linkText'    => $page['nav_title'] ?: $page['title'],
                'description' => (string)($page['description'] ?? ''),
                'image'       => $img,
            ];
        }

        $processedData['ywCarouselItems'] = $items;
        return $processedData;
    }

    private function firstImageByRelations(FileRepository $fileRepo, int $pageUid, array $fieldNames)
    {
        foreach ($fieldNames as $field) {
            $refs = $fileRepo->findByRelation('pages', $field, $pageUid);
            if (!empty($refs)) {
                return $refs[0];
            }
        }
        return null;
    }
}

