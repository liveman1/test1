<?php

defined('TYPO3_MODE') or die();

// $ll = 'LLL:EXT:news/Resources/Private/Language/locallang_db.xlf:';
// $configuration = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\GeorgRinger\News\Domain\Model\Dto\EmConfiguration::class);

/**
 * Change TCA configuration settings of extising columns of tx_kesearch_filteroptions
 */
$newSysCategoryColumns = [
    'category_image' => [
        'exclude' => true,
        'label' => 'LLL:EXT:ns_yw_search/Resources/Private/Language/BackendLayouts/locallang.xlf:categoryImage',
        'config' => \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::getFileFieldTCAConfig(
            'category_image',
            [
                'appearance' => [
                    'createNewRelationLinkTitle' => 'LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:images.addFileReference'
                ],
                'overrideChildTca' => [
                    'types' => [
                        '0' => [
                            'showitem' => '
                            --palette--;LLL:EXT:lang/locallang_tca.xlf:sys_file_reference.imageoverlayPalette;imageoverlayPalette,
                            --palette--;;filePalette'
                        ],
                        \TYPO3\CMS\Core\Resource\File::FILETYPE_TEXT => [
                            'showitem' => '
                            --palette--;LLL:EXT:lang/locallang_tca.xlf:sys_file_reference.imageoverlayPalette;imageoverlayPalette,
                            --palette--;;filePalette'
                        ],
                        \TYPO3\CMS\Core\Resource\File::FILETYPE_IMAGE => [
                            'showitem' => '
                            --palette--;;imageoverlayPalette,
                            --palette--;;filePalette',
                        ],
                        \TYPO3\CMS\Core\Resource\File::FILETYPE_AUDIO => [
                            'showitem' => '
                            --palette--;LLL:EXT:lang/locallang_tca.xlf:sys_file_reference.imageoverlayPalette;imageoverlayPalette,
                            --palette--;;filePalette'
                        ],
                        \TYPO3\CMS\Core\Resource\File::FILETYPE_VIDEO => [
                            'showitem' => '
                            --palette--;LLL:EXT:lang/locallang_tca.xlf:sys_file_reference.imageoverlayPalette;imageoverlayPalette,
                            --palette--;;filePalette'
                        ],
                        \TYPO3\CMS\Core\Resource\File::FILETYPE_APPLICATION => [
                            'showitem' => '
                            --palette--;LLL:EXT:lang/locallang_tca.xlf:sys_file_reference.imageoverlayPalette;imageoverlayPalette,
                            --palette--;;filePalette'
                        ]
                    ],
                ],
                'foreign_match_fields' => [
                    'fieldname' => 'category_image',
                    'tablenames' => 'tx_nsproducts_domain_model_manufacturers',
                    'table_local' => 'sys_file',
                ],
                'maxitems' => 1
            ],
            $GLOBALS['TYPO3_CONF_VARS']['GFX']['imagefile_ext']
        ),
    ],
    'isParent' => [
        'exclude' => 1,
        'label' => 'Is Parent',
        'config' => [
            'type' => 'check',
            'default' => '0',
        ],
    ],
    'image' => [
        'exclude' => 0,
        'label' => 'Filter Image',
        'config' => [
            'type' => 'input',
            'size' => '245',
        ],
    ],
];

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTCAcolumns('tx_kesearch_filteroptions', $newSysCategoryColumns);

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addToAllTCAtypes(
    'tx_kesearch_filteroptions',
    'isParent',
    '',
    ''
);
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addToAllTCAtypes(
    'tx_kesearch_filteroptions',
    'image',
    '',
    ''
);
?>