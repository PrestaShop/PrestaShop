<?php
/**
 * 2007-2017 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
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
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2017 PrestaShop SA
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

/**
 * Class TranslateCore
 *
 * @since 1.5.0
 */
class TranslateCore
{
    public static function getFrontTranslation($string, $class, $addslashes = false, $htmlentities = true, $sprintf = null)
    {
        global $_LANG;

        $string = preg_replace("/\\\*'/", "\'", $string);
        $key = $class.'_'.md5($string);

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
            $sprintf !== null &&
            (!is_array($sprintf) || !empty($sprintf)) &&
            !(count($sprintf) === 1 && isset($sprintf['legacy']))
        ) {
            $str = Translate::checkAndReplaceArgs($str, $sprintf);
        }

        return ($addslashes ? addslashes($str) : stripslashes($str));
    }

    /**
     * Get a translation for an admin controller
     *
     * @param $string
     * @param string $class
     * @param bool $addslashes
     * @param bool $htmlentities
     * @return string
     */
    public static function getAdminTranslation($string, $class = 'AdminTab', $addslashes = false, $htmlentities = true, $sprintf = null)
    {
        static $modulesTabs = null;

        // @todo remove global keyword in translations files and use static
        global $_LANGADM;

        if ($modulesTabs === null) {
            $modulesTabs = Tab::getModuleTabList();
        }

        if ($_LANGADM == null) {
            $iso = Context::getContext()->language->iso_code;
            if (empty($iso)) {
                $iso = Language::getIsoById((int)Configuration::get('PS_LANG_DEFAULT'));
            }
            if (file_exists(_PS_TRANSLATIONS_DIR_.$iso.'/admin.php')) {
                include_once(_PS_TRANSLATIONS_DIR_.$iso.'/admin.php');
            }
        }

        if (isset($modulesTabs[strtolower($class)])) {
            $classNameController = $class.'controller';
            // if the class is extended by a module, use modules/[module_name]/xx.php lang file
            if (class_exists($classNameController) && Module::getModuleNameFromClass($classNameController)) {
                return Translate::getModuleTranslation(Module::$classInModule[$classNameController], $string, $classNameController, $sprintf, $addslashes);
            }
        }

        $string = preg_replace("/\\\*'/", "\'", $string);
        $key = md5($string);
        if (isset($_LANGADM[$class.$key])) {
            $str = $_LANGADM[$class.$key];
        } else {
            $str = Translate::getGenericAdminTranslation($string, $key, $_LANGADM);
        }

        if ($htmlentities) {
            $str = htmlspecialchars($str, ENT_QUOTES, 'utf-8');
        }
        $str = str_replace('"', '&quot;', $str);

        if (
            $sprintf !== null &&
            (!is_array($sprintf) || !empty($sprintf)) &&
            !(count($sprintf) === 1 && isset($sprintf['legacy']))
        ) {
            $str = Translate::checkAndReplaceArgs($str, $sprintf);
        }

        return ($addslashes ? addslashes($str) : stripslashes($str));
    }

    /**
     * Return the translation for a string if it exists for the base AdminController or for helpers
     *
     * @param       $string     string to translate
     * @param null  $key        md5 key if already calculated (optional)
     * @param array $langArray  Global array of admin translations
     *
     * @return string translation
     */
    public static function getGenericAdminTranslation($string, $key, &$langArray)
    {
        $string = preg_replace("/\\\*'/", "\'", $string);
        if (is_null($key)) {
            $key = md5($string);
        }

        if (isset($langArray['AdminController'.$key])) {
            $str = $langArray['AdminController'.$key];
        } elseif (isset($langArray['Helper'.$key])) {
            $str = $langArray['Helper'.$key];
        } elseif (isset($langArray['AdminTab'.$key])) {
            $str = $langArray['AdminTab'.$key];
        } else {
            $str = $string;
        }

        return $str;
    }

    /**
     * Get a translation for a module
     *
     * @param string|Module $module
     * @param string $string
     * @param string $source
     * @return string
     */
    public static function getModuleTranslation($module, $originalString, $source, $sprintf = null, $js = false)
    {
        global $_MODULES, $_MODULE, $_LANGADM;

        static $langCache = array();
        // $_MODULES is a cache of translations for all module.
        // $translations_merged is a cache of wether a specific module's translations have already been added to $_MODULES
        static $translationsMerged = array();

        $name = $module instanceof Module ? $module->name : $module;

        $language = Context::getContext()->language;

        if (!isset($translationsMerged[$name]) && isset(Context::getContext()->language)) {
            $filesByPriority = array(
                // Translations in theme
                _PS_THEME_DIR_.'modules/'.$name.'/translations/'.$language->iso_code.'.php',
                _PS_THEME_DIR_.'modules/'.$name.'/'.$language->iso_code.'.php',
                // PrestaShop 1.5 translations
                _PS_MODULE_DIR_.$name.'/translations/'.$language->iso_code.'.php',
                // PrestaShop 1.4 translations
                _PS_MODULE_DIR_.$name.'/'.$language->iso_code.'.php'
            );
            foreach ($filesByPriority as $file) {
                if (file_exists($file)) {
                    include_once($file);
                    $_MODULES = !empty($_MODULES) ? $_MODULES + $_MODULE : $_MODULE; //we use "+" instead of array_merge() because array merge erase existing values.
                }
            }
            $translationsMerged[$name] = true;
        }
        $string = preg_replace("/\\\*'/", "\'", $originalString);
        $key = md5($string);

        $cacheKey = $name.'|'.$string.'|'.$source.'|'.(int)$js;

        if (isset($langCache[$cacheKey])) {
            $ret = $langCache[$cacheKey];
        } else {
            if ($_MODULES == null) {
                if (
                    $sprintf !== null &&
                    (!is_array($sprintf) || !empty($sprintf)) &&
                    !(count($sprintf) === 1 && isset($sprintf['legacy']))
                ) {
                    $string = Translate::checkAndReplaceArgs($string, $sprintf);
                }

                $ret = str_replace('"', '&quot;', $string);
            }

            $currentKey = strtolower('<{'.$name.'}'._THEME_NAME_.'>'.$source).'_'.$key;
            $defaultKey = strtolower('<{'.$name.'}prestashop>'.$source).'_'.$key;

            if ('controller' == substr($source, -10, 10)) {
                $file = substr($source, 0, -10);
                $currentKeyFile = strtolower('<{'.$name.'}'._THEME_NAME_.'>'.$file).'_'.$key;
                $defaultKeyFile = strtolower('<{'.$name.'}prestashop>'.$file).'_'.$key;
            }

            if (isset($currentKeyFile) && !empty($_MODULES[$currentKeyFile])) {
                $ret = stripslashes($_MODULES[$currentKeyFile]);
            } elseif (isset($defaultKeyFile) && !empty($_MODULES[$defaultKeyFile])) {
                $ret = stripslashes($_MODULES[$defaultKeyFile]);
            } elseif (!empty($_MODULES[$currentKey])) {
                $ret = stripslashes($_MODULES[$currentKey]);
            } elseif (!empty($_MODULES[$defaultKey])) {
                $ret = stripslashes($_MODULES[$defaultKey]);
            } elseif (!empty($_LANGADM)) {
                // if translation was not found in module, look for it in AdminController or Helpers
                $ret = stripslashes(Translate::getGenericAdminTranslation($string, $key, $_LANGADM));
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
            } else {
                $ret = htmlspecialchars($ret, ENT_COMPAT, 'UTF-8');
            }

            if ($sprintf === null) {
                $langCache[$cacheKey] = $ret;
            }
        }

        if (!is_array($sprintf) && !is_null($sprintf)) {
            $sprintf_for_trans = array($sprintf);
        } elseif (is_null($sprintf)) {
            $sprintf_for_trans = array();
        } else {
            $sprintf_for_trans = $sprintf;
        }

        /*
         * Native modules working on both 1.6 & 1.7 are translated in messages.xlf
         * So we need to check in the Symfony catalog for translations
         */
        if ($ret === $originalString) {
            $ret = Context::getContext()->getTranslator()->trans($originalString, $sprintf_for_trans);
        }

        return $ret;
    }

    /**
     * Get a translation for a PDF
     *
     * @param string $string
     * @return string
     */
    public static function getPdfTranslation($string, $sprintf = null)
    {
        global $_LANGPDF;

        $iso = Context::getContext()->language->iso_code;

        if (!Validate::isLangIsoCode($iso)) {
            Context::getContext()->getTranslator()->trans(
                'Invalid language ISO code (%s)',
                array(Tools::safeOutput($iso)),
                'Admin.International.Notification'
            );
        }

        if (!isset($_LANGPDF) || !is_array($_LANGPDF)) {
            return str_replace('"', '&quot;', $string);
        }

        $string = preg_replace("/\\\*'/", "\'", $string);
        $key = md5($string);

        $str = (array_key_exists('PDF'.$key, $_LANGPDF) ? $_LANGPDF['PDF'.$key] : $string);

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
     * Check if string use a specif syntax for sprintf and replace arguments if use it
     *
     * @param $string
     * @param $args
     *
     * @return string
     */
    public static function checkAndReplaceArgs($string, $args)
    {
        if (preg_match_all('#(?:%%|%(?:[0-9]+\$)?[+-]?(?:[ 0]|\'.)?-?[0-9]*(?:\.[0-9]+)?[bcdeufFosxX])#', $string, $matches) && !is_null($args)) {
            if (!is_array($args)) {
                $args = array($args);
            }

            return vsprintf($string, $args);
        }

        return $string;
    }

    /**
    * Perform operations on translations after everything is escaped and before displaying it
    */
    public static function postProcessTranslation($string, $params)
    {
        // If tags were explicitely provided, we want to use them *after* the translation string is escaped.
        if (!empty($params['tags'])) {
            foreach ($params['tags'] as $index => $tag) {
                // Make positions start at 1 so that it behaves similar to the %1$d etc. sprintf positional params
                $position = $index + 1;
                // extract tag name
                $match = array();
                if (preg_match('/^\s*<\s*(\w+)/', $tag, $match)) {
                    $opener = $tag;
                    $closer = '</'.$match[1].'>';

                    $string = str_replace('['.$position.']', $opener, $string);
                    $string = str_replace('[/'.$position.']', $closer, $string);
                    $string = str_replace('['.$position.'/]', $opener.$closer, $string);
                }
            }
        }

        return $string;
    }

    /**
     * Compatibility method that just calls postProcessTranslation.
     * @deprecated renamed this to postProcessTranslation, since it is not only used in relation to smarty.
     */
    public static function smartyPostProcessTranslation($string, $params)
    {
        return Translate::postProcessTranslation($string, $params);
    }

    /**
     * Helper function to make calls to postProcessTranslation more readable.
     * @deprecated 1.7.1.0
     */
    public static function ppTags($string, $tags)
    {
        Tools::displayAsDeprecated();
        return Translate::postProcessTranslation($string, array('tags' => $tags));
    }
}
