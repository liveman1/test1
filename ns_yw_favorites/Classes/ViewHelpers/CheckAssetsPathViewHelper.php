<?php

namespace NITSAN\NsYwFavorites\ViewHelpers;

use TYPO3\CMS\Core\Core\Environment;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractTagBasedViewHelper;

class CheckAssetsPathViewHelper extends AbstractTagBasedViewHelper
{

    /**
     * Initialize arguments
     */
    public function initializeArguments()
    {
        parent::initializeArguments();
        $this->registerArgument('path', 'string', 'Image Path', true);
    }

    /**
     * Render the image tag
     *
     * @return bool Rendered tag
     */
    public function render(): bool
    {
        $path = $this->arguments['path'];
        $publicPath = Environment::getPublicPath();

        return file_exists($publicPath.'/'.$path);
    }
}
