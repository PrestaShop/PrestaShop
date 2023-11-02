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
global $smarty;

use PrestaShop\TranslationToolsBundle\Translation\Helper\DomainHelper;

$template_dirs = array(_PS_THEME_DIR_.'templates');
$plugin_dirs = array(_PS_THEME_DIR_.'plugins');
if (_PS_PARENT_THEME_DIR_ !== '') {
    $template_dirs[] = _PS_PARENT_THEME_DIR_.'templates';
    $plugin_dirs[] = _PS_PARENT_THEME_DIR_.'plugins';
}

$smarty->setTemplateDir($template_dirs);
$smarty->addPluginsDir($plugin_dirs);

$module_resources = array('theme' => _PS_THEME_DIR_.'modules/');
if (_PS_PARENT_THEME_DIR_ !== '') {
    $module_resources['parent'] = _PS_PARENT_THEME_DIR_.'modules/';
}
$module_resources['modules'] = _PS_MODULE_DIR_;
$smarty->registerResource('module', new SmartyResourceModule($module_resources));

$parent_resources = array();
if (_PS_PARENT_THEME_DIR_ !== '') {
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
        return Hook::coreRenderWidget(
            $widget,
            isset($params['hook']) ? $params['hook'] : null,
            $params
        );
    });
}

function smartyRender($params, &$smarty)
{
    // Check if proper object was passed
    if (empty($params['ui']) || !method_exists($params['ui'], 'render')) {
        if (_PS_MODE_DEV_) {
            trigger_error(
                sprintf(
                    'When using {render}, you must provide proper `ui` parameter with the form. Template - %1$s',
                    $smarty->source->filepath
                ),
                E_USER_NOTICE
            );
        }
        return;
    }

    $ui = $params['ui'];

    // If specific template file was provided, we pass it along
    if (!empty($params['file'])) {
        // Ignoring the next line because PHPStan is not aware of the object passed
        /** @phpstan-ignore-next-line */
        $ui->setTemplate($params['file']);
    }

    return $ui->render($params);
}

function smartyFormField($params, $smarty)
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

function smartyWidgetBlock($params, $content, $smarty)
{
    static $backedUpVariablesStack = array();

    if (null === $content) {
        // Function is called twice: at the opening of the block
        // and when it is closed.
        // This is the first call.
        withWidget($params, function ($widget, $params) use (&$smarty, &$backedUpVariablesStack) {
            // Assign widget variables and backup all the variables they override
            $currentVariables = $smarty->getTemplateVars();
            $scopedVariables = $widget->getWidgetVariables(isset($params['hook']) ? $params['hook'] : null, $params);
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

function smartyTranslate($params, $smarty)
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

    // fix inheritance template filename in case of includes from different cross sources between theme, modules, ...
    $filename = $smarty->template_resource;
    if (!isset($smarty->inheritance->sourceStack[0]) || $filename === $smarty->inheritance->sourceStack[0]->resource) {
        $filename = $smarty->source->name;
    }
    $basename = basename($filename, '.tpl');

    if (!isset($params['d'])) {
        $params['d'] = null;
    }

    if (!empty($params['d'])) {
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

        return Context::getContext()->getTranslator()->trans($params['s'], $params['sprintf'], $params['d']);
    }

    $string = str_replace('\'', '\\\'', $params['s']);
    $key = $basename.'_'.md5($string);

    if (isset($smarty->source) && (strpos($smarty->source->filepath, DIRECTORY_SEPARATOR.'override'.DIRECTORY_SEPARATOR) !== false)) {
        $key = 'override_'.$key;
    }

    if ($params['mod']) {
        return Translate::postProcessTranslation(
            Translate::getModuleTranslation(
                $params['mod'],
                $params['s'],
                $basename,
                $params['sprintf'],
                $params['js']
            ),
            $params
        );
    } elseif ($params['pdf']) {
        return Translate::postProcessTranslation(
            Translate::getPdfTranslation(
                $params['s'],
                $params['sprintf']
            ),
            $params
        );
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

    return Translate::postProcessTranslation($params['js'] ? $msg : Tools::safeOutput($msg), $params);
}
