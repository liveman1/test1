<?php
namespace NITSAN\NsYwFavorites\ViewHelpers;

use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractTagBasedViewHelper;
use PDO;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Database\Query\QueryBuilder;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class SubtitleViewHelper extends AbstractTagBasedViewHelper
{
    public function initializeArguments()
    {
        parent::initializeArguments();
        $this->registerUniversalTagAttributes();
        $this->registerArgument('uid', 'int', 'UID to fetch subtitle', true);
        $this->registerArgument('property', 'string', 'Identifier of property to fetch data', false, 'subtitle');
        $this->registerArgument('isMainLang', 'string', '', false);
    }

    public function render()
    {
        $uid = (int)$this->arguments['uid'];
        $isMainLang = $this->arguments['isMainLang'];
        if ($uid<=0) {
            return false;
        }
        $languageAspect = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\TYPO3\CMS\Core\Context\Context::class)->getAspect('language');
        $sys_language_uid = $languageAspect->getId();
        /** @var QueryBuilder $queryBuilder */
        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)
            ->getQueryBuilderForTable('pages');

        if($sys_language_uid > 0 && $isMainLang == 0){
            $row =  $queryBuilder
            ->select('subtitle','abstract','twitter_description')
            ->from('pages')
            ->where(
                $queryBuilder->expr()->eq(
                    'l10n_parent',
                    $queryBuilder->createNamedParameter($uid, PDO::PARAM_INT)
                ),
                $queryBuilder->expr()->eq(
                    'sys_language_uid',
                    $queryBuilder->createNamedParameter($sys_language_uid, PDO::PARAM_INT)
                ),
            )
            ->execute()
            ->fetch();

        } else {
            if($isMainLang == 1) {
                $sys_language_uid = 0;
            } 
            $row =  $queryBuilder
            ->select('subtitle','abstract','twitter_description')
            ->from('pages')
            ->where(
                $queryBuilder->expr()->eq(
                    'uid',
                    $queryBuilder->createNamedParameter($uid, PDO::PARAM_INT)
                ),
                $queryBuilder->expr()->eq(
                    'sys_language_uid',
                    $queryBuilder->createNamedParameter($sys_language_uid, PDO::PARAM_INT)
                ),
            )
            ->execute()
            ->fetch();
        }

//fix added
    if (!$row) {
        return ''; // Return an empty string or a default value
    }
//fix added
        if($this->arguments['property'] === 'abstract'){
            return $row['abstract'];
        }
        else if($this->arguments['property'] === 'twitter_description'){
            return $row['twitter_description'];
        }
        else{
            return $row['subtitle'];
        }
    }
}
