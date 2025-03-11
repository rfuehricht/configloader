<?php

defined('TYPO3') or die();

\TYPO3\CMS\Core\Utility\DebugUtility::debug("sdf");
$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_tcemain.php']['clearCachePostProc']['configloader'] = \Rfuehricht\Configloader\Hooks\DataHandlerClearCachePostProcHook::class . '->clearConfigurationCache';

// Parses config-references provided by the loaded configuration files
$GLOBALS['TYPO3_CONF_VARS']['SYS']['yamlLoader']['placeholderProcessors'][\Rfuehricht\Configloader\PlaceholderProcessor\ConfigPlaceholderProcessor::class] = [
    'after' => [\TYPO3\CMS\Core\Configuration\Processor\Placeholder\EnvVariableProcessor::class]
];
