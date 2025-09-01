<?php
namespace NITSAN\NsYwSearch\ViewHelpers;

use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractTagBasedViewHelper;
use Doctrine\DBAL\DBALException;
use Doctrine\DBAL\FetchMode;
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
    }

    public function render()
    {
        $uid = (int)$this->arguments['uid'];
        if ($uid<=0) {
            return false;
        }
        $languageAspect = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\TYPO3\CMS\Core\Context\Context::class)->getAspect('language');
        $sys_language_uid = $languageAspect->getId();
        /** @var QueryBuilder $queryBuilder */
        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)
            ->getQueryBuilderForTable('pages');
        if($sys_language_uid > 0){
            $row =  $queryBuilder
            ->select('subtitle','abstract')
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
            $row =  $queryBuilder
            ->select('subtitle','abstract')
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
        
        return $row['subtitle'];
    }
}