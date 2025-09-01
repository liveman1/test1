<?php
// TYPO3 Security Check
if (!defined('TYPO3_MODE')) {
    die('Access denied.');
}


\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPageTSConfig('@import "EXT:ns_yw_search/Configuration/TypoScript/setup.typoscript"');


// $GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['ke_search']['registerIndexerConfiguration'][] = \NITSAN\NsYwSearch\Hooks\NewIndexer::class;
$GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['ke_search']['customIndexer'][] = \NITSAN\NsYwSearch\Hooks\NewIndexer::class;
// $GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['ke_search']['additionalResultMarker'][] = \NITSAN\NsYwSearch\Hooks\AdditionalResultMarker::class;


// $GLOBALS['TYPO3_CONF_VARS']['SYS']['Objects'][Tpwd\KeSearch\Lib\Db::class] = ['className' => \NITSAN\NsYwSearch\Xclass\Db::class];
$GLOBALS['TYPO3_CONF_VARS']['SYS']['fluid']['namespaces']['NsYwSearch'] = [
    'NITSAN\NsYwSearch\ViewHelpers',
];
