<?php

namespace Rfuehricht\Configloader\Utility;

use Exception;
use Noodlehaus\Config;
use Psr\Http\Message\ServerRequestInterface;
use TYPO3\CMS\Backend\Utility\BackendUtility;
use TYPO3\CMS\Core\Core\Environment;
use TYPO3\CMS\Core\Exception\SiteNotFoundException;
use TYPO3\CMS\Core\SingletonInterface;
use TYPO3\CMS\Core\Site\Entity\NullSite;
use TYPO3\CMS\Core\Site\Entity\Site;
use TYPO3\CMS\Core\Site\Entity\SiteInterface;
use TYPO3\CMS\Core\Site\SiteFinder;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class ConfigurationUtility implements SingletonInterface
{
    private array $configurations;

    private array $extensionConfiguration;

    public function __construct()
    {
        $this->configurations = [];
        $this->extensionConfiguration = $GLOBALS['TYPO3_CONF_VARS']['EXTENSIONS']['configloader'];
    }

    /**
     * Loads configuration object from configuration files and returns it.
     *
     * @param SiteInterface|null $site Site to load configuration for
     * @return Config
     */
    public function loadConfiguration(?SiteInterface $site = null): Config
    {

        //Return cached content if available
        if ($site instanceof Site && isset($this->configurations[$site->getIdentifier()])) {
            return $this->configurations[$site->getIdentifier()];
        }
        if ($site instanceof Site && ($configuration = CacheUtility::get($site->getIdentifier()))) {
            $this->configurations[$site->getIdentifier()] = $configuration;
            return $configuration;
        }

        if (($site === null || $site instanceof NullSite) && isset($this->configurations['global'])) {
            return $this->configurations['global'];
        }
        if (($site === null || $site instanceof NullSite) && ($configuration = CacheUtility::get('global'))) {
            $this->configurations['global'] = $configuration;
            return $configuration;
        }


        try {
            $fileFormat = $this->extensionConfiguration['fileFormat'] ?? '.json';
        } catch (Exception) {
            $fileFormat = '.json';
        }
        try {
            $fileName = $this->extensionConfiguration['fileName'] ?? '.config';
        } catch (Exception) {
            $fileName = '.config';
        }

        // Load configuration file
        $rootPath = rtrim(Environment::getProjectPath(), '/') . '/';
        $configPath = rtrim(Environment::getConfigPath(), '/') . '/';

        //Possibility to load all supported files in the given directories!
        $pattern = '';
        $defaultFileName = '';
        if (str_contains($fileName, '*') && $fileFormat === '*') {
            if ($fileName === '*') {
                $pattern = '{,.}*{yaml,json,xml,ini}';
            } else {
                $pattern = $fileName . '{yaml,json,xml,ini}';
            }
        } elseif (str_contains($fileName, '*')) {
            if ($fileName === '*') {
                $pattern = '{,.}*[!.]*' . $fileFormat;
            } else {
                $pattern = $fileName . $fileFormat;
            }
        } elseif ($fileFormat === '*') {
            $pattern = $fileName;
        } else {
            $defaultFileName = $fileName . $fileFormat;
        }

        $possibleFiles = [];
        if ($defaultFileName) {
            $possibleFiles = [
                '?' . $rootPath . $defaultFileName,
                '?' . $configPath . 'system/' . $defaultFileName
            ];
        } elseif ($pattern) {
            $possibleFiles = glob($rootPath . $pattern, GLOB_BRACE);
            $possibleFiles = array_merge(
                $possibleFiles,
                glob($configPath . 'system/' . $pattern, GLOB_BRACE)
            );
        }

        //Store additional files in array and merge just before configuration loading to make sure they are included last.
        $additionalFiles = [];
        if (isset($this->extensionConfiguration['additionalFiles'])) {
            $files = GeneralUtility::trimExplode(
                ',',
                trim($this->extensionConfiguration['additionalFiles']),
                removeEmptyValues: true);
            $projectPath = rtrim(Environment::getProjectPath(), '/') . '/';
            foreach ($files as &$file) {
                if (!str_starts_with($file, '/')) {
                    $file = $projectPath . $file;
                }
            }
            if (count($files) > 0 && strlen($files[0]) > 0) {
                $additionalFiles = $files;
            }
        }

        $configurations = [];

        //Load and cache global configuration
        $cacheIdentifier = 'global';
        if (!($configuration = CacheUtility::get($cacheIdentifier))) {
            $possibleFiles = array_merge($possibleFiles, $additionalFiles);
            $configuration = Config::load($possibleFiles);
            if ($configuration->valid()) {
                CacheUtility::set($cacheIdentifier, $configuration);
            } else {
                throw new Exception('Configuration file not valid');
            }
        }
        $configurations[$cacheIdentifier] = $configuration;

        //Load and cache site specific configuration
        $siteDirectories = array_filter(glob($configPath . 'sites/*'), 'is_dir');
        foreach ($siteDirectories as $siteDirectory) {
            $siteDirectory = trim(str_replace($configPath . 'sites/', '', $siteDirectory), '/');
            $localPossibleFiles = $possibleFiles;
            if ($defaultFileName) {
                $localPossibleFiles[] = '?' . $configPath . 'sites/' . $siteDirectory . '/' . $defaultFileName;
            } elseif ($pattern) {
                $localPossibleFiles = array_merge(
                    $localPossibleFiles,
                    glob($configPath . 'sites/' . $siteDirectory . '/' . $pattern, GLOB_BRACE)
                );
            }

            $cacheIdentifier = $siteDirectory;
            if (!($configuration = CacheUtility::get($cacheIdentifier))) {
                $localPossibleFiles = array_merge($localPossibleFiles, $additionalFiles);
                $configuration = Config::load($localPossibleFiles);
                CacheUtility::set($cacheIdentifier, $configuration);
            }
            $configurations[$cacheIdentifier] = $configuration;
        }

        //If configuration of a site was requested, use site related configuration
        if ($site && isset($configurations[$site->getIdentifier()])) {
            $configuration = $configurations[$site->getIdentifier()];
        }

        $this->configurations = $configurations;

        return $configuration;
    }

    /**
     * @throws Exception
     */
    public function get(string $key, ?string $alternative = ''): string|array|null
    {
        $site = (isset($GLOBALS['TYPO3_REQUEST']) ? $GLOBALS['TYPO3_REQUEST']->getAttribute('site') : null);
        if (($site === null || $site instanceof NullSite) && isset($GLOBALS['TYPO3_REQUEST'])) {
            $site = $this->determineSiteFromRequestParameters($GLOBALS['TYPO3_REQUEST']);
        }
        $configuration = $this->loadConfiguration($site);

        return $configuration->get($key, $alternative);
    }

    public function determineSiteFromRequestParameters(?ServerRequestInterface $request): ?Site
    {

        //Try to find out current site manually.
        if ($request) {
            if (isset($request->getParsedBody()['popViewId'])) {
                $pageId = intval($request->getParsedBody()['popViewId']);
                $siteFinder = GeneralUtility::makeInstance(SiteFinder::class);
                try {
                    $site = $siteFinder->getSiteByPageId($pageId);

                } catch (SiteNotFoundException) {
                }
            } elseif (isset($request->getQueryParams()['edit'])) {
                $params = $request->getQueryParams()['edit'];
                $keys = array_keys($params);
                $table = array_pop($keys);
                $keys = array_keys(array_pop($params));
                $uid = intval(array_pop($keys));
                if ($table && $uid) {
                    $siteFinder = GeneralUtility::makeInstance(SiteFinder::class);
                    if ($table === 'pages') {
                        try {
                            $site = $siteFinder->getSiteByPageId($uid);

                        } catch (SiteNotFoundException) {
                        }
                    } else {
                        $record = BackendUtility::getRecord($table, $uid);
                        if (isset($record['pid'])) {
                            try {
                                $site = $siteFinder->getSiteByPageId($record['pid']);

                            } catch (SiteNotFoundException) {
                            }
                        }
                    }
                }
            }
        }
        return ($site ?? null);
    }

}
