<?php

use Rfuehricht\Configloader\Hooks\DataHandlerClearCachePostProcHook;
use Rfuehricht\Configloader\PlaceholderProcessor\ConfigPlaceholderProcessor;
use TYPO3\CMS\Core\Configuration\Processor\Placeholder\EnvVariableProcessor;

defined('TYPO3') or die();

$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_tcemain.php']['clearCachePostProc']['configloader'] = DataHandlerClearCachePostProcHook::class . '->clearConfigurationCache';

// Parses config-references provided by the loaded configuration files
$GLOBALS['TYPO3_CONF_VARS']['SYS']['yamlLoader']['placeholderProcessors'][ConfigPlaceholderProcessor::class] = [
    'after' => [EnvVariableProcessor::class]
];
