<?php

namespace Rfuehricht\Configloader\ViewHelpers;

use Rfuehricht\Configloader\Utility\ConfigurationUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3Fluid\Fluid\Core\Rendering\RenderingContextInterface;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;
use TYPO3Fluid\Fluid\Core\ViewHelper\Traits\CompileWithRenderStatic;

final class GetConfigViewHelper extends AbstractViewHelper
{
    use CompileWithRenderStatic;
    
    public function initializeArguments()
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

    public static function renderStatic(
        array                     $arguments,
        \Closure                  $renderChildrenClosure,
        RenderingContextInterface $renderingContext
    )
    {

        return GeneralUtility::makeInstance(ConfigurationUtility::class)
            ->get($arguments['key'], ($arguments['alternative'] ?? ''));

    }
}