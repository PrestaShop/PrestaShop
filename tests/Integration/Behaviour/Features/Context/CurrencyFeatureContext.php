<?php
/**
 * 2007-2019 PrestaShop SA and Contributors
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
 * needs please refer to https://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2019 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace Tests\Integration\Behaviour\Features\Context;

use Context;
use Currency;
use Configuration;
use RuntimeException;

class CurrencyFeatureContext extends AbstractPrestaShopFeatureContext
{
    use CartAwareTrait;

    /**
     * @var Currency[]
     */
    protected $currencies = [];

    protected $previousDefaultCurrencyId;

    /**
     * @BeforeScenario
     */
    public function storePreviousCurrencyId()
    {
        $this->previousDefaultCurrencyId = Configuration::get('PS_CURRENCY_DEFAULT');
    }

    /**
     * This hook can be used to perform a database cleaning of added objects
     *
     * @AfterScenario
     */
    public function cleanCurrencyFixtures()
    {
        Configuration::set('PS_CURRENCY_DEFAULT', $this->previousDefaultCurrencyId);
        foreach ($this->currencies as $currency) {
            $currency->delete();
        }
        $this->currencies = [];
    }

    /**
     * @Given /^there is a currency named "(.+)" with iso code "(.+)" and exchange rate of (\d+\.\d+)$/
     */
    public function thereIsACurrency($currencyName, $currencyIsoCode, $changeRate)
    {
        $currencyId = Currency::getIdByIsoCode($currencyIsoCode);
        // soft delete here...
        if (!$currencyId) {
            $currency = new Currency();
            $currency->name = $currencyIsoCode;
            $currency->precision = 2;
            $currency->iso_code = $currencyIsoCode;
            $currency->active = 1;
            $currency->conversion_rate = $changeRate;
            $currency->add();
        } else {
            $currency = new Currency($currencyId);
            $currency->name = $currencyIsoCode;
            $currency->precision = 2;
            $currency->active = 1;
            $currency->conversion_rate = $changeRate;
            $currency->save();
        }
        $this->currencies[$currencyName] = $currency;
    }

    /**
     * @Given /^currency "(.+)" is the default one$/
     */
    public function setDefaultCurrency($currencyName)
    {
        $this->checkCurrencyWithNameExists($currencyName);
        Configuration::set('PS_CURRENCY_DEFAULT', $this->currencies[$currencyName]->id);
    }

    /**
     * @Given /^no currency is set as the current one$/
     */
    public function setNoCurrentCurrency()
    {
        $this->getCurrentCart()->id_currency = 0;
    }

    /**
     * @Given /^currency "(.+)" is the current one$/
     */
    public function setCurrentCurrency($currencyName)
    {
        $this->checkCurrencyWithNameExists($currencyName);
        $this->getCurrentCart()->id_currency = $this->currencies[$currencyName]->id;
        Context::getContext()->currency = $this->currencies[$currencyName];
    }

    /**
     * @param $cartRuleName
     */
    public function checkCurrencyWithNameExists($currencyName)
    {
        $this->checkFixtureExists($this->currencies, 'Currency', $currencyName);
    }

    /**
     * @Then currency :reference should be :isoCode
     */
    public function assertCurrencyIsoCode($reference, $isoCode)
    {
        /** @var Currency $currency */
        $currency = SharedStorage::getStorage()->get($reference);

        if ($currency->iso_code !== $isoCode) {
            throw new RuntimeException(sprintf(
                'Currency "%s" has "%s" iso code, but "%s" was expected.',
                $reference,
                $currency->iso_code,
                $isoCode
            ));
        }
    }

    /**
     * @Then /^currency "(.*)" should have status (enabled|disabled)$/
     */
    public function assertCurrencyStatus($reference, $status)
    {
        /** @var Currency $currency */
        $currency = SharedStorage::getStorage()->get($reference);
        $expectedStatus = $status === 'enabled';

        if ($currency->active != $expectedStatus) {
            throw new RuntimeException(sprintf(
                'Currency "%s" has status "%s", but "%s" was expected.',
                $reference,
                $currency->active,
                $expectedStatus
            ));
        }
    }

    /**
     * @Then currency :reference exchange rate should be :exchangeRate
     */
    public function assertCurrencyExchangeRate($reference, $exchangeRate)
    {
        /** @var Currency $currency */
        $currency = SharedStorage::getStorage()->get($reference);

        if ((float) $currency->conversion_rate != (float) $exchangeRate) {
            throw new RuntimeException(sprintf(
                'Currency "%s" has "%s" exchange rate, but "%s" was expected.',
                $reference,
                $currency->conversion_rate,
                $exchangeRate
            ));
        }
    }

    /**
     * @Then currency :currencyReference should be available in shop :shopReference
     */
    public function assertCurrencyIsAvailableInShop($currencyReference, $shopReference)
    {
        /** @var Currency $currency */
        $currency = SharedStorage::getStorage()->get($currencyReference);
        /** @var \Shop $shop */
        $shop = SharedStorage::getStorage()->get($shopReference);

        if (!in_array($shop->id, $currency->getAssociatedShops())) {
            throw new RuntimeException(sprintf(
                'Currency "%s" is not associated with "%s" shop',
                $currencyReference,
                $shopReference
            ));
        }
    }

    /**
     * @Given currency :reference with :isoCode exists
     */
    public function assertCurrencyExists($reference, $isoCode)
    {
        $currencyId = Currency::getIdByIsoCode($isoCode);

        if (!$currencyId) {
            throw new RuntimeException(sprintf('Currency with ISO Code "%s" does not exist', $isoCode));
        }

        SharedStorage::getStorage()->set($reference, new Currency($currencyId));
    }

    /**
     * @Given currency :currencyReference is default in :shopReference shop
     */
    public function assertCurrencyIsDefaultInShop($currencyReference, $shopReference)
    {
        /** @var Currency $currency */
        $currency = SharedStorage::getStorage()->get($currencyReference);
        /** @var \Shop $shop */
        $shop = SharedStorage::getStorage()->get($shopReference);

        if ($currency->id !== (int) Configuration::get('PS_CURRENCY_DEFAULT', null, null, $shop->id)) {
            throw new RuntimeException(
                sprintf('Currency "%s" is not default currency in shop "%s"', $currencyReference, $shopReference)
            );
        }
    }

    /**
     * @Then :isoCode currency should be deleted
     */
    public function assertCurrencyIsDeleted($isoCode)
    {
        if (Currency::getIdByIsoCode($isoCode)) {
            throw new RuntimeException(
                sprintf('Currency with ISO Code "%s" was found.', $isoCode)
            );
        }
    }
}
