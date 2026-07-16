<?php

namespace Rfuehricht\Configloader\ViewHelpers;

use Rfuehricht\Configloader\Utility\ConfigurationUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3Fluid\Fluid\Core\Rendering\RenderingContextInterface;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;
use TYPO3Fluid\Fluid\Core\ViewHelper\Traits\CompileWithRenderStatic;

final class GetConfigViewHelper extends AbstractViewHelper
{

    public function initializeArguments(): void
    {
        $this->registerArgument(
            'key',
            'string',
            'The key in configuration file',
            true
        );
        $this->registerArgument(
            'alternative',
            'string',
            'Use this, if no value for this key ist set in config.'
        );
    }

    public function render()
    {
        return GeneralUtility::makeInstance(ConfigurationUtility::class)
            ->get($this->arguments['key'], ($this->arguments['alternative'] ?? ''));

    }
}
