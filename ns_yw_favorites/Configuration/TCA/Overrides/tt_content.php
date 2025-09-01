<?php
defined('TYPO3') || die();

\TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerPlugin(
    'NsYwFavorites',
    'Pi1',
    'addtofavourite'
);
\TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerPlugin(
    'NsYwFavorites',
    'Pi2',
    'favourite'
);
\TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerPlugin(
    'NsYwFavorites',
    'Pi3',
    'Shared List'
);
$GLOBALS['TCA']['tt_content']['types']['list']['subtypes_excludelist']['nsywfavorites_pi3'] = 'pages,layout,select_key,recursive';

$GLOBALS['TCA']['tt_content']['types']['list']['subtypes_addlist']['nsywfavorites_pi3'] = 'pi_flexform';

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPiFlexFormValue(
    'nsywfavorites_pi3',
    'FILE:EXT:ns_yw_favorites/Configuration/FlexForms/SharedList.xml'
);
