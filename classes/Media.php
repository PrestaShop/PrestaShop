<?php
/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/OSL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to https://devdocs.prestashop.com/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

/**
 * Class MediaCore.
 */
class MediaCore
{
    public static $jquery_ui_dependencies = [
        'ui.core' => ['fileName' => 'jquery.ui.core.min.js', 'dependencies' => [], 'theme' => true],
        'ui.widget' => ['fileName' => 'jquery.ui.widget.min.js', 'dependencies' => [], 'theme' => false],
        'ui.mouse' => ['fileName' => 'jquery.ui.mouse.min.js', 'dependencies' => ['ui.core', 'ui.widget'], 'theme' => false],
        'ui.position' => ['fileName' => 'jquery.ui.position.min.js', 'dependencies' => [], 'theme' => false],
        'ui.draggable' => ['fileName' => 'jquery.ui.draggable.min.js', 'dependencies' => ['ui.core', 'ui.widget', 'ui.mouse'], 'theme' => false],
        'ui.droppable' => ['fileName' => 'jquery.ui.droppable.min.js', 'dependencies' => ['ui.core', 'ui.widget', 'ui.mouse', 'ui.draggable'], 'theme' => false],
        'ui.resizable' => ['fileName' => 'jquery.ui.resizable.min.js', 'dependencies' => ['ui.core', 'ui.widget', 'ui.mouse'], 'theme' => true],
        'ui.selectable' => ['fileName' => 'jquery.ui.selectable.min.js', 'dependencies' => ['ui.core', 'ui.widget', 'ui.mouse'], 'theme' => true],
        'ui.sortable' => ['fileName' => 'jquery.ui.sortable.min.js', 'dependencies' => ['ui.core', 'ui.widget', 'ui.mouse'], 'theme' => true],
        'ui.autocomplete' => ['fileName' => 'jquery.ui.autocomplete.min.js', 'dependencies' => ['ui.core', 'ui.widget', 'ui.position', 'ui.menu'], 'theme' => true],
        'ui.button' => ['fileName' => 'jquery.ui.button.min.js', 'dependencies' => ['ui.core', 'ui.widget'], 'theme' => true],
        'ui.dialog' => ['fileName' => 'jquery.ui.dialog.min.js', 'dependencies' => ['ui.core', 'ui.widget', 'ui.position', 'ui.button'], 'theme' => true],
        'ui.menu' => ['fileName' => 'jquery.ui.menu.min.js', 'dependencies' => ['ui.core', 'ui.widget', 'ui.position'], 'theme' => true],
        'ui.slider' => ['fileName' => 'jquery.ui.slider.min.js', 'dependencies' => ['ui.core', 'ui.widget', 'ui.mouse'], 'theme' => true],
        'ui.spinner' => ['fileName' => 'jquery.ui.spinner.min.js', 'dependencies' => ['ui.core', 'ui.widget', 'ui.button'], 'theme' => true],
        'ui.tabs' => ['fileName' => 'jquery.ui.tabs.min.js', 'dependencies' => ['ui.core', 'ui.widget'], 'theme' => true],
        'ui.datepicker' => ['fileName' => 'jquery.ui.datepicker.min.js', 'dependencies' => ['ui.core'], 'theme' => true],
        'ui.progressbar' => ['fileName' => 'jquery.ui.progressbar.min.js', 'dependencies' => ['ui.core', 'ui.widget'], 'theme' => true],
        'ui.tooltip' => ['fileName' => 'jquery.ui.tooltip.min.js', 'dependencies' => ['ui.core', 'ui.widget', 'ui.position', 'effects.core'], 'theme' => true],
        'ui.accordion' => ['fileName' => 'jquery.ui.accordion.min.js', 'dependencies' => ['ui.core', 'ui.widget', 'effects.core'], 'theme' => true],
        'effects.core' => ['fileName' => 'jquery.effects.core.min.js', 'dependencies' => [], 'theme' => false],
        'effects.blind' => ['fileName' => 'jquery.effects.blind.min.js', 'dependencies' => ['effects.core'], 'theme' => false],
        'effects.bounce' => ['fileName' => 'jquery.effects.bounce.min.js', 'dependencies' => ['effects.core'], 'theme' => false],
        'effects.clip' => ['fileName' => 'jquery.effects.clip.min.js', 'dependencies' => ['effects.core'], 'theme' => false],
        'effects.drop' => ['fileName' => 'jquery.effects.drop.min.js', 'dependencies' => ['effects.core'], 'theme' => false],
        'effects.explode' => ['fileName' => 'jquery.effects.explode.min.js', 'dependencies' => ['effects.core'], 'theme' => false],
        'effects.fade' => ['fileName' => 'jquery.effects.fade.min.js', 'dependencies' => ['effects.core'], 'theme' => false],
        'effects.fold' => ['fileName' => 'jquery.effects.fold.min.js', 'dependencies' => ['effects.core'], 'theme' => false],
        'effects.highlight' => ['fileName' => 'jquery.effects.highlight.min.js', 'dependencies' => ['effects.core'], 'theme' => false],
        'effects.pulsate' => ['fileName' => 'jquery.effects.pulsate.min.js', 'dependencies' => ['effects.core'], 'theme' => false],
        'effects.scale' => ['fileName' => 'jquery.effects.scale.min.js', 'dependencies' => ['effects.core'], 'theme' => false],
        'effects.shake' => ['fileName' => 'jquery.effects.shake.min.js', 'dependencies' => ['effects.core'], 'theme' => false],
        'effects.slide' => ['fileName' => 'jquery.effects.slide.min.js', 'dependencies' => ['effects.core'], 'theme' => false],
        'effects.transfer' => ['fileName' => 'jquery.effects.transfer.min.js', 'dependencies' => ['effects.core'], 'theme' => false],
    ];

