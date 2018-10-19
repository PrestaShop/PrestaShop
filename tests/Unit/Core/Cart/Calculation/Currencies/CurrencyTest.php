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

namespace Tests\Unit\Core\Cart\Calculation\Currencies;

use Configuration;
use Context;
use Currency;
use Tests\Unit\Core\Cart\Calculation\AbstractCartCalculationTest;

class CurrencyTest extends AbstractCartCalculationTest
{

    /**
     * isoCode : [a-zA-Z]{2,3}  @see Validate::isLanguageIsoCode()
     */
    const CURRENCY_FIXTURES = [
        1 => [
            'isoCode'    => 'USD',
            'changeRate' => 0.92,
        ],
        2 => [
            'isoCode'    => 'CHF',
            'changeRate' => 1.25,
        ],
        3 => [
            'isoCode'    => 'EUR',
            'changeRate' => 0.63,
        ],
    ];

    /**
     * @var Currency[]
     */
    protected $currencies = [];

    protected $defaultCurrencyId;

    public function setUp()
    {
        $this->defaultCurrencyId = Configuration::get('PS_CURRENCY_DEFAULT');

        parent::setUp();
    }

    public function tearDown()
    {
        /*
        // do not delete currencies here : theyr are only soft deleted, and it creates conflicts when recreating with same iso code
        foreach ($this->currencies as $currency) {
            $currency->delete();
        }
        */
        Configuration::set('PS_CURRENCY_DEFAULT', $this->defaultCurrencyId);

        parent::tearDown();
    }

    /**
     * @dataProvider currencyDataProvider
     */
    public function testCurrencies(
        $productData,
        $expectedTotal,
        $cartRuleData,
        $defaultCurrencyId,
        $currencyId
    ) {
        $this->resetCart();
        $this->insertCurrencies();
        $this->setDefaultCurrency($defaultCurrencyId);

        $this->addProductsToCart($productData);
        $this->addCartRulesToCart($cartRuleData);
        $this->setCurrentCurrency($currencyId);
        $this->compareCartTotalTaxIncl($expectedTotal);
    }

    /**
     * sets the default currency change rate to avoid using 1.0 as default
     *
     * @param float $changeRate
     */
    protected function setDefaultCurrency($currencyId)
    {
        $currency = $this->getCurrencyFromFixtureId($currencyId);
        if ($currency === null) {
            throw new \Exception('Currency not found with fixture id = ' . $currencyId);
        }
        Configuration::set('PS_CURRENCY_DEFAULT', $currency->id);
    }

    protected function insertCurrencies()
    {
        foreach (static::CURRENCY_FIXTURES as $k => $currencyFixture) {
            $currencyId = Currency::getIdByIsoCode($currencyFixture['isoCode']);
            // soft delete here...
            if (!$currencyId) {
                $currency                  = new Currency();
                $currency->name            = $currencyFixture['isoCode'];
                $currency->iso_code        = $currencyFixture['isoCode'];
                $currency->active          = 1;
                $currency->conversion_rate = $currencyFixture['changeRate'];
                $currency->add();
            } else {
                $currency                  = new Currency($currencyId);
                $currency->name            = $currencyFixture['isoCode'];
                $currency->active          = 1;
                $currency->conversion_rate = $currencyFixture['changeRate'];
                $currency->save();
            }
            $this->currencies[$k] = $currency;
        }
    }

    /**
     * @param int $id fixture id
     *
     * @return Currency|null
     */
    protected function getCurrencyFromFixtureId($id)
    {
        if (isset($this->currencies[$id])) {
            return $this->currencies[$id];
        }

        return null;
    }

    protected function setCurrentCurrency($currencyId)
    {
        if ($currencyId == 0) {
            $this->cart->id_currency = 0;

            return;
        }

        $currency = $this->getCurrencyFromFixtureId($currencyId);
        if ($currency === null) {
            throw new \Exception('Currency not found with fixture id = ' . $currencyId);
        }
        $this->cart->id_currency        = $currency->id;
        Context::getContext()->currency = $currency;
    }

    public function currencyDataProvider()
    {
        $data              = [];
        $currencyIdDoubles = [
            [
                'defaultCurrencyId' => 1,
                'currencyId'        => 1,
            ],
            [
                'defaultCurrencyId' => 1,
                'currencyId'        => 2,
            ],
            [
                'defaultCurrencyId' => 2,
                'currencyId'        => 1,
            ],
            [
                'defaultCurrencyId' => 1,
                'currencyId'        => 3,
            ],
            [
                'defaultCurrencyId' => 3,
                'currencyId'        => 1,
            ],
        ];
        foreach ($currencyIdDoubles as $currencyIdDouble) {
            $dataSets = $this->getCurrencyData(
                $currencyIdDouble['defaultCurrencyId'],
                $currencyIdDouble['currencyId']
            );
            foreach ($dataSets as $k => $dataSet) {
                $testCasePrefix             = 'defaultCurrency #'
                                              . $currencyIdDouble['defaultCurrencyId']
                                              . ' / currencyId #'
                                              . $currencyIdDouble['currencyId']
                                              . ' - ';
                $data[$testCasePrefix . $k] = array_merge(
                    $dataSet,
                    $currencyIdDouble
                );
            }
        }

        return $data;
    }

    public function getCurrencyData($defaultCurrencyId, $currencyId)
    {
        if ($defaultCurrencyId == $currencyId) {
            $rate = 1;
        } else {
            $rate = static::CURRENCY_FIXTURES[$currencyId]['changeRate'];
        }


        return [
            'empty cart'                             => [
                'products'      => [],
                'expectedTotal' => 0,
                'cartRules'     => [],
            ],
            'one product in cart, quantity 1'        => [
                'products'      => [1 => 1,],
                'expectedTotal' => $rate * (static::PRODUCT_FIXTURES[1]['price']
                                            + static::DEFAULT_SHIPPING_FEE + static::DEFAULT_WRAPPING_FEE),
                'cartRules'     => [],
            ],
            'one product in cart, quantity 3'        => [
                'products'      => [1 => 3,],
                'expectedTotal' => round(
                    $rate * (3 * static::PRODUCT_FIXTURES[1]['price']
                             + static::DEFAULT_SHIPPING_FEE + static::DEFAULT_WRAPPING_FEE),
                    2
                ),
                'cartRules'     => [],
            ],
            '3 products in cart, several quantities' => [
                'products'      => [
                    2 => 2,
                    1 => 3,
                    3 => 1,
                ],
                'expectedTotal' => $rate * (3 * static::PRODUCT_FIXTURES[1]['price']
                                            + 2 * static::PRODUCT_FIXTURES[2]['price']
                                            + static::PRODUCT_FIXTURES[3]['price']
                                            + static::DEFAULT_SHIPPING_FEE + static::DEFAULT_WRAPPING_FEE),
                'cartRules'     => [],
            ],
        ];
    }

}
