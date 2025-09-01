<?php
// TYPO3 Security Check
if (!defined('TYPO3_MODE')) {
    die('Access denied.');
}


\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPageTSConfig('@import "EXT:ns_yw_favorites/Configuration/PageTSconfig/ContentElementWizard.tsconfig"');


// $GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['ke_search']['registerIndexerConfiguration'][] = \NITSAN\NsYwFavorites\Hooks\NewIndexer::class;
// $GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['ke_search']['customIndexer'][] = \NITSAN\NsYwFavorites\Hooks\NewIndexer::class;


// $GLOBALS['TYPO3_CONF_VARS']['SYS']['Objects'][Tpwd\KeSearch\Lib\Db::class] = ['className' => \NITSAN\NsYwFavorites\Xclass\Db::class];
$GLOBALS['TYPO3_CONF_VARS']['SYS']['fluid']['namespaces']['NsYwFavorites'] = [
    'NITSAN\NsYwFavorites\ViewHelpers',
];

(static function() {
    \TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
        'NsYwFavorites',
        'Pi1',
        [
            \NITSAN\NsYwFavorites\Controller\AddtofavouriteController::class => 'list, create, edit, update, delete, addpagetolist, duplicateList'
        ],
        // non-cacheable actions
        [
            \NITSAN\NsYwFavorites\Controller\AddtofavouriteController::class => 'list, create, edit, update, delete, addpagetolist, duplicateList'
        ]
    );
    \TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
        'NsYwFavorites',
        'Pi2',
        [
            \NITSAN\NsYwFavorites\Controller\FavouriteController::class => 'list, slider, create, edit, update, share, delete, addpagetolist, duplicateList, deletePage, rightPage, crypticurl, unfollow'
        ],
        // non-cacheable actions
        [
            \NITSAN\NsYwFavorites\Controller\FavouriteController::class => 'list, slider, create, edit, update, share, delete, addpagetolist, duplicateList, deletePage, rightPage, crypticurl, unfollow'
        ]
    );
    \TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
        'NsYwFavorites',
        'Pi3',
        [
            \NITSAN\NsYwFavorites\Controller\FavouriteController::class => 'sharedList, crypticurl, share'
        ],
        // non-cacheable actions
        [
            \NITSAN\NsYwFavorites\Controller\FavouriteController::class => 'sharedList, crypticurl, share'
        ]
    );
})();
