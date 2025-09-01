<?php
// TYPO3 Security Check
if (!defined('TYPO3_MODE')) {
    die('Access denied.');
}
// Extension key
$_EXTKEY = 'ns_yw_favorites';

// Add default include static TypoScript (for root page)
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addStaticFile($_EXTKEY, 'Configuration/TypoScript', '[NITSAN] Custom Extension Container');

(static function() {
    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addLLrefForTCAdescr('tx_nsywfavorites_domain_model_test', 'EXT:ns_yw_favorites/Resources/Private/Language/locallang_csh_tx_nsywfavorites_domain_model_test.xlf');
    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::allowTableOnStandardPages('tx_nsywfavorites_domain_model_test');

    $renderer = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\TYPO3\CMS\Core\Page\PageRenderer::class);
    $renderer->setCharSet('utf-8');
    $renderer->addInlineLanguageLabelFile('EXT:ns_yw_favorites/Resources/Private/Language/locallang.xlf');
})();