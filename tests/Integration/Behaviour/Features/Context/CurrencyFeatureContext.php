<?php
/**
 * 2007-2019 PrestaShop
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
 * @copyright 2007-2019 PrestaShop SA
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace Tests\Integration\Behaviour\Features\Context;

use Behat\Behat\Context\Context as BehatContext;
use Context;
use Currency;
use Configuration;

class CurrencyFeatureContext implements BehatContext
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
    public function cleanCurrencies()
    {
        Configuration::set('PS_CURRENCY_DEFAULT', $this->previousDefaultCurrencyId);
        foreach ($this->currencies as $currency) {
            $currency->delete();
        }
        $this->currencies = [];
    }

    /**
     * @Given /^There is a currency with name (.+) and iso code (.+) and change rate of ([\d\.]+)$/
     */
    public function thereIsACurrency($currencyName, $currencyIsoCode, $changeRate)
    {
        $currencyId = Currency::getIdByIsoCode($currencyIsoCode);
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
    }

    /**
     * @Given /^Currency with name (.+) is the default one$/
     */
    public function setDefaultCurrency($currencyName)
    {
        $this->checkCurrencyWithNameExists($currencyName);
        Configuration::set('PS_CURRENCY_DEFAULT', $this->currencies[$currencyName]->id);
    }

    /**
     * @Given /^No currency is set as the current one$/
     */
    public function setNoCurrentCurrency()
    {
        $this->getCurrentCart()->id_currency = 0;
    }

    /**
     * @Given /^Currency with name (.+) is the current one$/
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
        if (!isset($this->currencies[$currencyName])) {
            throw new \Exception('Currency with name "' . $currencyName . '" was not added in fixtures');
        }
    }
}
