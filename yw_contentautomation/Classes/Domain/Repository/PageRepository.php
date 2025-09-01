<?php

declare(strict_types=1);

namespace Yummyworld\YwContentautomation\Domain\Repository;

use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Repository for querying page records.
 */
class PageRepository
{
    private const TABLE = 'pages';

    /**
     * Fetch pages where title starts with "ym-".
     *
     * @param int $limit
     * @return array
     */
    public function findYmPages(int $limit): array
    {
        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)
            ->getQueryBuilderForTable(self::TABLE);

        return $queryBuilder
            ->select('uid', 'title', 'description', 'SYS_LASTCHANGED')
            ->from(self::TABLE)
            ->where(
                $queryBuilder->expr()->like(
                    'title',
                    $queryBuilder->createNamedParameter('ym-%')
                )
            )
            ->orderBy('SYS_LASTCHANGED', 'DESC')
            ->setMaxResults($limit)
            ->executeQuery()
            ->fetchAllAssociative();
    }
}
