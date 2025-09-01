<?php

defined('TYPO3') or die();

use TYPO3\CMS\Extbase\Utility\ExtensionUtility;
use Yummyworld\YwContentautomation\Controller\LatestContentController;

(static function() {
    // Register the plugin with the controller's class name to avoid runtime TypeErrors
    ExtensionUtility::configurePlugin(
        'YwContentautomation',
        'LatestContent',
        [
            LatestContentController::class => 'list',
        ],

        [],
        ExtensionUtility::PLUGIN_TYPE_CONTENT_ELEMENT
    );
})();
