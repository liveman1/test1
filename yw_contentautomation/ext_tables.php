<?php

defined('TYPO3') or die();

use TYPO3\CMS\Core\Imaging\IconProvider\SvgIconProvider;
use TYPO3\CMS\Core\Imaging\IconRegistry;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Utility\ExtensionUtility;

(static function() {
    ExtensionUtility::registerPlugin(
        'YwContentautomation',
        'LatestContent',
        'Latest Content'
    );

    ExtensionManagementUtility::addStaticFile(
        'yw_contentautomation',
        'Configuration/TypoScript',
        'Yw Content Automation'
    );

    GeneralUtility::makeInstance(IconRegistry::class)->registerIcon(
        'yw_contentautomation-plugin-latestcontent',
        SvgIconProvider::class,
        ['source' => 'EXT:yw_contentautomation/Resources/Public/Icons/LatestContent.svg']
    );
})();
