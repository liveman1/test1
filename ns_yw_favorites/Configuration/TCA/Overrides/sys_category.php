<?php

defined('TYPO3_MODE') or die();

// $ll = 'LLL:EXT:news/Resources/Private/Language/locallang_db.xlf:';
// $configuration = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\GeorgRinger\News\Domain\Model\Dto\EmConfiguration::class);

/**
 * Add extra fields to the sys_category record
 */
$newSysCategoryColumns = [
    'images' => [
        'exclude' => true,
        'label' => 'Image',
        'config' => \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::getFileFieldTCAConfig(
            'images',
            [
                'behaviour' => [
                    'allowLanguageSynchronization' => true,
                ],
                'appearance' => [
                    'createNewRelationLinkTitle' => 'LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:images.addFileReference',
                    'showPossibleLocalizationRecords' => true,
                    'showRemovedLocalizationRecords' => true,
                    'showAllLocalizationLink' => true,
                    'showSynchronizationLink' => true
                ],
                'foreign_match_fields' => [
                    'fieldname' => 'image',
                    'tablenames' => 'sys_category',
                    'table_local' => 'sys_file',
                ],
                'maxitems' => 1,
            ],
            $GLOBALS['TYPO3_CONF_VARS']['GFX']['imagefile_ext'] = 'jpg,png'
        )
    ],
];

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTCAcolumns('sys_category', $newSysCategoryColumns);
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addToAllTCAtypes(
    'sys_category',
    'images',
    '',
    'after:title'
);

// $GLOBALS['TCA']['sys_category']['columns']['items']['config']['MM_oppositeUsage']['tx_news_domain_model_news']
//     = [0 => 'categories'];

// $GLOBALS['TCA']['sys_category']['ctrl']['label_userFunc'] =
//     \GeorgRinger\News\Hooks\Labels::class . '->getUserLabelCategory';

?>