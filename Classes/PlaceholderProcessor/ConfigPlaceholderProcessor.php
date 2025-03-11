<?php

namespace Rfuehricht\Configloader\PlaceholderProcessor;

use Rfuehricht\Configloader\Utility\ConfigurationUtility;
use TYPO3\CMS\Core\Configuration\Processor\Placeholder\PlaceholderProcessorInterface;

/**
 * Replace `%config(...)%` strings with values from the global `CONFIG` object.
 * This config-object has been loaded with the package `hassankhan/config.
 */
class ConfigPlaceholderProcessor implements PlaceholderProcessorInterface
{

    public function __construct(
        protected ConfigurationUtility $configurationUtility
    )
    {

    }

    public function canProcess(string $placeholder, array $referenceArray): bool
    {
        return str_contains($placeholder, '%getConfig(');
    }


    public function process(string $value, array $referenceArray)
    {
        return $this->configurationUtility->get($value);
    }
}
