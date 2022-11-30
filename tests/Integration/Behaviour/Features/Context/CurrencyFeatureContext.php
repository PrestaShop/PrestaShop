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

use Cache;
use Configuration;
use Context;
use Currency;
use Db;
use DbQuery;
use Language;
use RuntimeException;

class CurrencyFeatureContext extends AbstractPrestaShopFeatureContext
{
    use CartAwareTrait;

    /**
     * @var Currency[]
     */
    protected $currencies = [];

    /**
     * @var Currency[]
     */
    protected $addedCurrencies = [];

    protected $previousDefaultCurrencyId;

    /**
     * @BeforeScenario
     */
    public function storePreviousCurrencyId()
    {
        $this->previousDefaultCurrencyId = Currency::getDefaultCurrencyId();
        Cache::clean('Currency::*');
    }

    /**
     * This hook can be used to perform a database cleaning of added objects
     *
     * @AfterScenario
     */
    public function cleanCurrencyFixtures()
    {
        Configuration::set('PS_CURRENCY_DEFAULT', $this->previousDefaultCurrencyId);
        // We only delete currencies that were added in the scenario, deleting the default currency would result in
        // impacting the default currency
        foreach ($this->addedCurrencies as $currency) {
            $currency->delete();
        }
        $this->addedCurrencies = [];
        $this->currencies = [];
    }

    /**
     * @Given /^there is a currency named "(.+)" with iso code "(.+)" and exchange rate of (\d+\.\d+)$/
     */
    public function thereIsACurrency($currencyName, $currencyIsoCode, $changeRate)
    {
        $currencyId = Currency::getIdByIsoCode($currencyIsoCode, 0, true);
        // soft delete here...
        if (!$currencyId) {
            $currency = new Currency();
            $currency->name = $currencyIsoCode;
            $currency->precision = 2;
            $currency->iso_code = $currencyIsoCode;
            $currency->active = 1;
            $currency->conversion_rate = $changeRate;
            $currency->add();
            $this->addedCurrencies[] = $currency;
        } else {
            $currency = new Currency($currencyId);
            $currency->name = $currencyIsoCode;
            $currency->precision = 2;
            $currency->active = 1;
            $currency->conversion_rate = $changeRate;
            $currency->save();
        }
        $this->currencies[$currencyName] = $currency;
        SharedStorage::getStorage()->set($currencyName, (int) $currency->id);
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
        if ($this->getCurrentCart() !== null) {
            $this->getCurrentCart()->id_currency = $this->currencies[$currencyName]->id;
        }
        Context::getContext()->currency = $this->currencies[$currencyName];
    }

    /**
     * @When I set the pattern :pattern for currency :reference in locale :localeIsoCode
     */
    public function setCurrencyPattern($pattern, $reference, $localeIsoCode)
    {
        $languageId = Language::getIdByLocale($localeIsoCode, true);
        $currency = $this->getCurrency($reference);
        $patterns = $currency->pattern;
        if (is_array($patterns)) {
            $patterns[$languageId] = $pattern;
        } else {
            $patterns = [$languageId => $pattern];
        }
        $currency->setLocalizedPatterns($patterns);

        if (!$currency->save(true, true)) {
            throw new RuntimeException('Could not save format modification');
        }
    }

    /**
     * @param string $currencyName
     */
    public function checkCurrencyWithNameExists(string $currencyName)
    {
        $this->checkFixtureExists($this->currencies, 'Currency', $currencyName);
    }

    /**
     * @Given database contains :expectedCount rows of currency :currencyIsoCode
     */
    public function countCurrencies($expectedCount, $currencyIsoCode)
    {
        $query = new DbQuery();
        $query->select('COUNT(c.id_currency)');
        $query->from('currency', 'c');
        $query->where('iso_code = \'' . pSQL($currencyIsoCode) . '\'');

        $databaseCount = (int) Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue($query->build());

        if ((int) $expectedCount !== $databaseCount) {
            throw new RuntimeException(sprintf('Found %s currencies with iso code %s, expected %s', $databaseCount, $currencyIsoCode, $expectedCount));
        }
    }

