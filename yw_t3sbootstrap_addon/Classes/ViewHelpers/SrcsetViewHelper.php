<?php

namespace YwExtensions\YwT3sbootstrapAddon\ViewHelpers;

use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;
use TYPO3\CMS\Core\Resource\FileReference;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use YwExtensions\YwT3sbootstrapAddon\Service\SrcsetService;

/**
 * Fluid ViewHelper returning srcset strings for a FAL FileReference.
 */
class SrcsetViewHelper extends AbstractViewHelper
{
    /**
     * {@inheritdoc}
     */
    public function initializeArguments(): void
    {
        $this->registerArgument('file', FileReference::class, 'FAL file reference', true);
    }

    /**
     * @return array<string,string>
     */
    public function render(): array
    {
        /** @var SrcsetService $service */
        $service = GeneralUtility::makeInstance(SrcsetService::class);
        return $service->generate($this->arguments['file']);
    }
}

