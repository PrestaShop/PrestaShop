<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
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
smartyRegisterFunction($smarty, 'function', 'isBrightColor', 'isBrightColor');
smartyRegisterFunction($smarty, 'block', 'addJsDefL', array('Media', 'addJsDefL'));
smartyRegisterFunction($smarty, 'modifier', 'secureReferrer', array('Tools', 'secureReferrer'));

$module_resources['modules'] = _PS_MODULE_DIR_;
$smarty->registerResource('module', new SmartyResourceModule($module_resources, $isAdmin = true));

function isBrightColor(string $params): bool {
    $colorBrightnessCalculator = new PrestaShop\PrestaShop\Core\Util\ColorBrightnessCalculator();
    return $colorBrightnessCalculator->isBright($params);
}

function toolsConvertPrice($params, &$smarty)
{
    return Tools::convertPrice($params['price'], Context::getContext()->currency);
}

function smartyTranslate($params, $smarty)
{
    $translator = Context::getContext()->getTranslator();

    $htmlEntities = !isset($params['html']) && !isset($params['js']);
    $addSlashes = (isset($params['slashes']) || isset($params['js']));
    $isInPDF = isset($params['pdf']);
    $isInModule = !empty($params['mod']);
    $sprintf = array();

    if (isset($params['sprintf']) && !is_array($params['sprintf'])) {
        $sprintf = array($params['sprintf']);
    } elseif (isset($params['sprintf'])) {
        $sprintf = $params['sprintf'];
    }

    if ($isInPDF && empty($params['d'])) {
        return Translate::postProcessTranslation(
            Translate::getPdfTranslation(
                $params['s'],
                $sprintf
            ),
            $params
        );
    }

    // If the template is part of a module
    if ($isInModule && empty($params['d'])) {
        return Translate::postProcessTranslation(
            Translate::getModuleTranslation(
                $params['mod'],
                $params['s'],
                basename($smarty->source->name, '.tpl'),
                $sprintf,
                isset($params['js'])
            ),
            $params
        );
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

        $translatedValue = $translator->trans($params['s'], $sprintf, $params['d']);
    } else {
        $translatedValue = $translator->trans($params['s'], $sprintf, null);
    }

    if ($htmlEntities) {
        $translatedValue = htmlspecialchars($translatedValue, ENT_COMPAT, 'UTF-8');
    }

    if ($addSlashes) {
        $translatedValue = addslashes($translatedValue);
    }

    return $translatedValue;
}