    private static $jquery_ui_datepicker_iso_code = [
        'bn' => 'en',
        'bz' => 'en',
        'dh' => 'de',
        'gb' => 'en-GB',
        'ag' => 'es',
        'cb' => 'es',
        'mx' => 'es',
        'pe' => 'es',
        've' => 'es',
        'qc' => 'fr-CA',
        'ga' => 'en',
        'lo' => 'en',
        'br' => 'pt-BR',
        'sh' => 'en',
        'si' => 'sl',
        'ug' => 'en',
        'ur' => 'en',
        'vn' => 'vi',
        'zh' => 'zh-CN',
        'tw' => 'zh-TW',
    ];

    /**
     * @var array list of javascript definitions
     */
    protected static $js_def = [];

    /**
     * addJS return javascript path.
     *
     * @param mixed $jsUri
     *
     * @return string
     */
    public static function getJSPath($jsUri)
    {
        return Media::getMediaPath($jsUri);
    }

    /**
     * addCSS return stylesheet path.
     *
     * @param mixed $cssUri
     * @param string $cssMediaType
     * @param bool $needRtl
     *
     * @return bool|array<string, string>
     */
    public static function getCSSPath($cssUri, $cssMediaType = 'all', $needRtl = true)
    {
        // RTL Ready: search and load rtl css file if it's not originally rtl
        if ($needRtl && Context::getContext()->language->is_rtl) {
            $cssUriRtl = preg_replace('/(^[^.].*)(\.css)$/', '$1_rtl.css', $cssUri);
            $rtlMedia = Media::getMediaPath($cssUriRtl, $cssMediaType);
            if ($rtlMedia != false) {
                return $rtlMedia;
            }
        }
        // End RTL
        return Media::getMediaPath($cssUri, $cssMediaType);
    }

