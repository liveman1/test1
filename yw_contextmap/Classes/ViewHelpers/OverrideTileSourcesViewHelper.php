<?php
declare(strict_types=1);

namespace Yummyworld\YwContextmap\ViewHelpers;

use TYPO3Fluid\Fluid\Core\Rendering\RenderingContextInterface;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;

final class OverrideTileSourcesViewHelper extends AbstractViewHelper
{
    public function initializeArguments(): void
    {
        $this->registerArgument('content', 'string', 'Original viewer config JS', true);
        $this->registerArgument('tileSource', 'string', 'DZI path to inject', false, '');
        $this->registerArgument('tileOpacity', 'mixed', 'Opacity (0..1)', false, 1);
    }

    public static function renderStatic(
        array $arguments,
        \Closure $renderChildrenClosure,
        RenderingContextInterface $renderingContext
    ): string {
        $content = (string)($arguments['content'] ?? '');
        $tileSource = trim((string)($arguments['tileSource'] ?? ''));
        if ($tileSource === '') {
            return $content; // nothing to override
        }

        $opacityRaw = $arguments['tileOpacity'];
        $opacity = is_numeric($opacityRaw) ? (string)$opacityRaw : '1';

        // Build replacement
        $tsEsc = addslashes($tileSource);
        $replacement = 'tileSources: [{tileSource: "' . $tsEsc . '", opacity: ' . $opacity . '}]';

        // Replace the first tileSources: [ ... ] block (multiline-safe)
        $pattern = '/tileSources\s*:\s*\[[\s\S]*?\]/';
        $out = preg_replace($pattern, $replacement, $content, 1);

        // Fallback: if not found, inject right after id: "os-rez13",
        if ($out === null || $out === $content) {
            $pattern2 = '/(id\s*:\s*"os-rez13"\s*,)/';
            $out2 = preg_replace($pattern2, '$1' . "\n          " . $replacement . ',', $content, 1);
            if ($out2 !== null) {
                return $out2;
            }
        }
        return $out ?? $content;
    }
}

