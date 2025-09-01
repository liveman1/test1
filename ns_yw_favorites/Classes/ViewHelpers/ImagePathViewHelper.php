<?php
namespace NITSAN\NsYwFavorites\ViewHelpers;

use Exception;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractTagBasedViewHelper;

class ImagePathViewHelper extends AbstractTagBasedViewHelper
{
    public function initializeArguments()
    {
        parent::initializeArguments();
        $this->registerUniversalTagAttributes();
        $this->registerArgument('uid', 'int', 'UID to fetch image path', true);
    }

    public function render(): string
    {
        $uid = (int)$this->arguments['uid'];
        if ($uid<=0) {
            return '';
        }

        $resourceFactory = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\TYPO3\CMS\Core\Resource\ResourceFactory::class);
        try {
            $file = $resourceFactory->getFileObject($uid);
            if($file) {
                if (file_exists($file->getPublicUrl())) {
                    return $file->getPublicUrl();
                } else {
                    return '';
                }
            }
        } catch (Exception $e) {}
        return '';
    }
}