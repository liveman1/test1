<?php

defined('TYPO3') or die();

use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;

(static function() {
    $pluginSignature = 'ywcontentautomation_latestcontent';

    ExtensionManagementUtility::addTcaSelectItem(
        'tt_content',
        'list_type',
        [
            'Latest Content',
            $pluginSignature,
            'yw_contentautomation-plugin-latestcontent'
        ]
    );

    $GLOBALS['TCA']['tt_content']['types']['list']['subtypes_excludelist'][$pluginSignature] = 'recursive,select_key';
    $GLOBALS['TCA']['tt_content']['types']['list']['subtypes_addlist'][$pluginSignature] = 'pi_flexform';
    ExtensionManagementUtility::addPiFlexFormValue(
        $pluginSignature,
        'FILE:EXT:yw_contentautomation/Configuration/FlexForms/latestcontent.xml'
    );
    $GLOBALS['TCA']['tt_content']['ctrl']['typeicon_classes']['list-' . $pluginSignature] = 'yw_contentautomation-plugin-latestcontent';
})();
