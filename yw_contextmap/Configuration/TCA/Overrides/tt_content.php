<?php
defined('TYPO3') or die();

use TYPO3\CMS\Extbase\Utility\ExtensionUtility;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;

(static function() {
    // Register plugin
    ExtensionUtility::registerPlugin(
        'YwContextmap',
        'Viewer',
        'YW Context Map',
        'EXT:yw_contextmap/Resources/Public/Icons/ContentelementIcon.svg'
    );

    // Add FlexForm for optional settings (registerJs, custom JS paths)
    $pluginSignature = 'ywcontextmap_viewer';
    $GLOBALS['TCA']['tt_content']['types']['list']['subtypes_addlist'][$pluginSignature] = 'pi_flexform';
    ExtensionManagementUtility::addPiFlexFormValue(
        $pluginSignature,
        'FILE:EXT:yw_contextmap/Configuration/FlexForms/Viewer.xml'
    );
})();
