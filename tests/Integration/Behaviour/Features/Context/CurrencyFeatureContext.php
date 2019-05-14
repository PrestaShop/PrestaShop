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

use Behat\Behat\Hook\Scope\BeforeScenarioScope;
use Context;
use Currency;
use Configuration;
use RuntimeException;

class CurrencyFeatureContext extends AbstractPrestaShopFeatureContext
{
    use CartAwareTrait;

    /**
     * @var ShopFeatureContext
     */
    private $shopFeatureContext;

    /**
     * @var Domain\CurrencyFeatureContext
     */
    private $domainCurrencyFeatureContext;

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

    /** @BeforeScenario */
    public function before(BeforeScenarioScope $scope)
    {
        $this->shopFeatureContext = $scope->getEnvironment()->getContext(ShopFeatureContext::class);
        $this->domainCurrencyFeatureContext = $scope->getEnvironment()->getContext(Domain\CurrencyFeatureContext::class);
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
        $currency = $this->domainCurrencyFeatureContext->getCurrencyFromRegistry($reference);

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
     * @Then currency :reference should have status :status
     */
    public function assertCurrencyStatus($reference, $status)
    {
        $statuses = ['enabled', 'disabled'];

        if (!in_array($status, $statuses)) {
            throw new RuntimeException(sprintf('Available statuses are: %s', implode(',', $statuses)));
        }

        $currency = $this->domainCurrencyFeatureContext->getCurrencyFromRegistry($reference);
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
        $currency = $this->domainCurrencyFeatureContext->getCurrencyFromRegistry($reference);

        if ($currency->conversion_rate != $exchangeRate) {
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
        $currency = $this->domainCurrencyFeatureContext->getCurrencyFromRegistry($currencyReference);
        $shop = $this->shopFeatureContext->getShopFromRegistry($shopReference);

        if (!in_array($shop->id, $currency->getAssociatedShops())) {
            throw new RuntimeException(sprintf(
                'Currency "%s" is not associated with "%s" shop',
                $currencyReference,
                $shopReference
            ));
        }
    }
}
