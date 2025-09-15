<?php

namespace NITSAN\NsYwContextmodal\Renderer;

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Database\ConnectionPool;

class SubTitleRenderer 
{

    public function render()
    {
        $subTitleArray = [];
        if (GeneralUtility::_GP('slug')) {
            $slugArray = GeneralUtility::trimExplode(',',GeneralUtility::_GP('slug'));
            
            foreach($slugArray as $slug) {
                $subTitleArray[$slug] = $this->getSubTitle($slug) ?? '';
            }

        }
        return json_encode($subTitleArray,1);
    }

    protected function getSubTitle($slug)
    {
        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)
        ->getQueryBuilderForTable('pages');
        $result =  $queryBuilder
        ->select('nav_title')
        ->from('pages')
        ->where(
            $queryBuilder->expr()->eq('slug',$queryBuilder->createNamedParameter($slug, \PDO::PARAM_STR)),
            $queryBuilder->expr()->eq('deleted',$queryBuilder->createNamedParameter(0, \PDO::PARAM_INT))
        )
        ->orWhere(
            $queryBuilder->expr()->eq('slug', $queryBuilder->createNamedParameter('/'.$slug, \PDO::PARAM_STR)),
        )
        ->executeQuery()->fetchOne();
        return $result ? $result : '';
    }
}
