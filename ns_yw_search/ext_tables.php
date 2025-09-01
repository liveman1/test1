<?php
// TYPO3 Security Check
if (!defined('TYPO3_MODE')) {
    die('Access denied.');
}
// Extension key
$_EXTKEY = 'ns_yw_search';

// Add default include static TypoScript (for root page)
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addStaticFile($_EXTKEY, 'Configuration/TypoScript', '[NITSAN] Extended Index Search');

