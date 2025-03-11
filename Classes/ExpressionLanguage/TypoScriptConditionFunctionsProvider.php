<?php

namespace Rfuehricht\Configloader\ExpressionLanguage;

use Rfuehricht\Configloader\Utility\ConfigurationUtility;
use Symfony\Component\ExpressionLanguage\ExpressionFunction;
use Symfony\Component\ExpressionLanguage\ExpressionFunctionProviderInterface;

/**
 * Add custom conditions to the TypoScript condition provider.
 *
 * Provides the following functions:
 * - [getConfig('site.pages.root')]: Gets the value of the key `sites.pages.root` from the global `CONFIG` object
 *
 */
class TypoScriptConditionFunctionsProvider implements ExpressionFunctionProviderInterface
{

    public function __construct(
        protected ConfigurationUtility $configurationUtility
    )
    {

    }

    public function getFunctions(): array
    {
        return [
            $this->getConfigFunction()
        ];
    }


    /**
     * Replaces `getConfig(...)` strings with values from the global `CONFIG` object. This config-object has been loaded with the package `hassankhan/config`.
     *
     * @return ExpressionFunction
     */
    protected function getConfigFunction(): ExpressionFunction
    {
        return new ExpressionFunction(
            'getConfig',
            function () {
                // Not implemented, we only use the evaluator
            },
            function (array $arguments, string $value = '') {
                return $this->configurationUtility->get($value);
            }
        );
    }
}