    /**
     * Get Media path.
     *
     * @param array|string|null $mediaUri
     * @param string|null $cssMediaType
     *
     * @return bool|string|array<string, string>
     */
    public static function getMediaPath($mediaUri, $cssMediaType = null)
    {
        if (is_array($mediaUri) || $mediaUri === null || empty($mediaUri)) {
            return false;
        }

        $urlData = parse_url($mediaUri);
        if (!is_array($urlData) || !array_key_exists('path', $urlData)) {
            return false;
        }

        if (!array_key_exists('host', $urlData)) {
            $mediaUri = '/' . ltrim(str_replace(str_replace(['/', '\\'], DIRECTORY_SEPARATOR, _PS_ROOT_DIR_), __PS_BASE_URI__, $urlData['path']), '/\\');
            // remove PS_BASE_URI on _PS_ROOT_DIR_ for the following
            $fileUri = _PS_ROOT_DIR_ . Tools::str_replace_once(__PS_BASE_URI__, DIRECTORY_SEPARATOR, $mediaUri);
            if (!file_exists($fileUri) || !@filemtime($fileUri) || @filesize($fileUri) === 0) {
                return false;
            }

            $mediaUri = str_replace('//', '/', $mediaUri);
            if (array_key_exists('query', $urlData)) {
                $mediaUri .= '?' . $urlData['query'];
            }
        }

        if ($cssMediaType) {
            return [$mediaUri => $cssMediaType];
        }

        return $mediaUri;
    }

    /**
     * return jqueryUI component path.
     *
     * @param string $component
     * @param string $theme
     * @param bool $checkDependencies
     *
     * @return array<string, array<string>>
     */
    public static function getJqueryUIPath($component, $theme, $checkDependencies)
    {
        $uiPath = ['js' => [], 'css' => []];
        $folder = _PS_JS_DIR_ . 'jquery/ui/';
        $file = 'jquery.' . $component . '.min.js';
        $urlData = parse_url($folder . $file);
        $fileUri = _PS_ROOT_DIR_ . Tools::str_replace_once(__PS_BASE_URI__, DIRECTORY_SEPARATOR, $urlData['path']);
        $uiTmp = [];
        if (isset(Media::$jquery_ui_dependencies[$component]) && Media::$jquery_ui_dependencies[$component]['theme'] && $checkDependencies) {
            $themeCss = Media::getCSSPath($folder . 'themes/' . $theme . '/jquery.ui.theme.css');
            $compCss = Media::getCSSPath($folder . 'themes/' . $theme . '/jquery.' . $component . '.css');
            if (!empty($themeCss)) {
                $uiPath['css'] = array_merge($uiPath['css'], $themeCss);
            }
            if (!empty($compCss)) {
                $uiPath['css'] = array_merge($uiPath['css'], $compCss);
            }
        }
        if ($checkDependencies && array_key_exists($component, self::$jquery_ui_dependencies)) {
            foreach (self::$jquery_ui_dependencies[$component]['dependencies'] as $dependency) {
                $uiTmp[] = Media::getJqueryUIPath($dependency, $theme, false);
                if (self::$jquery_ui_dependencies[$dependency]['theme']) {
                    $depCss = Media::getCSSPath($folder . 'themes/' . $theme . '/jquery.' . $dependency . '.css');
                }

                if (isset($depCss) && !empty($depCss)) {
                    $uiPath['css'] = array_merge($uiPath['css'], $depCss);
                }
            }
        }

        if (@filemtime($fileUri)) {
            if (!empty($uiTmp)) {
                foreach ($uiTmp as $ui) {
                    if (!empty($ui['js'])) {
                        $uiPath['js'][] = $ui['js'];
                    }

                    if (!empty($ui['css'])) {
                        $uiPath['css'][] = $ui['css'];
                    }
                }
            }

            $uiPath['js'][] = Media::getJSPath($folder . $file);
        }

        //add i18n file for datepicker
        if ($component == 'ui.datepicker') {
            $datePickerIsoCode = Context::getContext()->language->iso_code;
            if (array_key_exists($datePickerIsoCode, self::$jquery_ui_datepicker_iso_code)) {
                $datePickerIsoCode = self::$jquery_ui_datepicker_iso_code[$datePickerIsoCode];
            }
            $uiPath['js'][] = Media::getJSPath($folder . 'i18n/jquery.ui.datepicker-' . $datePickerIsoCode . '.js');
        }

        return $uiPath;
    }

