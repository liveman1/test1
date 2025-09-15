<?php
defined('TYPO3') or die();

use TYPO3\CMS\Extbase\Utility\ExtensionUtility;
use Yummyworld\YwContextmap\Controller\ViewerController;

(static function() {
    ExtensionUtility::configurePlugin(
        'YwContextmap',
        'Viewer',
        [ViewerController::class => 'show'],
        []
    );
})();
