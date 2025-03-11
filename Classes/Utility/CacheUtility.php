<?php

namespace Rfuehricht\Configloader\Utility;

use Noodlehaus\Config;
use TYPO3\CMS\Core\Core\Environment;
use TYPO3\CMS\Core\SingletonInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class CacheUtility implements SingletonInterface
{

    static private string $cachePath = '';

    static private function getCacheFilePath(string $key): string
    {
        $cachePath = self::getCachePath();
        return $cachePath . '/.' . $key;
    }

    static private function getCachePath(): string
    {
        if (strlen(self::$cachePath) === 0) {
            self::$cachePath = rtrim(Environment::getVarPath(), '/') . '/cache/data/configloader';
            if (!file_exists(self::$cachePath)) {
                GeneralUtility::mkdir_deep(self::$cachePath);
            }
        }
        return self::$cachePath;
    }

    static public function flush(): void
    {
        $cachePath = self::getCachePath();
        if (file_exists($cachePath)) {
            GeneralUtility::rmdir($cachePath, removeNonEmpty: true);
        }
        GeneralUtility::mkdir_deep($cachePath);
    }

    static public function get(string $key): ?Config
    {

        $cacheFile = self::getCacheFilePath($key);
        if (file_exists($cacheFile)) {
            return Config::load($cacheFile, new \Noodlehaus\Parser\Serialize());
        }
        return null;
    }

    static public function set(string $key, Config $value): void
    {
        $cacheFile = self::getCacheFilePath($key);
        $value->toFile($cacheFile, new \Noodlehaus\Writer\Serialize());
    }
}