    /**
     * return jquery plugin path.
     *
     * @param mixed $name
     * @param string|null $folder
     *
     * @return bool|array{js: string, css: array<string, string>}
     */
    public static function getJqueryPluginPath($name, $folder = null)
    {
        $pluginPath = ['js' => [], 'css' => []];
        if ($folder === null) {
            $folder = _PS_JS_DIR_ . 'jquery/plugins/';
        } //set default folder

        $file = 'jquery.' . $name . '.js';
        $urlData = parse_url($folder);
        $fileUri = _PS_ROOT_DIR_ . Tools::str_replace_once(__PS_BASE_URI__, DIRECTORY_SEPARATOR, $urlData['path']);

        if (@file_exists($fileUri . $file)) {
            $pluginPath['js'] = Media::getJSPath($folder . $file);
        } elseif (@file_exists($fileUri . $name . '/' . $file)) {
            $pluginPath['js'] = Media::getJSPath($folder . $name . '/' . $file);
        } else {
            return false;
        }
        $pluginPath['css'] = Media::getJqueryPluginCSSPath($name, $folder);

        return $pluginPath;
    }

    /**
     * return jquery plugin css path if exist.
     *
     * @param mixed $name
     * @param string|null $folder
     *
     * @return bool|array<string, string>
     */
    public static function getJqueryPluginCSSPath($name, $folder = null)
    {
        if ($folder === null) {
            $folder = _PS_JS_DIR_ . 'jquery/plugins/';
        } //set default folder
        $file = 'jquery.' . $name . '.css';
        $urlData = parse_url($folder);
        $fileUri = _PS_ROOT_DIR_ . Tools::str_replace_once(__PS_BASE_URI__, DIRECTORY_SEPARATOR, $urlData['path']);

        if (@file_exists($fileUri . $file)) {
            return Media::getCSSPath($folder . $file);
        } elseif (@file_exists($fileUri . $name . '/' . $file)) {
            return Media::getCSSPath($folder . $name . '/' . $file);
        } else {
            return false;
        }
    }

    /**
     * Clear theme cache.
     */
    public static function clearCache()
    {
        $files = array_merge(
            glob(_PS_THEME_DIR_ . 'assets/cache/*', GLOB_NOSORT),
            glob(_PS_THEME_DIR_ . 'cache/*', GLOB_NOSORT)
        );

        foreach ($files as $file) {
            if ('index.php' !== basename($file)) {
                Tools::deleteFile($file);
            }
        }

        $version = (int) Configuration::get('PS_CCCJS_VERSION');
        Configuration::updateValue('PS_CCCJS_VERSION', ++$version);
        $version = (int) Configuration::get('PS_CCCCSS_VERSION');
        Configuration::updateValue('PS_CCCCSS_VERSION', ++$version);
    }

    /**
     * Get JS definitions.
     *
     * @return array JS definitions
     */
    public static function getJsDef()
    {
        ksort(Media::$js_def);

        return Media::$js_def;
    }

    /**
     * Add a new javascript definition at bottom of page.
     *
     * @param mixed $jsDef
     */
    public static function addJsDef($jsDef)
    {
        if (is_array($jsDef)) {
            foreach ($jsDef as $key => $js) {
                Media::$js_def[$key] = $js;
            }
        } elseif ($jsDef) {
            Media::$js_def[] = $jsDef;
        }
    }

    /**
     * Add a new javascript definition from a capture at bottom of page.
     *
     * @param mixed $params
     * @param string $content
     * @param Smarty $smarty
     * @param bool $repeat
     */
    public static function addJsDefL($params, $content, $smarty = null, &$repeat = false)
    {
        if (!$repeat && isset($params) && Tools::strlen($content)) {
            if (!is_array($params)) {
                $params = (array) $params;
            }

            foreach ($params as $param) {
                Media::$js_def[$param] = $content;
            }
        }
    }
}
