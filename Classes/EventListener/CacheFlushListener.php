<?php

namespace Rfuehricht\Configloader\EventListener;

use Rfuehricht\Configloader\Utility\CacheUtility;
use TYPO3\CMS\Core\Cache\Event\CacheFlushEvent;


final class CacheFlushListener
{

    public function __invoke(CacheFlushEvent $event): void
    {
        CacheUtility::flush();
    }
}
