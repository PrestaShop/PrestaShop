<?php

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
        $this->getCurrentCart()->id_currency        = $this->currencies[$currencyName]->id;
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
