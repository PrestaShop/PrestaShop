<?php
/**
 * 2007-2018 PrestaShop
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
 * @copyright 2007-2018 PrestaShop SA
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */
global $smarty;
$smarty->debugging = false;
$smarty->debugging_ctrl = 'NONE';

// Let user choose to force compilation
$smarty->force_compile = (Configuration::get('PS_SMARTY_FORCE_COMPILE') == _PS_SMARTY_FORCE_COMPILE_) ? true : false;
// But force compile_check since the performance impact is small and it is better for debugging
$smarty->compile_check = true;

smartyRegisterFunction($smarty, 'function', 'toolsConvertPrice', 'toolsConvertPrice');
smartyRegisterFunction($smarty, 'function', 'convertPrice', array('Product', 'convertPrice'));
smartyRegisterFunction($smarty, 'function', 'convertPriceWithCurrency', array('Product', 'convertPriceWithCurrency'));
smartyRegisterFunction($smarty, 'function', 'displayWtPrice', array('Product', 'displayWtPrice'));
smartyRegisterFunction($smarty, 'function', 'displayWtPriceWithCurrency', array('Product', 'displayWtPriceWithCurrency'));
smartyRegisterFunction($smarty, 'function', 'displayPrice', array('Tools', 'displayPriceSmarty'));
smartyRegisterFunction($smarty, 'modifier', 'convertAndFormatPrice', array('Product', 'convertAndFormatPrice')); // used twice
smartyRegisterFunction($smarty, 'function', 'getAdminToken', array('Tools', 'getAdminTokenLiteSmarty'));
smartyRegisterFunction($smarty, 'function', 'displayAddressDetail', array('AddressFormat', 'generateAddressSmarty'));
smartyRegisterFunction($smarty, 'function', 'getWidthSize', array('Image', 'getWidth'));
smartyRegisterFunction($smarty, 'function', 'getHeightSize', array('Image', 'getHeight'));
smartyRegisterFunction($smarty, 'function', 'addJsDef', array('Media', 'addJsDef'));
smartyRegisterFunction($smarty, 'block', 'addJsDefL', array('Media', 'addJsDefL'));
smartyRegisterFunction($smarty, 'modifier', 'secureReferrer', array('Tools', 'secureReferrer'));

$module_resources['modules'] = _PS_MODULE_DIR_;
$smarty->registerResource('module', new SmartyResourceModule($module_resources, $isAdmin = true));

function toolsConvertPrice($params, &$smarty)
{
    return Tools::convertPrice($params['price'], Context::getContext()->currency);
}

function smartyTranslate($params, &$smarty)
{
    $translator = Context::getContext()->getTranslator();

    $htmlEntities = !isset($params['html']) && !isset($params['js']);
    $addSlashes = (isset($params['slashes']) || isset($params['js']));
    $isInPDF = isset($params['pdf']);
    $isInModule = isset($params['mod']) && !empty($params['mod']);
    $sprintf = array();

    if (isset($params['sprintf']) && null !== $params['sprintf'] && !is_array($params['sprintf'])) {
        $sprintf = array($params['sprintf']);
    } elseif ((isset($params['sprintf']) && null !== $params['sprintf'])) {
        $sprintf = $params['sprintf'];
    }

    if (($htmlEntities || $addSlashes)) {
        $sprintf['legacy'] = $htmlEntities ? 'htmlspecialchars': 'addslashes';
    }

    if (isset($params['d']) && !empty($params['d'])) {
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

        if (!is_array($sprintf)) {
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

        return $translator->trans($params['s'], $sprintf, $params['d']);
    }

    if ($isInPDF) {
        return Translate::smartyPostProcessTranslation(Translate::getPdfTranslation($params['s'], $sprintf), $params);
    }

    $filename = ((!isset($smarty->compiler_object) || !is_object($smarty->compiler_object->template)) ? $smarty->template_resource : $smarty->compiler_object->template->getTemplateFilepath());

    // If the template is part of a module
    if ($isInModule) {
        return Translate::smartyPostProcessTranslation(Translate::getModuleTranslation($params['mod'], $params['s'], basename($filename, '.tpl'), $sprintf, isset($params['js'])), $params);
    }

    $translatedValue = $translator->trans($params['s'], $sprintf, null);

    if ($htmlEntities) {
        $translatedValue = htmlspecialchars($translatedValue, ENT_COMPAT, 'UTF-8');
    }

    if ($addSlashes) {
        $translatedValue = addslashes($translatedValue);
    }

    return $translatedValue;
}
