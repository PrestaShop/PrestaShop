<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Addon\Theme;

use AbstractAssetManager;
use PrestaShop\PrestaShop\Adapter\Configuration;
use PrestaShop\PrestaShop\Core\Addon\AddonInterface;
use PrestaShop\PrestaShop\Core\Util\ArrayFinder;
use PrestaShop\PrestaShop\Core\Util\File\YamlParser;

class Theme implements AddonInterface
{
    /**
     * If you change this value, you should probably also update the PS_FF_DEFAULT_THEME in .env file
     *
     * Priority (from most important to less important) is defined as (when present):
     *  - Env variable PS_FF_DEFAULT_THEME from environment (system, shell, apache, ...)
     *  - .env.local PS_FF_DEFAULT_THEME variable (if file is present)
     *  - .env PS_FF_DEFAULT_THEME variable (if file is present, by default it should be)
     *  - .env.dist PS_FF_DEFAULT_THEME variable (if file is present)
     *  - Theme::DEFAULT_THEME private const (last fallback when no env variable is defined)
     */
    private const DEFAULT_THEME = 'hummingbird';

    /**
     * List of core native themes.
     */
    public const CORE_THEMES = [
        'classic',
        'hummingbird',
    ];

    /**
     * @var ArrayFinder
     */
    private $attributes;

    public static function getDefaultTheme(): string
    {
        return $_ENV['PS_FF_DEFAULT_THEME'] ?? Theme::DEFAULT_THEME;
    }

    /**
     * @param array $attributes Theme attributes
     * @param string|null $configurationCacheDirectory Default _PS_CACHE_DIR_
     * @param string $themesDirectory Default _PS_ALL_THEMES_DIR_
     */
    public function __construct(
        array $attributes,
        ?string $configurationCacheDirectory = null,
        string $themesDirectory = _PS_ALL_THEMES_DIR_
    ) {
        if (isset($attributes['parent'])) {
            if (null === $configurationCacheDirectory) {
                $configurationCacheDirectory = (new Configuration())->get('_PS_CACHE_DIR_');
            }

            $yamlParser = new YamlParser($configurationCacheDirectory);
            $parentAttributes = $yamlParser->parse($themesDirectory . '/' . $attributes['parent'] . '/config/theme.yml');
            $parentAttributes['preview'] = 'themes/' . $attributes['parent'] . '/preview.png';
            $parentAttributes['parent_directory'] = rtrim($attributes['directory'], '/') . '/';
            $attributes = array_merge($parentAttributes, $attributes);
        }

        $attributes['directory'] = rtrim($attributes['directory'], '/') . '/';

        if (file_exists($themesDirectory . $attributes['name'] . '/preview.png')) {
            $attributes['preview'] = 'themes/' . $attributes['name'] . '/preview.png';
        }

        $this->attributes = new ArrayFinder($attributes);
    }

    public function get($attr = null, $default = null)
    {
        return $this->attributes->get($attr, $default);
    }

    public function has($attr)
    {
        return $this->attributes->offsetExists($attr);
    }

    public function getName()
    {
        return $this->attributes->get('name');
    }

    public function getDirectory()
    {
        return $this->attributes->get('directory');
    }

    public function getModulesToEnable()
    {
        $modulesToEnable = $this->get('global_settings.modules.to_enable', []);
        $modulesToHook = $this->get('global_settings.hooks.modules_to_hook', []);

        foreach ($modulesToHook as $modules) {
            if (is_array($modules)) {
                foreach (array_values($modules) as $module) {
                    if (is_array($module)) {
                        $module = key($module);
                    }
                    if (null !== $module && !in_array($module, $modulesToEnable)) {
                        $modulesToEnable[] = $module;
                    }
                }
            }
        }

        return $modulesToEnable;
    }

    public function getModulesToDisable()
    {
        return $this->get('dependencies.modules', []);
    }

    public function getPageSpecificAssets($pageId)
    {
        return [
            'css' => $this->getPageSpecificCss($pageId),
            'js' => $this->getPageSpecificJs($pageId),
        ];
    }

    public function onInstall()
    {
        return true;
    }

    public function onUninstall()
    {
        return true;
    }

