<?php
defined('TYPO3') or die();

use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;

ExtensionManagementUtility::addPlugin(
    [
        'LLL:EXT:yw_t3sbootstrap_addon/Resources/Private/Language/locallang_be.xlf:page_swiper.title',
        'page_swiper',
        'content-carousel-item-textandimage'
    ],
    'CType',
    'yw_t3sbootstrap_addon'
);

// Add new content element to new content element wizard
ExtensionManagementUtility::addPageTSConfig(
    "@import 'EXT:yw_t3sbootstrap_addon/Configuration/TSConfig/NewContentElement.tsconfig'"
);
