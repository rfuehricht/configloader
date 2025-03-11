# EXT:configloader - Configuration Loading for TYPO3

This extension uses `hassankhan/config` to load configuration files in any supported format.

Visit [https://github.com/hassankhan/config](https://github.com/hassankhan/config) for details.

## How it works

Place files in supported format in supported directories and `EXT:configloader` parses them automatically:

* Project root folder
* `config/system`
* `config/sites/[site-identifier]` for site related settings

After loading, the configuration is available via `ConfigurationUtility->get()`. Via various helper functions and View Helpers you can also access the configuration values in TypoScript, Site configurations and Fluid templates.

Use dot notation to access nested configuration keys.

You can add your own configuration files using an `EventListener`. See section below.

> It is recommended to prefix configuration files with `.` to be hidden files on UNIX system by default.

## Example configuration file in JSON format

```
{
  "typo3": {
    "setting": "foo"
  }
}
```

## Installation

Just require the extension via composer:

```
composer require rfuehricht/configloader
```

## Configuration

In the TYPO3 backend, navigate to `Settings -> Configure extensions`.
There, you can adjust the default format and file name of configuration files.

You can select to load all supported file formats and configure "*" as file name to load ALL available files in a folder.

The default file name and format loaded is `.settings.yaml`.

## Usage

`EXT:configloader` additionally provides helper functions and uses TYPO3 hooks to provide configuration at different places in backend and frontend.

### ViewHelper

The viewhelper `getConfig` may be used to access configuration in Fluid templates:

```
<html data-namespace-typo3-fluid="true"
      xmlns:config="http://typo3.org/ns/Rfuehricht/Configloader/ViewHelpers"
>

    <config:getConfig key="typo3.setting" alternative="fallback value" />

</html>
```

### TypoScript

In TypoScript you can access configuration either in values or in conditions:

```
page.5 = TEXT
page.5.value := getConfig(typo3.setting)
```

```
[getConfig('typo3.setting') == 'foo']
page.10.value = foo
[end]
```

### Site Configuration

You can also use placeholders in site configuration files:

```
languages:
  -
    title: '%getConfig(typo3.setting)%'
    enabled: true
    languageId: 0
    base: /
    locale: de_AT.UTF-8
    navigationTitle: Deutsch
    flag: at
rootPageId: '%getConfig(mySite.rootPageId)%'
```

> In site configuration you can NOT use configuration values defined in 
> configuration files in your site configuration folder (e.g. `config/sites/default/.settings.yaml`).
> These file cannot be loaded before loading the site configuration.

### TCA

It may be needed, to have configuration items selectable in a TCA record.
`EXT:configloader` provides an `ItemsProcFunc` to create a select field:

```
$GLOBALS['TCA']['tt_content']['columns']['myfield']['config'] = [
    'type' => 'select',
    'renderType' => 'selectSingle',
    'items' => [
        ['', '']
    ],
    'itemsProcFunc' => \Rfuehricht\Configloader\Backend\ItemsProcFunc::class . '->getConfigurationKeys'
];
```

### PHP

In your custom PHP code, you can load and access the configuration using:

```
use Rfuehricht\Configloader\Utility\ConfigurationUtility;
...

class MyClass 
{

    public function __construct(
        protected ConfigurationUtility $configurationUtility
    )
    {}
    
    public function myFunction(): void
    {
        $configurationValue = $this->configurationUtility->get('typo3.setting');   
    }

}
```

In places without dependency injection (e.g. additional.php):

```
$configurationUtility = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\Rfuehricht\Configloader\Utility\ConfigurationUtility::class);
$configurationValue = $configurationUtility->get('typo3.setting');   
```