    /**
     * @Then currency :reference should be :isoCode
     */
    public function assertCurrencyIsoCode($reference, $isoCode)
    {
        $currency = $this->getCurrency($reference);

        if ($currency->iso_code !== $isoCode) {
            throw new RuntimeException(sprintf('Currency "%s" has "%s" iso code, but "%s" was expected.', $reference, $currency->iso_code, $isoCode));
        }
    }

    /**
     * @Then /^currency "(.*)" should have status (enabled|disabled)$/
     */
    public function assertCurrencyStatus($reference, $status)
    {
        $currency = $this->getCurrency($reference);
        $expectedStatus = $status === 'enabled';

        if ($currency->active != $expectedStatus) {
            throw new RuntimeException(sprintf('Currency "%s" has status "%s", but "%s" was expected.', $reference, $currency->active, $expectedStatus));
        }
    }

    /**
     * @Then currency :reference exchange rate should be :exchangeRate
     */
    public function assertCurrencyExchangeRate($reference, $exchangeRate)
    {
        $currency = $this->getCurrency($reference);

        if ((float) $currency->conversion_rate != (float) $exchangeRate) {
            throw new RuntimeException(sprintf('Currency "%s" has "%s" exchange rate, but "%s" was expected.', $reference, $currency->conversion_rate, $exchangeRate));
        }
    }

    /**
     * @Then currency :reference precision should be :precision
     */
    public function assertCurrencyPrecision($reference, $precision)
    {
        $currency = $this->getCurrency($reference);

        if ((int) $currency->precision != (int) $precision) {
            throw new RuntimeException(sprintf('Currency "%s" has "%s" precision, but "%s" was expected.', $reference, $currency->precision, $precision));
        }
    }

