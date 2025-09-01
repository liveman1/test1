<?php

declare(strict_types=1);

namespace Yummyworld\YwContentautomation\Controller;

use Psr\Http\Message\ResponseInterface;
use TYPO3\CMS\Core\Page\PageRenderer;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;
use TYPO3\CMS\Frontend\Page\PageRepository as Typo3PageRepository;
use Yummyworld\YwContentautomation\Domain\Repository\PageRepository;

/**
 * Controller for listing latest content pages.
 */
class LatestContentController extends ActionController
{
    public function __construct(
        private readonly PageRepository $pageRepository,
        private readonly PageRenderer $pageRenderer,
        private readonly Typo3PageRepository $typo3PageRepository,
    ) {
    }

    public function listAction(): ResponseInterface
    {
        $this->pageRenderer->addCssFile('EXT:yw_contentautomation/Resources/Public/css/swiper.css');
        $this->pageRenderer->addCssFile('EXT:yw_contentautomation/Resources/Public/css/latestcontent.css');
        $this->pageRenderer->addJsFooterFile('EXT:yw_contentautomation/Resources/Public/javascript/swiper-bundle.min.js');
        $this->pageRenderer->addJsFooterFile('EXT:yw_contentautomation/Resources/Public/javascript/latestcontent.js');

        $limit = (int)($this->settings['count'] ?? 10);
        $pages = $this->pageRepository->findYmPages($limit);

        $site = $this->request->getAttribute('site');
        $languages = $site ? $site->getLanguages() : [];

        foreach ($pages as &$page) {
            $translations = [];
            $languageLabels = [];
            foreach ($languages as $language) {
                $overlay = $this->typo3PageRepository->getPageOverlay($page, $language->getLanguageId());
                if (!empty($overlay) && ($overlay['title'] ?? '') !== '') {
                    $translations[$language->getLanguageId()] = [
                        'title' => $overlay['title'] ?? '',
                        'description' => $overlay['description'] ?? '',
                    ];
                    if ($language->getLanguageId() !== 0) {
                        $languageLabels[] = strtoupper($language->getTwoLetterIsoCode());
                    }
                }
            }
            $page['translations'] = $translations;
            $page['languageLabels'] = $languageLabels;
        }

        $this->view->assignMultiple([
            'pages' => $pages,
            'languages' => $languages,
        ]);

        return $this->htmlResponse();
    }
}
