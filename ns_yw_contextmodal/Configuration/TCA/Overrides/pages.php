<?php
if (!defined('TYPO3_MODE')) {
    die('Access denied.');
}

$tempPageColumns = [
    'yw_a' => [
        'label' => 'YW A',
        'exclude' => true,
        'config' => [
            'type' => 'input',
            'size' => '40',
            'eval' => 'trim',
            'behaviour' => [
                'allowLanguageSynchronization' => true,
            ],
        ],
    ],
    'yw_b' => [
        'label' => 'YW B',
        'exclude' => true,
        'config' => [
            'type' => 'input',
            'size' => '40',
            'eval' => 'trim',
            'behaviour' => [
                'allowLanguageSynchronization' => true,
            ],
        ],
    ],
    'yw_c' => [
        'label' => 'YW C',
        'exclude' => true,
        'config' => [
            'type' => 'input',
            'size' => '40',
            'eval' => 'trim',
            'behaviour' => [
                'allowLanguageSynchronization' => true,
            ],
        ],
    ],
    'yw_d' => [
        'label' => 'YW D',
        'exclude' => true,
        'config' => [
            'type' => 'input',
            'size' => '40',
            'eval' => 'trim',
            'behaviour' => [
                'allowLanguageSynchronization' => true,
            ],
        ],
    ],
];
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTCAcolumns('pages', $tempPageColumns);
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addToAllTCAtypes(
    'pages','--div--;YW, yw_a, yw_b, yw_c, yw_d'
);