    /**
     * @Then currency :currencyReference should be available in shop :shopReference
     */
    public function assertCurrencyIsAvailableInShop($currencyReference, $shopReference)
    {
        $currencyId = SharedStorage::getStorage()->get($currencyReference);
        $shopId = SharedStorage::getStorage()->get($shopReference);
        $currency = new Currency($currencyId);

        if (!in_array($shopId, $currency->getAssociatedShops())) {
            throw new RuntimeException(sprintf('Currency "%s" is not associated with "%s" shop', $currencyReference, $shopReference));
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

        SharedStorage::getStorage()->set($reference, $currencyId);
    }

    /**
     * @Given currency with :isoCode has been deleted
     */
    public function assertCurrencyHasBeenDeleted($isoCode)
    {
        $query = new DbQuery();
        $query->select('c.id_currency');
        $query->from('currency', 'c');
        $query->where('deleted = 1');
        $query->where('iso_code = \'' . pSQL($isoCode) . '\'');

        $currencyId = (int) Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue($query->build());

        if (!$currencyId) {
            throw new RuntimeException(sprintf('Currency with ISO Code "%s" should be deleted in database', $isoCode));
        }
    }

    /**
     * @Given currency with :isoCode is not deleted
     */
    public function assertCurrencyIsNotDeleted($isoCode)
    {
        $currencyId = (int) Currency::getIdByIsoCode($isoCode, 0, true, false);
        if (!$currencyId) {
            throw new RuntimeException(sprintf('Currency with ISO Code "%s" should not be deleted in database', $isoCode));
        }
    }

    /**
     * @Given currency with :isoCode has been deactivated
     */
    public function assertCurrencyHasBeenDeactivated($isoCode)
    {
        $query = new DbQuery();
        $query->select('c.id_currency');
        $query->from('currency', 'c');
        $query->where('active = 0');
        $query->where('iso_code = \'' . pSQL($isoCode) . '\'');

        $currencyId = (int) Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue($query->build());

        if (!$currencyId) {
            throw new RuntimeException(sprintf('Currency with ISO Code "%s" should be deactivated in database', $isoCode));
        }
    }

    /**
     * @Given currency :currencyReference is default in :shopReference shop
     *
     * @param string $currencyReference
     * @param string $shopReference
     */
    public function assertCurrencyIsDefaultInShop(string $currencyReference, string $shopReference)
    {
        $currencyId = SharedStorage::getStorage()->get($currencyReference);
        $shopId = SharedStorage::getStorage()->get($shopReference);

        if ($currencyId !== (int) Configuration::get('PS_CURRENCY_DEFAULT', null, null, $shopId)) {
            throw new RuntimeException(sprintf('Currency "%s" is not default currency in shop "%s"', $currencyReference, $shopReference));
        }
    }

    /**
     * @Then :isoCode currency should be deleted
     */
    public function assertCurrencyIsDeleted($isoCode)
    {
        if (Currency::getIdByIsoCode($isoCode)) {
            throw new RuntimeException(sprintf('Currency with ISO Code "%s" was found.', $isoCode));
        }
    }

    /**
     * @Then currency :reference numeric iso code should be :numericIsoCode
     */
    public function assertCurrencyNumericIsoCode($reference, $numericIsoCode)
    {
        $currency = $this->getCurrency($reference);

        if ('null' === $numericIsoCode) {
            if (null !== $currency->numeric_iso_code) {
                throw new RuntimeException(sprintf('Currency "%s" has "%s" numeric iso code, but null was expected.', $reference, $currency->numeric_iso_code));
            }
        } elseif ((int) $currency->numeric_iso_code !== (int) $numericIsoCode) {
            throw new RuntimeException(sprintf('Currency "%s" has "%s" numeric iso code, but "%s" was expected.', $reference, $currency->numeric_iso_code, $numericIsoCode));
        }
    }

    /**
     * @Then currency :reference name should be :name
     */
    public function assertCurrencyName($reference, $name)
    {
        $currency = $this->getCurrency($reference);

        if ($currency->name !== $name) {
            throw new RuntimeException(sprintf('Currency "%s" has "%s" name, but "%s" was expected.', $reference, $currency->name, $name));
        }
    }

    /**
     * @Then currency :reference symbol should be :symbol
     */
    public function assertCurrencySymbol($reference, $symbol)
    {
        $currency = $this->getCurrency($reference);

        if ($currency->symbol !== $symbol) {
            throw new RuntimeException(sprintf('Currency "%s" has "%s" symbol, but "%s" was expected.', $reference, $currency->symbol, $symbol));
        }
    }

    /**
     * @Then /^currency "(.*)" should have unofficial (true|false)$/
     */
    public function assertCurrencyUnofficial($reference, $unofficial)
    {
        $currency = $this->getCurrency($reference);
        $expectedUnofficial = $unofficial === 'true';

        if ($currency->unofficial != $expectedUnofficial) {
            throw new RuntimeException(sprintf('Currency "%s" has unofficial "%s", but "%s" was expected.', $reference, $currency->unofficial, (int) $expectedUnofficial));
        }
    }

    /**
     * @Then /^currency "(.*)" should have modified (true|false)$/
     */
    public function assertCurrencyModified($reference, $modified)
    {
        $currency = $this->getCurrency($reference);
        $expectedModified = $modified === 'true';

        if ($currency->modified != $expectedModified) {
            throw new RuntimeException(sprintf('Currency "%s" has modified "%s", but "%s" was expected.', $reference, $currency->modified, (int) $expectedModified));
        }
    }

    /**
     * @Then currency :reference should have pattern :pattern for language :localeCode
     */
    public function assertCurrencyPattern($reference, $pattern, $localeCode)
    {
        $currency = $this->getCurrency($reference);
        $langId = Language::getIdByLocale($localeCode, true);
        $currencyPattern = $currency->getPattern($langId);
        if ('empty' === $pattern) {
            $pattern = '';
        }

        if ($currencyPattern !== $pattern) {
            throw new RuntimeException(sprintf('Currency "%s" has "%s" pattern for language %s, but "%s" was expected.', $reference, $currencyPattern, $localeCode, $pattern));
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
