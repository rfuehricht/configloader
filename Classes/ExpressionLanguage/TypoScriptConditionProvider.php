<?php

namespace Rfuehricht\Configloader\ExpressionLanguage;

use TYPO3\CMS\Core\ExpressionLanguage\AbstractProvider;

/**
 * Provides custom TypoScript conditions.
 *
 */
class TypoScriptConditionProvider extends AbstractProvider
{
    public function __construct()
    {
        $this->expressionLanguageProviders = [
            TypoScriptConditionFunctionsProvider::class
        ];
    }
    
}
