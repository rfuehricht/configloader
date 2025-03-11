<?php

namespace Rfuehricht\Configloader\Hooks;

use Rfuehricht\Configloader\Utility\CacheUtility;


final class DataHandlerClearCachePostProcHook
{

    public function clearConfigurationCache(): void
    {
        CacheUtility::flush();
    }
}
