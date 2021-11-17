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

namespace Tests\Integration\Behaviour\Features\Context;

use Currency;
use Language;
use PrestaShop\PrestaShop\Core\Localization\CLDR\LocaleRepository;
use PrestaShop\PrestaShop\Core\Localization\Locale\RepositoryInterface;
use RuntimeException;

class CLDRFeatureContext extends AbstractPrestaShopFeatureContext
{
    /**
     * @Given currency :reference with ISO code :isoCode exists
     */
    public function createOfficialCurrencyWithIsoCode($reference, $isoCode)
    {
        $this->createCurrencyWithIsoCode($reference, $isoCode, false);
    }

    /**
     * @Given currency :reference with unofficial ISO code :isoCode exists
     */
    public function createUnofficialCurrencyWithIsoCode($reference, $isoCode)
    {
        $this->createCurrencyWithIsoCode($reference, $isoCode, true);
    }

    /**
     * @param string $reference
     * @param string $isoCode
     * @param bool $unofficial
     */
    private function createCurrencyWithIsoCode(string $reference, string $isoCode, bool $unofficial)
    {
        /*
         * Currency::getIdByIsoCode only returns not deleted currency so we check the storage to avoid
         * duplicate contents, if it matches the expected iso code then we do nothing
         */
        if (SharedStorage::getStorage()->exists($reference)) {
            $currency = $this->getCurrency($reference);
            if ($currency->iso_code == $isoCode) {
                return;
            }
        }

        $currencyId = Currency::getIdByIsoCode($isoCode, 0, true);

        if (!$currencyId) {
            $currency = new Currency();
            $currency->name = $isoCode;
            $currency->iso_code = $isoCode;
            $currency->active = 1;
            $currency->deleted = false;
            $currency->conversion_rate = 1;
            $currency->precision = 2;
            $currency->unofficial = $unofficial;
            $currency->add();

            /** @var LocaleRepository $localeRepository */
            $localeRepository = CommonFeatureContext::getContainer()->get('prestashop.core.localization.cldr.locale_repository');
            $currency->refreshLocalizedCurrencyData(Language::getLanguages(), $localeRepository);
            $currency->save();
        } else {
            $currency = new Currency($currencyId);
        }

        SharedStorage::getStorage()->set($reference, (int) $currency->id);
    }

    /**
     * @Then a price of :price using :currencyIsoCode in locale :locale should look like :expectedPrice
     */
    public function assertDisplayPrice($price, $currencyIsoCode, $locale, $expectedPrice)
    {
        /** @var RepositoryInterface $localeRepository */
        $localeRepository = CommonFeatureContext::getContainer()->get('prestashop.core.localization.locale.repository');
        $locale = $localeRepository->getLocale($locale);
        $displayedPrice = $locale->formatPrice($price, $currencyIsoCode);

        if ($expectedPrice !== $displayedPrice) {
            throw new RuntimeException(sprintf('Displayed price is "%s" but "%s" was expected', $displayedPrice, $expectedPrice));
        }
    }

    /**
     * @param string $reference
     *
     * @return Currency
     */
    private function getCurrency(string $reference): Currency
    {
        return new Currency(SharedStorage::getStorage()->get($reference));
    }
}
