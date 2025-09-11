<?php
namespace YwExtensions\YwT3sbootstrapAddon\DataProcessing;

use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;
use TYPO3\CMS\Frontend\ContentObject\DataProcessorInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Domain\Repository\PageRepository;
use TYPO3\CMS\Core\Resource\FileRepository;

class PageCardsProcessor implements DataProcessorInterface
{
    public function process(ContentObjectRenderer $cObj, array $contentObjectConfiguration, array $processorConfiguration, array $processedData)
    {
        $uidList = $cObj->data['pages'] ?? '';
        $pages = [];
        if (!empty($uidList)) {
            $uids = GeneralUtility::intExplode(',', $uidList, true);
            /** @var PageRepository $pageRepository */
            $pageRepository = GeneralUtility::makeInstance(PageRepository::class);
            /** @var FileRepository $fileRepository */
            $fileRepository = GeneralUtility::makeInstance(FileRepository::class);
            foreach ($uids as $uid) {
                $page = $pageRepository->getPage($uid);
                if (!empty($page)) {
                    $images = $fileRepository->findByRelation('pages', 'twitter_image', $uid);
                    $pages[] = [
                        'uid' => $uid,
                        'title' => $page['subtitle'] ?? '',
                        'linkTitle' => $page['nav_title'] ?? '',
                        'description' => $page['description'] ?? '',
                        'images' => $images,
                    ];
                }
            }
        }
        $processedData['pages'] = $pages;
        return $processedData;
    }
}
