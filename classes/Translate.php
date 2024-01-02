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
 * Class TranslateCore.
 *
 * @since 1.5.0
 */
class TranslateCore
{
    public static $regexSprintfParams = '#(?:%%|%(?:[0-9]+\$)?[+-]?(?:[ 0]|\'.)?-?[0-9]*(?:\.[0-9]+)?[bcdeufFosxX])#';
    public static $regexClassicParams = '/%\w+%/';

    /**
     * @param string $string
     * @param string $class
     * @param bool $addslashes
     * @param bool $htmlentities
     * @param array|null $sprintf
     *
     * @return string
     */
    public static function getFrontTranslation($string, $class, $addslashes = false, $htmlentities = true, $sprintf = null)
    {
        global $_LANG;

        $string = preg_replace("/\\\*'/", "\'", $string);
        $key = $class . '_' . md5($string);

        if (isset($_LANG[$key])) {
            $str = $_LANG[$key];
        } else {
            $str = $string;
        }

        if ($htmlentities) {
            $str = htmlspecialchars($str, ENT_QUOTES, 'utf-8');
        }
        $str = str_replace('"', '&quot;', $str);

        if (
            $sprintf !== null
            && (!is_array($sprintf) || !empty($sprintf))
            && !(count($sprintf) === 1 && isset($sprintf['legacy']))
        ) {
            $str = Translate::checkAndReplaceArgs($str, $sprintf);
        }

        return $addslashes ? addslashes($str) : stripslashes($str);
    }

    /**
     * Get a translation for a module.
     *
     * @param string|ModuleCore $module
     * @param string $originalString
     * @param string $source
     * @param string|array|null $sprintf
     * @param bool $js
     * @param string|null $locale
     * @param bool $fallback [default=true] If true, this method falls back to the new translation system if no translation is found
     *
     * @return mixed|string
     *
     * @throws Exception
     */
    public static function getModuleTranslation(
        $module,
        $originalString,
        $source,
        $sprintf = null,
        $js = false,
        $locale = null,
        $fallback = true,
        $escape = true
    ) {
        global $_MODULES, $_MODULE;

        static $langCache = [];
        // $_MODULES is a cache of translations for all module.
        // $translations_merged is a cache of wether a specific module's translations have already been added to $_MODULES
        static $translationsMerged = [];

        $name = $module instanceof ModuleCore ? $module->name : $module;

        if (null !== $locale) {
            $iso = Language::getIsoByLocale($locale);
        }

        if (empty($iso)) {
            $iso = Context::getContext()->language->iso_code;
        }

        if (!isset($translationsMerged[$name][$iso])) {
            $filesByPriority = [
                // PrestaShop 1.5 translations
                _PS_MODULE_DIR_ . $name . '/translations/' . $iso . '.php',
                // PrestaShop 1.4 translations
                _PS_MODULE_DIR_ . $name . '/' . $iso . '.php',
                // Translations in theme
                _PS_THEME_DIR_ . 'modules/' . $name . '/translations/' . $iso . '.php',
                _PS_THEME_DIR_ . 'modules/' . $name . '/' . $iso . '.php',
            ];
            foreach ($filesByPriority as $file) {
                if (file_exists($file)) {
                    include_once $file;
                    $_MODULES = !empty($_MODULES) ? array_merge($_MODULES, $_MODULE) : $_MODULE;
                }
            }
            $translationsMerged[$name][$iso] = true;
        }

        $string = preg_replace("/\\\*'/", "\'", $originalString);
        $key = md5($string);

        $cacheKey = $name . '|' . $string . '|' . $source . '|' . (int) $js . '|' . $iso;
        if (isset($langCache[$cacheKey])) {
            $ret = $langCache[$cacheKey];
        } else {
            $currentKey = strtolower('<{' . $name . '}' . _THEME_NAME_ . '>' . $source) . '_' . $key;
            $defaultKey = strtolower('<{' . $name . '}prestashop>' . $source) . '_' . $key;

            if ('controller' == substr($source, -10, 10)) {
                $file = substr($source, 0, -10);
                $currentKeyFile = strtolower('<{' . $name . '}' . _THEME_NAME_ . '>' . $file) . '_' . $key;
                $defaultKeyFile = strtolower('<{' . $name . '}prestashop>' . $file) . '_' . $key;
            }

            if (isset($currentKeyFile) && !empty($_MODULES[$currentKeyFile])) {
                $ret = stripslashes($_MODULES[$currentKeyFile]);
            } elseif (isset($defaultKeyFile) && !empty($_MODULES[$defaultKeyFile])) {
                $ret = stripslashes($_MODULES[$defaultKeyFile]);
            } elseif (!empty($_MODULES[$currentKey])) {
                $ret = stripslashes($_MODULES[$currentKey]);
            } elseif (!empty($_MODULES[$defaultKey])) {
                $ret = stripslashes($_MODULES[$defaultKey]);
            } else {
                $ret = stripslashes($string);
            }

            if (
                $sprintf !== null &&
                (!is_array($sprintf) || !empty($sprintf)) &&
                !(count($sprintf) === 1 && isset($sprintf['legacy']))
            ) {
                $ret = Translate::checkAndReplaceArgs($ret, $sprintf);
            }

            if ($js) {
                $ret = addslashes($ret);
            } elseif ($escape) {
                $ret = htmlspecialchars($ret, ENT_COMPAT, 'UTF-8');
            }

            if ($sprintf === null) {
                $langCache[$cacheKey] = $ret;
            }
        }

        if (!is_array($sprintf) && null !== $sprintf) {
            $sprintf_for_trans = [$sprintf];
        } elseif (null === $sprintf) {
            $sprintf_for_trans = [];
        } else {
            $sprintf_for_trans = $sprintf;
        }

        /*
         * Native modules working on both 1.6 & 1.7 are translated in messages.xlf
         * So we need to check in the Symfony catalog for translations
         */
        if ($ret === $originalString && $fallback) {
            $ret = Context::getContext()->getTranslator()->trans($originalString, $sprintf_for_trans, null, $locale);
        }

        return $ret;
    }

