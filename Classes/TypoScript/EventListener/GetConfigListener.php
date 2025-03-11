<?php

namespace Rfuehricht\Configloader\TypoScript\EventListener;

use Rfuehricht\Configloader\Utility\ConfigurationUtility;
use TYPO3\CMS\Core\Attribute\AsEventListener;
use TYPO3\CMS\Core\TypoScript\AST\Event\EvaluateModifierFunctionEvent;
use TYPO3\CMS\Core\Utility\DebugUtility;


/**
 * Replaces `getConfig(...)` strings with values from the global `CONFIG` object.
 * This config-object has been loaded with the package `hassankhan/config`.
 *
 */
#[AsEventListener(
    identifier: 'configloader/evaluate-getconfig-function',
)]
final class GetConfigListener
{
    public function __construct(
        protected ConfigurationUtility $configurationUtility
    )
    {

    }

    public function __invoke(EvaluateModifierFunctionEvent $event): void
    {
        if ($event->getFunctionName() === 'getConfig') {

            $functionArgument = $event->getFunctionArgument();
            $functionArgument = trim($functionArgument, "'");
            $value = $this->configurationUtility->get($functionArgument);

            $event->setValue($value);


        }
    }
}
