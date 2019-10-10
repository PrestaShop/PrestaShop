<?php
/**
 * 2007-2019 PrestaShop and Contributors
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
use DbQuery;
use Db;
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
     * @Given currency :reference with ISO code :isoCode exists
     */
    public function createCurrencyWithIsoCode($reference, $isoCode)
    {
        /*
         * Currency::getIdByIsoCode only returns not deleted currency so we check the storage to avoid
         * duplicate contents, if it matches the expected iso code then we do nothing
         */
        if (SharedStorage::getStorage()->exists($reference)) {
            /** @var Currency $currency */
            $currency = SharedStorage::getStorage()->get($reference);
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
            $currency->deleted = 0;
            $currency->conversion_rate = 1;
            $currency->add();
        } else {
            $currency = new Currency($currencyId);
        }

        SharedStorage::getStorage()->set($reference, $currency);
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
            $currency->iso_code = $currencyIsoCode;
            $currency->active = 1;
            $currency->conversion_rate = $changeRate;
            $currency->add();
        } else {
            $currency = new Currency($currencyId);
            $currency->name = $currencyIsoCode;
            $currency->active = 1;
            $currency->conversion_rate = $changeRate;
            $currency->save();
        }
        $this->currencies[$currencyName] = $currency;
        SharedStorage::getStorage()->set($currencyName, $currency);
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
            throw new RuntimeException(sprintf(
                'Found %s currencies with iso code %s, expected %s',
                $databaseCount,
                $currencyIsoCode,
                $expectedCount
            ));
        }
    }
}
