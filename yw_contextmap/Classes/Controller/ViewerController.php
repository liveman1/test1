<?php
declare(strict_types=1);

namespace Yummyworld\YwContextmap\Controller;

use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;
use TYPO3\CMS\Core\Page\PageRenderer;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class ViewerController extends ActionController
{
    public function showAction(): void
    {
        // Optionally register core JS via PageRenderer if enabled in settings
        $settings = $this->settings ?? [];
        $registerJs = (bool)($settings['registerJs'] ?? true);

        if ($registerJs) {
            /** @var PageRenderer $pageRenderer */
            $pageRenderer = GeneralUtility::makeInstance(PageRenderer::class);

            $files = [
                'ymOsHref'      => $settings['js']['ymOsHref'] ?? 'EXT:yw_contextmap/Resources/Public/JavaScript/os/ym-os-href.min.js',
                'ymOsBookmark'  => $settings['js']['ymOsBookmark'] ?? 'EXT:yw_contextmap/Resources/Public/JavaScript/os-url/ym-os-bookmark-url.min.js',
                'pieCmImages'   => $settings['js']['pieCmImages'] ?? 'EXT:yw_contextmap/Resources/Public/JavaScript/pie-context-menu/pie-cm-images.min.js',
                'ymOsHtmlOverlay'=> $settings['js']['ymOsHtmlOverlay'] ?? 'EXT:yw_contextmap/Resources/Public/JavaScript/os-over/ym_os-html-overlay.min.js',
            ];

            // HEADER: must run before inline viewer config
            if (!empty($files['ymOsHref'])) {
                // addJsLibrary(name, file, type, compress, forceOnTop, allWrap, excludeFromConcatenation, splitChar)
                $pageRenderer->addJsLibrary('yw_contextmap_ymOsHref', $files['ymOsHref'], 'text/javascript', true, true);
                unset($files['ymOsHref']);
            }
            if (!empty($files['ymOsBookmark'])) {
                $pageRenderer->addJsLibrary('yw_contextmap_ymOsBookmark', $files['ymOsBookmark'], 'text/javascript', true, true);
                unset($files['ymOsBookmark']);
            }

            // FOOTER: the rest can be deferred to the footer
            foreach ($files as $key => $file) {
                $pageRenderer->addJsFooterLibrary('yw_contextmap_' . $key, $file);
            }
        }

        // Nothing to assign; Fluid template pulls in TypoScript libs
    }
}

