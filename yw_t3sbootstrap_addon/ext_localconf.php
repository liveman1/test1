<?php
defined('TYPO3') || die();

use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;

call_user_func(function () {
    // constants (optional but recommended if you created constants.typoscript)
    ExtensionManagementUtility::addTypoScriptConstants(
        "@import 'EXT:yw_t3sbootstrap_addon/Configuration/TypoScript/constants.typoscript'"
    );

    // setup (must include!)
    ExtensionManagementUtility::addTypoScriptSetup(
        "@import 'EXT:yw_t3sbootstrap_addon/Configuration/TypoScript/setup.typoscript'"
    );

    // (Optional but nice) Wizard tile – ensures the NCE wizard picks it up with the correct CType
    ExtensionManagementUtility::addPageTSConfig("
mod.wizards.newContentElement.wizardItems.common.elements.yw_bscarousel {
  iconIdentifier = content-carousel
  title = YW: BS Carousel (pages)
  description = Carousel fed by selected pages
  tt_content_defValues {
    CType = yw_bscarousel
  }
}
mod.wizards.newContentElement.wizardItems.common.show := addToList(yw_bscarousel)
");
});
