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

use PrestaShop\PrestaShop\Adapter\SymfonyContainer;
use PrestaShop\PrestaShop\Core\Localization\CLDR\LocaleRepository;

/**
 * On PrestaShop 1.7.6.0, two new columns have been introduced in the PREFIX_currency table: precision & numeric_iso_code
 * A fresh install would add the proper data in these columns, however an upgraded shop to 1.7.6.0 would not get the
 * corresponding values of each currency.
 *
 * This upgrade script will cover this need by loading the CLDR data and update the currency if it still has the default table values.
 */
function ps_1761_update_currencies()
{
    // Force cache reset of languages (load locale column)
    ObjectModel::disableCache();

    /** @var Currency[] $currencies */
    $currencies = Currency::getCurrencies(true, false);
    $context = Context::getContext();
    $container = isset($context->controller) ? $context->controller->getContainer() : null;
    if (null === $container) {
        $container = SymfonyContainer::getInstance();
    }

    /** @var LocaleRepository $localeRepoCLDR */
    $localeRepoCLDR = $container->get('prestashop.core.localization.cldr.locale_repository');
    // CLDR locale give us the CLDR reference specification
    $cldrLocale = $localeRepoCLDR->getLocale($context->language->getLocale());

    foreach ($currencies as $currency) {
        if ((int) $currency->precision !== 6 || !empty((int) $currency->numeric_iso_code)) {
            continue;
        }
        // CLDR currency gives data from CLDR reference, for the given language
        $cldrCurrency = $cldrLocale->getCurrency($currency->iso_code);
        if (!empty($cldrCurrency)) {
            $currency->precision = (int) $cldrCurrency->getDecimalDigits();
            $currency->numeric_iso_code = $cldrCurrency->getNumericIsoCode();
        }
        Db::getInstance()->execute(
            'UPDATE `' . _DB_PREFIX_ . 'currency`
            SET `precision` = ' . $currency->precision . ', `numeric_iso_code` = ' . $currency->numeric_iso_code . '
            WHERE `id_currency` = ' . $currency->id
        );
    }

    ObjectModel::enableCache();
}
