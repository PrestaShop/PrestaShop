<?php
/**
 * 2007-2015 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
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
 * @copyright 2007-2015 PrestaShop SA
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShop\PrestaShop\Foundation\View\SmartyPlugins;

/**
 * This Trait will add smarty functions for plugins
 */
trait FunctionsTrait
{
    public function smartyTranslateAdmin($params, &$smarty)
    {
        // FIXME: tout est legacy ici, déplacer vers Adapter ? ou sous-couche Adapter en plus ?
        $htmlentities = !isset($params['js']);
        $pdf = isset($params['pdf']);
        $addslashes = (isset($params['slashes']) || isset($params['js']));
        $sprintf = isset($params['sprintf']) ? $params['sprintf'] : null;

        if ($pdf) {
            return \Translate::smartyPostProcessTranslation(Translate::getPdfTranslation($params['s'], $sprintf), $params);
        }

        $filename = ((!isset($smarty->compiler_object) || !is_object($smarty->compiler_object->template)) ? $smarty->template_resource : $smarty->compiler_object->template->getTemplateFilepath());

        // If the template is part of a module
        if (!empty($params['mod'])) {
            return \Translate::smartyPostProcessTranslation(\Translate::getModuleTranslation($params['mod'], $params['s'], basename($filename, '.tpl'), $sprintf, isset($params['js'])), $params);
        }

        // If the tpl is at the root of the template folder
        if (dirname($filename) == '.') {
            $class = 'index';
        }

        // If the tpl is used by a Helper
        if (strpos($filename, 'helpers') === 0) {
            $class = 'Helper';
        } else {
            // If the tpl is used by a Controller

            if (!empty(\Context::getContext()->override_controller_name_for_translations)) {
                $class = \Context::getContext()->override_controller_name_for_translations;
            } elseif (isset(\Context::getContext()->controller)) {
                $class_name = get_class(\Context::getContext()->controller);
                $class = substr($class_name, 0, strpos(\Tools::strtolower($class_name), 'controller'));
            } else {
                // Split by \ and / to get the folder tree for the file
                $folder_tree = preg_split('#[/\\\]#', $filename);
                $key = array_search('controllers', $folder_tree);

                // If there was a match, construct the class name using the child folder name
                // Eg. xxx/controllers/customers/xxx => AdminCustomers
                if ($key !== false) {
                    $class = 'Admin'.Tools::toCamelCase($folder_tree[$key + 1], true);
                } elseif (isset($folder_tree[0])) {
                    $class = 'Admin'.Tools::toCamelCase($folder_tree[0], true);
                }
            }
        }
        return \Translate::smartyPostProcessTranslation(\Translate::getAdminTranslation($params['s'], $class, $addslashes, $htmlentities, $sprintf), $params);
    }

    public function smartyTranslateFront($params, &$smarty)
    {
        // FIXME: tout est legacy ici, déplacer vers Adapter ? ou sous-couche Adapter en plus ?
        $htmlentities = !isset($params['js']);
        $pdf = isset($params['pdf']);
        $addslashes = (isset($params['slashes']) || isset($params['js']));
        $sprintf = isset($params['sprintf']) ? $params['sprintf'] : null;

        if ($pdf) {
            return \Translate::smartyPostProcessTranslation(\Translate::getPdfTranslation($params['s'], $sprintf), $params);
        }

        $filename = ((!isset($smarty->compiler_object) || !is_object($smarty->compiler_object->template)) ? $smarty->template_resource : $smarty->compiler_object->template->getTemplateFilepath());

        // If the template is part of a module
        if (!empty($params['mod'])) {
            return \Translate::smartyPostProcessTranslation(\Translate::getModuleTranslation($params['mod'], $params['s'], basename($filename, '.tpl'), $sprintf, isset($params['js'])), $params);
        }

        // If the tpl is at the root of the template folder
        if (dirname($filename) == '.') {
            $class = 'index';
        }

        // If the tpl is used by a Helper
        if (strpos($filename, 'helpers') === 0) {
            $class = 'Helper';
        } else {
            // If the tpl is used by a Controller

            if (!empty(\Context::getContext()->override_controller_name_for_translations)) {
                $class = \Context::getContext()->override_controller_name_for_translations;
            } elseif (isset(\Context::getContext()->controller)) {
                $class_name = get_class(\Context::getContext()->controller);
                $class = substr($class_name, 0, strpos(\Tools::strtolower($class_name), 'controller'));
            } else {
                // Split by \ and / to get the folder tree for the file
                $folder_tree = preg_split('#[/\\\]#', $filename);
                $key = array_search('controllers', $folder_tree);

                // If there was a match, construct the class name using the child folder name
                // Eg. xxx/controllers/customers/xxx => AdminCustomers
                if ($key !== false) {
                    $class = 'Admin'.\Tools::toCamelCase($folder_tree[$key + 1], true);
                } elseif (isset($folder_tree[0])) {
                    $class = 'Admin'.\Tools::toCamelCase($folder_tree[0], true);
                }
            }
        }

        return \Translate::smartyPostProcessTranslation(\Translate::getAdminTranslation($params['s'], $class, $addslashes, $htmlentities, $sprintf), $params);
    }

