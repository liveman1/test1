<?php

defined('TYPO3_MODE') or die();

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

?>