<?php

namespace NITSAN\NsYwContextmodal\Renderer;

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;

class PageRenderer 
{

    public function render()
    {
        $requestData = GeneralUtility::_GET();
        $mainPid = (int)($requestData['mainPid'] ?? 0);

        if ($mainPid <= 0) {
            return json_encode([]);
        }

        $isLocalizedPage = false;
        $subPageSlug = [];
        $mainPageLangUid = $this->getSysLangUid($mainPid);

        if ($mainPageLangUid >= 1) {
            $isLocalizedPage = true;
            $mainPid = $this->getL10nParent($mainPid);
        }

        $subPages = $this->getSubPages($mainPid);

        foreach ($subPages as $item) {
            $localizePid = $isLocalizedPage ? $this->getLocalizePid($item) : $item;
            $subPageSlug[] = $this->getSlug($localizePid);
        }

        return json_encode($subPageSlug);
    }

    protected function getSubPages($mainPid)
    {
        $contentObjectRenderer = GeneralUtility::makeInstance(ContentObjectRenderer::class);
        $subPagesString = $contentObjectRenderer->getTreeList($mainPid, 1);
        return array_map('intval', explode(',', $subPagesString));
    }

    protected function getSlug($pid) 
    {
        return $this->fetchPageField($pid, 'slug');
    }

    protected function getSysLangUid($pid)
    {
        return $this->fetchPageField($pid, 'sys_language_uid');
    }

    protected function getL10nParent($pid)
    {
        return $this->fetchPageField($pid, 'l10n_parent');
    }

    protected function getLocalizePid($pid)
    {
        return $this->fetchPageField($pid, 'uid', 'l10n_parent');
    }

    protected function fetchPageField($pid, $field, $whereField = 'uid')
    {
        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)
            ->getQueryBuilderForTable('pages');
        return $queryBuilder
            ->select($field)
            ->from('pages')
            ->where(
                $queryBuilder->expr()->eq($whereField, $queryBuilder->createNamedParameter($pid))
            )
            ->execute()
            ->fetchOne();
    }
}
