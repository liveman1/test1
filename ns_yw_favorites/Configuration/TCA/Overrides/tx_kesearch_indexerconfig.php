<?php

defined('TYPO3_MODE') or die();

// $ll = 'LLL:EXT:news/Resources/Private/Language/locallang_db.xlf:';
// $configuration = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\GeorgRinger\News\Domain\Model\Dto\EmConfiguration::class);

/**
 * Change TCA configuration settings of extising columns of tx_kesearch_indexerconfig
 */
$newSysCategoryColumns = [
    'startingpoints_recursive' => [
    'exclude' => 0,
    'label' => 'LLL:EXT:ke_search/Resources/Private/Language/locallang_db.xlf:tx_kesearch_indexerconfig.startingpoints_recursive',
    'description' => 'LLL:EXT:ke_search/Resources/Private/Language/locallang_db.xlf:tx_kesearch_indexerconfig.startingpoints_recursive.description',
    'displayCond' => 'FIELD:type:IN:page,tt_content,tt_address,news,tt_news,specify_page',
    'config' => [
        'type' => 'group',
        'internal_type' => 'db',
        'allowed' => 'pages',
        'size' => 10,
        'minitems' => 0,
        'maxitems' => 99,
        ],
    ],
    'targetpid' => [
        'displayCond' => 'FIELD:type:!IN:page,tt_content,file,remote,specify_page',
        'exclude' => 0,
        'label' => 'LLL:EXT:ke_search/Resources/Private/Language/locallang_db.xlf:tx_kesearch_indexerconfig.targetpid',
        'config' => [
            'type' => 'group',
            'internal_type' => 'db',
            'allowed' => 'pages',
            'size' => 1,
            'minitems' => 1,
            'maxitems' => 1,
        ],
    ]
];

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTCAcolumns('tx_kesearch_indexerconfig', $newSysCategoryColumns);

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addToAllTCAtypes(
    'tx_kesearch_indexerconfig',
    'startingpoints_recursive',
    '',
    ''
);
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addToAllTCAtypes(
    'tx_kesearch_indexerconfig',
    'targetpid',
    '',
    ''
);
?>