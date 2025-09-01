<?php

declare(strict_types=1);

namespace NITSAN\NsYwFavorites\ViewHelpers;

use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractTagBasedViewHelper;
use PDO;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Database\Query\QueryBuilder;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class GetLanguageContentViewHelper extends AbstractTagBasedViewHelper
{
    public function initializeArguments()
    {
        parent::initializeArguments();
        $this->registerUniversalTagAttributes();
        $this->registerArgument('uid', 'int', 'UID to fetch subtitle', true);
        $this->registerArgument('property', 'string', 'Identifier of property to fetch data', false, 'subtitle');
        $this->registerArgument('currentpage', 'array', 'Identifier of property to fetch data', true);

    }

    public function render()
    {
        $uid = (int)$this->arguments['uid'];
        if ($uid <= 0) {
            return false;
        }

        $languageAspect = GeneralUtility::makeInstance(\TYPO3\CMS\Core\Context\Context::class)->getAspect('language');
        $currentLanguage = $languageAspect->getId();
        $data = $this->arguments['currentpage'];

        /** @var QueryBuilder $queryBuilder */
        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)
            ->getQueryBuilderForTable('pages');

        if($this->arguments['property'] === 'assigned_article'){
            if ($currentLanguage > 0) {
                $row = $queryBuilder
                ->select('uid')
                ->from('pages')
                ->where(
                    $queryBuilder->expr()->eq('l10n_parent', $queryBuilder->createNamedParameter($data['uid'], PDO::PARAM_INT))
                )
                ->andWhere(
                    $queryBuilder->expr()->eq('sys_language_uid', $queryBuilder->createNamedParameter($currentLanguage, PDO::PARAM_INT))
                )
                ->execute()
                ->fetch();

                return $row['uid'];
            }
            else{
                return $data['uid'];
            }
        }else{
            return $this->fetchRow($queryBuilder, $uid, $data, $currentLanguage);
        }
    }

    private function fetchRow($queryBuilder, $uid, $data, $currentLanguage)
    {
        if ($currentLanguage > 0) {
            $row = $queryBuilder
            ->select('subtitle', 'abstract', 'nav_title')
            ->from('pages')
            ->where(
                $queryBuilder->expr()->eq('l10n_parent', $queryBuilder->createNamedParameter($data['uid'], PDO::PARAM_INT))
            )
            ->andWhere(
                $queryBuilder->expr()->eq('sys_language_uid', $queryBuilder->createNamedParameter($currentLanguage, PDO::PARAM_INT))
            )
            ->execute()
            ->fetch();

            // Get Fallback Value
            if($this->arguments['property'] === 'nav_title'){
                if($row['nav_title'] == ''){
                    $navTitle =  $queryBuilder
                    ->select('nav_title')
                    ->from('pages')
                    ->where(
                        $queryBuilder->expr()->eq(
                            'uid',
                            $queryBuilder->createNamedParameter($data['uid'], PDO::PARAM_INT)
                        ),                
                    )
                    ->execute()
                    ->fetch();
                    return $navTitle['nav_title'];
                }
                else{
                    return $row['nav_title'];
                }
            }
            if($this->arguments['property'] === 'abstract'){
                if($row['abstract'] == ''){
                    $abstract =  $queryBuilder
                    ->select('abstract')
                    ->from('pages')
                    ->where(
                        $queryBuilder->expr()->eq(
                            'uid',
                            $queryBuilder->createNamedParameter($data['uid'], PDO::PARAM_INT)
                        ),                
                    )
                    ->execute()
                    ->fetch();
                    return $abstract['abstract'];
                }
                else{
                    return $row['abstract'];
                }
            }
            if($this->arguments['property'] === 'subtitle'){
                if($row['subtitle'] == ''){
                    $subtitle =  $queryBuilder
                    ->select('subtitle')
                    ->from('pages')
                    ->where(
                        $queryBuilder->expr()->eq(
                            'uid',
                            $queryBuilder->createNamedParameter($data['uid'], PDO::PARAM_INT)
                        ),                
                    )
                    ->execute()
                    ->fetch();
                    return $subtitle['subtitle'];
                }
                else{
                    return $row['subtitle'];
                }
            }
        } else {
            $row = $queryBuilder
                ->select('subtitle', 'abstract', 'nav_title')
                ->from('pages')
                ->where(
                    $queryBuilder->expr()->eq('uid', $queryBuilder->createNamedParameter($data['uid'], PDO::PARAM_INT))
                )
                ->execute()
                ->fetch();
            if($this->arguments['property'] === 'nav_title'){
                return $row['nav_title'];
            }
            if($this->arguments['property'] === 'abstract'){
                return $row['abstract'];
            }
            if($this->arguments['property'] === 'subtitle'){
                return $row['subtitle'];
            }
        }
    }
}
