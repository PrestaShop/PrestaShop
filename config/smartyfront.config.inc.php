<?php
/**
 * 2007-2017 PrestaShop
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
 * @copyright 2007-2017 PrestaShop SA
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */
global $smarty;

$template_dirs = array(_PS_THEME_DIR_.'templates');
$plugin_dirs = array(_PS_THEME_DIR_.'plugins');
if (_PS_PARENT_THEME_DIR_) {
    $template_dirs[] = _PS_PARENT_THEME_DIR_.'templates';
    $plugin_dirs[] = _PS_PARENT_THEME_DIR_.'plugins';
}

$smarty->setTemplateDir($template_dirs);
$smarty->addPluginsDir($plugin_dirs);

$module_resources = array('theme' => _PS_THEME_DIR_.'modules/');
if (_PS_PARENT_THEME_DIR_) {
    $module_resources['parent'] = _PS_PARENT_THEME_DIR_.'modules/';
}
$module_resources['modules'] = _PS_MODULE_DIR_;
$smarty->registerResource('module', new SmartyResourceModule($module_resources));

$parent_resources = array();
if (_PS_PARENT_THEME_DIR_) {
    $parent_resources['parent'] = _PS_PARENT_THEME_DIR_.'templates/';
}
$smarty->registerResource('parent', new SmartyResourceParent($parent_resources));

$smarty->escape_html = true;

smartyRegisterFunction($smarty, 'function', 'widget', 'smartyWidget');
smartyRegisterFunction($smarty, 'function', 'render', 'smartyRender');
smartyRegisterFunction($smarty, 'function', 'form_field', 'smartyFormField');
smartyRegisterFunction($smarty, 'block', 'widget_block', 'smartyWidgetBlock');

function withWidget($params, callable $cb)
{
    if (!isset($params['name'])) {
        throw new Exception('Smarty helper `render_widget` expects at least the `name` parameter.');
    }

    $moduleName = $params['name'];
    unset($params['name']);

    $moduleInstance = Module::getInstanceByName($moduleName);

    if (!$moduleInstance instanceof PrestaShop\PrestaShop\Core\Module\WidgetInterface) {
        throw new Exception(sprintf(
            'Module `%1$s` is not a WidgetInterface.',
            $moduleName
        ));
    }

    return $cb($moduleInstance, $params);
}

function smartyWidget($params, &$smarty)
{
    return withWidget($params, function ($widget, $params) {
        return $widget->renderWidget(null, $params);
    });
}

function smartyRender($params, &$smarty)
{
    $ui = $params['ui'];

    if (array_key_exists('file', $params)) {
        $ui->setTemplate($params['file']);
    }

    return $ui->render($params);
}

function smartyFormField($params, &$smarty)
{
    $scope = $smarty->createData(
        $smarty
    );

    $scope->assign($params);

    $file = '_partials/form-fields.tpl';

    if (isset($params['file'])) {
        $file = $params['file'];
    }

    $tpl = $smarty->createTemplate($file, $scope);

    return $tpl->fetch();
}

function smartyWidgetBlock($params, $content, &$smarty)
{
    static $backedUpVariablesStack = array();

    if (null === $content) {
        // Function is called twice: at the opening of the block
        // and when it is closed.
        // This is the first call.
        withWidget($params, function ($widget, $params) use (&$smarty, &$backedUpVariablesStack) {
            // Assign widget variables and backup all the variables they override
            $currentVariables = $smarty->getTemplateVars();
            $scopedVariables = $widget->getWidgetVariables(null, $params);
            $backedUpVariables = array();
            foreach ($scopedVariables as $key => $value) {
                if (array_key_exists($key, $currentVariables)) {
                    $backedUpVariables[$key] = $currentVariables[$key];
                }
                $smarty->assign($key, $value);
            }
            $backedUpVariablesStack[] = $backedUpVariables;
        });
        // We don't display anything since the template is not rendered yet.
        return '';
    } else {
        // Function gets called for the closing tag of the block.
        // We restore the backed up variables in order not to override
        // template variables.
        if (!empty($backedUpVariablesStack)) {
            $backedUpVariables = array_pop($backedUpVariablesStack);
            foreach ($backedUpVariables as $key => $value) {
                $smarty->assign($key, $value);
            }
        }
        // This time content is filled with rendered template, so return it.
        return $content;
    }
}

function smartyTranslate($params, &$smarty)
{
    global $_LANG;

    if (!isset($params['js'])) {
        $params['js'] = false;
    }
    if (!isset($params['pdf'])) {
        $params['pdf'] = false;
    }
    if (!isset($params['mod'])) {
        $params['mod'] = false;
    }
    if (!isset($params['sprintf'])) {
        $params['sprintf'] = array();
    }
    if (!isset($params['d'])) {
        $params['d'] = null;
    }

    if (!is_null($params['d'])) {
        if (isset($params['tags'])) {
            $backTrace = debug_backtrace();

            $errorMessage = sprintf(
                'Unable to translate "%s" in %s. tags() is not supported anymore, please use sprintf().',
                $params['s'],
                $backTrace[0]['args'][1]->template_resource
            );

            if (_PS_MODE_DEV_) {
                throw new Exception($errorMessage);
            } else {
                PrestaShopLogger::addLog($errorMessage);
            }
        }

        if (!is_array($params['sprintf'])) {
            $backTrace = debug_backtrace();

            $errorMessage = sprintf(
                'Unable to translate "%s" in %s. sprintf() parameter should be an array.',
                $params['s'],
                $backTrace[0]['args'][1]->template_resource
            );

            if (_PS_MODE_DEV_) {
                throw new Exception($errorMessage);
            } else {
                PrestaShopLogger::addLog($errorMessage);

                return $params['s'];
            }
        }
    }

    if (($translation = Context::getContext()->getTranslator()->trans($params['s'], $params['sprintf'], $params['d'])) !== $params['s']
        && $params['mod'] === false) {
        return $translation;
    }

    $string = str_replace('\'', '\\\'', $params['s']);
    $filename = ((!isset($smarty->compiler_object) || !is_object($smarty->compiler_object->template)) ? $smarty->template_resource : $smarty->compiler_object->template->getTemplateFilepath());

    $basename = basename($filename, '.tpl');
    $key = $basename.'_'.md5($string);

    if (isset($smarty->source) && (strpos($smarty->source->filepath, DIRECTORY_SEPARATOR.'override'.DIRECTORY_SEPARATOR) !== false)) {
        $key = 'override_'.$key;
    }

    if ($params['mod']) {
        return Translate::smartyPostProcessTranslation(Translate::getModuleTranslation($params['mod'], $params['s'], $basename, $params['sprintf'], $params['js']), $params);
    } elseif ($params['pdf']) {
        return Translate::smartyPostProcessTranslation(Translate::getPdfTranslation($params['s'], $params['sprintf']), $params);
    }

    if ($_LANG != null && isset($_LANG[$key])) {
        $msg = $_LANG[$key];
    } elseif ($_LANG != null && isset($_LANG[Tools::strtolower($key)])) {
        $msg = $_LANG[Tools::strtolower($key)];
    } else {
        $msg = $params['s'];
    }

    if ($msg != $params['s'] && !$params['js']) {
        $msg = stripslashes($msg);
    } elseif ($params['js']) {
        $msg = addslashes($msg);
    }

    if ($params['sprintf'] !== null) {
        $msg = Translate::checkAndReplaceArgs($msg, $params['sprintf']);
    }

    return Translate::smartyPostProcessTranslation($params['js'] ? $msg : Tools::safeOutput($msg), $params);
}
