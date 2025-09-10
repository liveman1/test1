<?php
defined('TYPO3') or die();

use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;

// Add CType item
ExtensionManagementUtility::addTcaSelectItem(
    'tt_content',
    'CType',
    [
        'LLL:EXT:yw_t3sbootstrap_addon/Resources/Private/Language/locallang_be.xlf:yw_bscarousel.title',
        'yw_bscarousel',
        'content-carousel-item-textandimage'
    ],
    't3sbs_carousel',
    'after'
);

ExtensionManagementUtility::addPiFlexFormValue(
    '*',
    'FILE:EXT:yw_t3sbootstrap_addon/Configuration/FlexForms/YwBsCarousel.xml',
    'yw_bscarousel'
);

// Configure TCA for new type
$GLOBALS['TCA']['tt_content']['types']['yw_bscarousel'] = [
    'showitem' => '
        --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:general,
            --palette--;LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:palette.general;general,
            pages;LLL:EXT:yw_t3sbootstrap_addon/Resources/Private/Language/locallang_be.xlf:yw_bscarousel.pages,
            pi_flexform,
        --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:access,
            --palette--;LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:palette.hidden;hidden,
            --palette--;LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:palette.access;access,
    ',
];

// Add CType for Page Swiper
ExtensionManagementUtility::addTcaSelectItem(
    'tt_content',
    'CType',
    [
        'LLL:EXT:yw_t3sbootstrap_addon/Resources/Private/Language/locallang_be.xlf:page_swiper.title',
        'page_swiper',
        'content-carousel-item-textandimage'
    ],
    't3sbs_carousel',
    'after'
);

// Configure TCA for Page Swiper element
$GLOBALS['TCA']['tt_content']['types']['page_swiper'] = [
    'showitem' => '
        --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:general,
            --palette--;LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:palette.general;general,
        --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:access,
            --palette--;LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:palette.hidden;hidden,
            --palette--;LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:palette.access;access,
    ',
];
