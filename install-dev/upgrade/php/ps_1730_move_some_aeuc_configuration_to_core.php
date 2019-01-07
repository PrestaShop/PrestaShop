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

function ps_1730_move_some_aeuc_configuration_to_core()
{
    $translator = Context::getContext()->getTranslator();

    $labelInStock = array();
    $labelOOSProductsBOA = array();
    $labelOOSProductsBOD = array();
    $deliveryTimeAvailable = array();
    $deliveryTimeOutOfStockBackorderAllowed = array();

    foreach (Language::getLanguages() as $language) {
        $labelInStock[$language['id_lang']] = $translator->trans('In Stock', array(), 'Admin.Shopparameters.Feature', $language['locale']);
        $labelOOSProductsBOA[$language['id_lang']] = $translator->trans('Product available for orders', array(), 'Admin.Shopparameters.Feature', $language['locale']);
        $labelOOSProductsBOD[$language['id_lang']] = $translator->trans('Out-of-Stock', array(), 'Admin.Shopparameters.Feature', $language['locale']);

        if ($value = Configuration::get('AEUC_LABEL_DELIVERY_TIME_AVAILABLE', $language['id_lang'])) {
            $deliveryTimeAvailable[$language['id_lang']] = $value;
        }

        if ($value = Configuration::get('AEUC_LABEL_DELIVERY_TIME_OOS', $language['id_lang'])) {
            $deliveryTimeOutOfStockBackorderAllowed[$language['id_lang']] = $value;
        }
    }

    Configuration::updateValue('PS_LABEL_IN_STOCK_PRODUCTS', $labelInStock);
    Configuration::updateValue('PS_LABEL_OOS_PRODUCTS_BOA', $labelOOSProductsBOA);
    Configuration::updateValue('PS_LABEL_OOS_PRODUCTS_BOD', $labelOOSProductsBOD);
    Configuration::updateValue('PS_LABEL_DELIVERY_TIME_AVAILABLE', $deliveryTimeAvailable);
    Configuration::updateValue('PS_LABEL_DELIVERY_TIME_OOSBOA', $deliveryTimeOutOfStockBackorderAllowed);
}
