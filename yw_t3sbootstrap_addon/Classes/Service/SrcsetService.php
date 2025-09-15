<?php

namespace YwExtensions\YwT3sbootstrapAddon\Service;

use TYPO3\CMS\Core\Resource\FileReference;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Service\ImageService;

/**
 * Generates srcset strings for various breakpoints from a FAL FileReference.
 */
class SrcsetService
{
    /**
     * Create srcset strings and a 500px fallback image.
     *
     * @param FileReference $file
     * @return array<string,string>
     */
    public function generate(FileReference $file): array
    {
        $imageService = GeneralUtility::makeInstance(ImageService::class);

        $definitions = [
            'lg' => [125, 255, 385, 576],
            'md' => [125, 255, 385, 576],
            'sm' => [60, 100, 200, 385, 575],
        ];

        $result = [];

        foreach ($definitions as $key => $widths) {
            $parts = [];
            foreach ($widths as $width) {
                try {
                    $processed = $imageService->applyProcessingInstructions($file, ['width' => $width]);
                    $uri = $imageService->getImageUri($processed);
                    $parts[] = $uri . ' ' . $width . 'w';
                } catch (\Exception $e) {
                    // silently skip missing images
                }
            }
            if (!empty($parts)) {
                $result[$key] = implode(', ', $parts);
            }
        }

        try {
            $fallbackProcessed = $imageService->applyProcessingInstructions($file, ['width' => 500]);
            $result['fallback'] = $imageService->getImageUri($fallbackProcessed);
        } catch (\Exception $e) {
            $result['fallback'] = $imageService->getImageUri($file);
        }

        return $result;
    }
}