    public function smartyDieObject($params)
    {
        return \Tools::d($params['var']);
    }

    public function smartyShowObject($params)
    {
        return \Tools::p($params['var']);
    }

    public function smartyMaxWords($params)
    {
        \Tools::displayAsDeprecated();
        $params['s'] = str_replace('...', ' ...', html_entity_decode($params['s'], ENT_QUOTES, 'UTF-8'));
        $words = explode(' ', $params['s']);

        foreach ($words as &$word) {
            if (\Tools::strlen($word) > $params['n']) {
                $word = \Tools::substr(trim(chunk_split($word, $params['n']-1, '- ')), 0, -1);
            }
        }

        return implode(' ', \Tools::htmlentitiesUTF8($words));
    }

    public function smartyTruncate($params)
    {
        \Tools::displayAsDeprecated();
        $text = isset($params['strip']) ? strip_tags($params['text']) : $params['text'];
        $length = $params['length'];
        $sep = isset($params['sep']) ? $params['sep'] : '...';

        if (Tools::strlen($text) > $length + \Tools::strlen($sep)) {
            $text = \Tools::substr($text, 0, $length).$sep;
        }

        return (isset($params['encode']) ? \Tools::htmlentitiesUTF8($text, ENT_NOQUOTES) : $text);
    }

    public function smarty_modifier_truncate($string, $length = 80, $etc = '...', $break_words = false, $middle = false, $charset = 'UTF-8')
    {
        if (!$length) {
            return '';
        }

        $string = trim($string);

        if (\Tools::strlen($string) > $length) {
            $length -= min($length, \Tools::strlen($etc));
            if (!$break_words && !$middle) {
                $string = preg_replace('/\s+?(\S+)?$/u', '', \Tools::substr($string, 0, $length+1, $charset));
            }
            return !$middle ? \Tools::substr($string, 0, $length, $charset).$etc : Tools::substr($string, 0, $length/2, $charset).$etc.\Tools::substr($string, -$length/2, $length, $charset);
        } else {
            return $string;
        }
    }

    public function smarty_modifier_htmlentitiesUTF8($string)
    {
        return \Tools::htmlentitiesUTF8($string);
    }
    public function smartyMinifyHTML($tpl_output)
    {
        $context = \Context::getContext();
        if (isset($context->controller) && in_array($context->controller->php_self, array('pdf-invoice', 'pdf-order-return', 'pdf-order-slip'))) {
            return $tpl_output;
        }
        $tpl_output = \Media::minifyHTML($tpl_output);
        return $tpl_output;
    }

    public function smartyPackJSinHTML($tpl_output)
    {
        $context = \Context::getContext();
        if (isset($context->controller) && in_array($context->controller->php_self, array('pdf-invoice', 'pdf-order-return', 'pdf-order-slip'))) {
            return $tpl_output;
        }
        $tpl_output = \Media::packJSinHTML($tpl_output);
        return $tpl_output;
    }

    public function smartyHook($params, &$smarty)
    {
        if (!empty($params['h'])) {
            $id_module = null;
            $hook_params = $params;
            $hook_params['smarty'] = $smarty;
            if (!empty($params['mod'])) {
                $module = \Module::getInstanceByName($params['mod']);
                if ($module && $module->id) {
                    $id_module = $module->id;
                }
                unset($hook_params['mod']);
            }
            unset($hook_params['h']);
            return \Hook::exec($params['h'], $hook_params, $id_module);
        }
    }

    public function smartyCleanHtml($data)
    {
        // Prevent xss injection.
        if (\Validate::isCleanHtml($data)) {
            return $data;
        }
    }

    public function toolsConvertPrice($params)
    {
        return \Tools::convertPrice($params['price'], \Context::getContext()->currency);
    }
}