    /**
     * Execute up files. You can update configuration, update sql schema.
     * No file modification.
     *
     * @return bool true for success
     */
    public function onUpgrade($version)
    {
        return true;
    }

    /**
     * Called when switching the current theme of the selected shop.
     * You can update configuration, enable/disable modules...
     *
     * @return bool true for success
     */
    public function onEnable()
    {
        return true;
    }

    /**
     * Not necessarily the opposite of enable. Use this method if
     * something must be done when switching to another theme (like uninstall
     * very specific modules for example).
     *
     * @return bool true for success
     */
    public function onDisable()
    {
        return true;
    }

    public function onReset()
    {
        return true;
    }

    public function setPageLayouts(array $layouts)
    {
        $this->attributes->set('theme_settings.layouts', $layouts);
    }

    public function getDefaultLayout()
    {
        $availableLayouts = $this->getAvailableLayouts();
        $defaultLayoutIdentifier = $this->attributes->get('theme_settings.default_layout');
        $defaultLayout = $availableLayouts[$defaultLayoutIdentifier];

        $defaultLayout['key'] = $defaultLayoutIdentifier;

        return $defaultLayout;
    }

    public function getPageLayouts()
    {
        return $this->attributes->get('theme_settings.layouts');
    }

    public function getAvailableLayouts()
    {
        return $this->attributes->get('meta.available_layouts');
    }

    /**
     * Returns layout name for page from theme configuration
     *
     * @param string $page page identifier
     *
     * @return string layout name
     */
    public function getLayoutNameForPage($page)
    {
        $layout_name = $this->get('theme_settings.default_layout');
        if (isset($this->attributes['theme_settings']['layouts'][$page])
            && $this->attributes['theme_settings']['layouts'][$page]) {
            $layout_name = $this->attributes['theme_settings']['layouts'][$page];
        }

        return $layout_name;
    }

    /**
     * Returns layout relative path for provided page identifier
     *
     * @param string $page page identifier
     *
     * @return string layout relative path
     */
    public function getLayoutRelativePathForPage($page)
    {
        return $this->getLayoutPath($this->getLayoutNameForPage($page));
    }

    /**
     * Returns relative path for provided layout name
     *
     * @param string $layoutName layout name
     *
     * @return string layout relative path
     */
    public function getLayoutPath($layoutName)
    {
        return 'layouts/' . $layoutName . '.tpl';
    }

    private function getPageSpecificCss($pageId)
    {
        $css = array_merge(
            (array) $this->get('assets.css.all'),
            (array) $this->get('assets.css.' . $pageId)
        );
        foreach ($css as $key => &$entry) {
            // Required parameters
            if (!isset($entry['id']) || !isset($entry['path'])) {
                unset($css[$key]);

                continue;
            }
            if (!isset($entry['media'])) {
                $entry['media'] = AbstractAssetManager::DEFAULT_MEDIA;
            }
            if (!isset($entry['priority'])) {
                $entry['priority'] = AbstractAssetManager::DEFAULT_PRIORITY;
            }
            if (!isset($entry['inline'])) {
                $entry['inline'] = false;
            }
        }

        return $css;
    }

    private function getPageSpecificJs($pageId)
    {
        $js = array_merge(
            (array) $this->get('assets.js.all'),
            (array) $this->get('assets.js.' . $pageId)
        );
        foreach ($js as $key => &$entry) {
            // Required parameters
            if (!isset($entry['id']) || !isset($entry['path'])) {
                unset($js[$key]);

                continue;
            }
            if (!isset($entry['position'])) {
                $entry['position'] = AbstractAssetManager::DEFAULT_JS_POSITION;
            }
            if (!isset($entry['priority'])) {
                $entry['priority'] = AbstractAssetManager::DEFAULT_PRIORITY;
            }
            if (!isset($entry['inline'])) {
                $entry['inline'] = false;
            }
            if (!isset($entry['attribute'])) {
                $entry['attribute'] = false;
            }
        }

        return $js;
    }

    /**
     * Defines if the theme requires core.js scripts or it provides it's own implementation.
     *
     * @return bool
     */
    public function requiresCoreScripts(): bool
    {
        return $this->attributes->get('theme_settings.core_scripts', true);
    }
}
