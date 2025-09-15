<?php
defined('TYPO3') || die();

use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;

call_user_func(function () {

    // 1) Register the CType in the "Type" dropdown
    ExtensionManagementUtility::addTcaSelectItem(
        'tt_content',
        'CType',
        [
            'YW: BS Carousel (pages)',
            'yw_bscarousel',                // <-- use this key consistently
            'content-carousel'
        ]
    );

    // 2) Add the custom field to pick & order pages
    ExtensionManagementUtility::addTCAcolumns('tt_content', [
        'tx_yw_selected_pages' => [
            'exclude' => 1,
            'label'   => 'Selected pages (slide order)',
            'config'  => [
                'type' => 'select',
                'renderType' => 'selectMultipleSideBySide',
                'foreign_table' => 'pages',
                'MM' => 'tx_yw_bs_carousel_pages_mm',
                'size' => 10,
                'maxitems' => 999,
                'enableMultiSelectFilterTextfield' => true,
            ],
        ],
        'tx_yw_show_latest' => [
            'exclude' => true,
            'label'   => 'Show latest pages',
            'config'  => [
                'type' => 'check',
                'items' => [
                    ['Enable', ''],
                ],
                'default' => 0,
                'renderType' => 'checkboxToggle',
            ],
        ],
        'tx_yw_latest_limit' => [
            'exclude' => true,
            'label'   => 'Number of pages',
            'config'  => [
                'type' => 'input',
                'eval' => 'int',
                'size' => 5,
                'default' => 5,
                'range' => [
                    'lower' => 1,
                    'upper' => 100
                ],
            ],
        ],
    ]);

    // 3) Define the backend form for this CType
    $GLOBALS['TCA']['tt_content']['types']['yw_bscarousel'] = [
        'showitem' => '
            --palette--;;general,
            header;Carousel title,
            tx_yw_selected_pages,
	    tx_yw_show_latest,
	    tx_yw_latest_limit,
            --div--;Appearance,layout,frame_class,space_before_class,space_after_class,
            --div--;Access,hidden,starttime,endtime
        ',
    ];
});