    /**
     * Get a translation for a PDF.
     *
     * @param string $string
     * @param array|null $sprintf
     *
     * @return string
     */
    public static function getPdfTranslation($string, $sprintf = null)
    {
        global $_LANGPDF;

        $iso = Context::getContext()->language->iso_code;

        if (!Validate::isLangIsoCode($iso)) {
            Context::getContext()->getTranslator()->trans(
                'Invalid language ISO code (%s)',
                [Tools::safeOutput($iso)],
                'Admin.International.Notification'
            );
        }

        if (!isset($_LANGPDF) || !is_array($_LANGPDF)) {
            return str_replace('"', '&quot;', Translate::checkAndReplaceArgs($string, $sprintf));
        }

        $string = preg_replace("/\\\*'/", "\'", $string);
        $key = md5($string);

        $str = (array_key_exists('PDF' . $key, $_LANGPDF) ? $_LANGPDF['PDF' . $key] : $string);

        if (
            $sprintf !== null &&
            (!is_array($sprintf) || !empty($sprintf)) &&
            !(count($sprintf) === 1 && isset($sprintf['legacy']))
        ) {
            $str = Translate::checkAndReplaceArgs($str, $sprintf);
        }

        return $str;
    }

    /**
     * Check if string use a specif syntax for sprintf and replace arguments if use it.
     *
     * @param string $string
     * @param array $args
     *
     * @return string
     */
    public static function checkAndReplaceArgs($string, $args)
    {
        if (!empty($args) && self::isSprintfString($string)) {
            return vsprintf($string, $args);
        } elseif (!empty($args)) {
            return strtr($string, $args);
        }

        return $string;
    }

    /**
     * Perform operations on translations after everything is escaped and before displaying it.
     */
    public static function postProcessTranslation($string, $params)
    {
        // If tags were explicitely provided, we want to use them *after* the translation string is escaped.
        if (!empty($params['tags'])) {
            foreach ($params['tags'] as $index => $tag) {
                // Make positions start at 1 so that it behaves similar to the %1$d etc. sprintf positional params
                $position = $index + 1;
                // extract tag name
                $match = [];
                if (preg_match('/^\s*<\s*(\w+)/', $tag, $match)) {
                    $opener = $tag;
                    $closer = '</' . $match[1] . '>';

                    $string = str_replace('[' . $position . ']', $opener, $string);
                    $string = str_replace('[/' . $position . ']', $closer, $string);
                    $string = str_replace('[' . $position . '/]', $opener . $closer, $string);
                }
            }
        }

        return $string;
    }

    /**
     * @param string $string
     *
     * @return bool
     */
    private static function isSprintfString($string)
    {
        return (bool) preg_match_all(static::$regexSprintfParams, $string)
            && !(bool) preg_match_all(static::$regexClassicParams, $string);
    }
}
