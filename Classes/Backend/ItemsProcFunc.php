<?php

namespace Rfuehricht\Configloader\Backend;

use Rfuehricht\Configloader\Utility\ConfigurationUtility;
use TYPO3\CMS\Core\Exception\SiteNotFoundException;
use TYPO3\CMS\Core\Site\SiteFinder;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class ItemsProcFunc
{

    public function getConfigurationKeys(array &$params): void
    {
        $site = null;
        if (isset($params['row']['pid'])) {
            $siteFinder = GeneralUtility::makeInstance(SiteFinder::class);
            try {
                $site = $siteFinder->getSiteByPageId($params['row']['pid']);
            } catch (SiteNotFoundException) {
            }
        }
        $config = GeneralUtility::makeInstance(ConfigurationUtility::class)->loadConfiguration($site);
        $keys = array_keys($this->flatten($config->all()));
        sort($keys);
        foreach ($keys as $key) {
            $params['items'][] = [$key, $key];
        }

    }

    private function flatten(array $array, string $prefix = ''): array
    {
        $result = array();
        foreach ($array as $key => $value) {
            if (is_array($value)) {
                $result = $result + $this->flatten($value, $prefix . $key . '.');
            } else {
                $result[$prefix . $key] = $value;
            }
        }
        return $result;
    }

}