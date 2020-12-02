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

function ps_1760_copy_data_from_currency_to_currency_lang()
{
    // Force cache reset of languages (load locale column)
    ObjectModel::disableCache();

    $languages = Language::getLanguages();
    foreach ($languages as $language) {
        Db::getInstance()->execute(
            "INSERT INTO `" . _DB_PREFIX_ . "currency_lang` (`id_currency`, `id_lang`, `name`)
            SELECT `id_currency`, " . (int) $language['id_lang'] . " as id_lang , `name`
            FROM `" . _DB_PREFIX_ . "currency`
            ON DUPLICATE KEY UPDATE
            `name` = `" . _DB_PREFIX_ . "currency`.`name`
            "
        );
    }
    /** @var Currency[] $currencies */
    $currencies = Currency::getCurrencies(true, false);
    $context = Context::getContext();
    $container = isset($context->controller) ? $context->controller->getContainer() : null;
    if (null === $container) {
        $container = SymfonyContainer::getInstance();
    }

    /** @var LocaleRepository $localeRepoCLDR */
    $localeRepoCLDR = $container->get('prestashop.core.localization.cldr.locale_repository');
    foreach ($currencies as $currency) {
        refreshLocalizedCurrencyData($currency, $languages, $localeRepoCLDR);
    }

    ObjectModel::enableCache();
}

function refreshLocalizedCurrencyData(Currency $currency, array $languages, LocaleRepository $localeRepoCLDR)
{
    $language = new Language($languages[0]['id_lang']);
    $cldrLocale = $localeRepoCLDR->getLocale($language->locale);
    $cldrCurrency = $cldrLocale->getCurrency($currency->iso_code);

    if (!empty($cldrCurrency)) {
        $fields = [
            'numeric_iso_code' => $cldrCurrency->getNumericIsoCode(),
            'precision' => $cldrCurrency->getDecimalDigits(),
        ];
        Db::getInstance()->update('currency', $fields, 'id_currency = ' . (int) $currency->id);
    }

    foreach ($languages as $languageData) {
        $language = new Language($languageData['id_lang']);
        if (empty($language->locale)) {
            // Language doesn't have locale we can't install this language
            continue;
        }

        // CLDR locale give us the CLDR reference specification
        $cldrLocale = $localeRepoCLDR->getLocale($language->locale);
        // CLDR currency gives data from CLDR reference, for the given language
        $cldrCurrency = $cldrLocale->getCurrency($currency->iso_code);

        if (empty($cldrCurrency)) {
            continue;
        }

        $fields = [
            'name' => $cldrCurrency->getDisplayName(),
            'symbol' => (string) $cldrCurrency->getSymbol() ?: $currency->iso_code
        ];

        $where = 'id_currency = ' . (int) $currency->id
            . ' AND id_lang = ' . (int) $language->id;
        Db::getInstance()->update('currency_lang', $fields, $where);
    }
}
