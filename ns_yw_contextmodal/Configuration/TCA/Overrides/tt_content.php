<?php
$tempContentColumns = [
    'modal_page' => [
        'displayCond' => 'FIELD:CType:=:modal',
        'label' => 'Modal Page',
        'config' => [
            'type' => 'group',
            'internal_type' => 'db',
            'allowed' => 'pages',
            'maxitems' => 1,
            'minitems' => 0,
            'size' => 1,
            'default' => 0,
            'suggestOptions' => [
                'default' => [
                    'additionalSearchFields' => 'nav_title, alias, url',
                    'addWhere' => 'AND pages.doktype = 1'
                ]
            ]
        ],
    ],
];
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTCAcolumns('tt_content',$tempContentColumns);
unset($tempContentColumns);
$GLOBALS['TCA']['tt_content']['palettes']['bsHeaderExtra'] = [
    'showitem' => 'tx_t3sbootstrap_header_display, tx_t3sbootstrap_header_position, --linebreak--,
  tx_t3sbootstrap_header_class, tx_t3sbootstrap_header_fontawesome,--linebreak--,modal_page'
];
