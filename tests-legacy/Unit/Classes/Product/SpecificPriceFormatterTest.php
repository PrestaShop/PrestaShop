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

namespace LegacyTests\Unit\Classes\Product\SpecificPrice;

use Context;
use Configuration;
use Currency;
use Language;
use LegacyTests\Unit\ContextMocker;
use LocalizationPack;
use PrestaShop\PrestaShop\Adapter\Product\PriceFormatter;
use PrestaShopBundle\Cache\LocalizationWarmer;
use SpecificPriceFormatter;
use Tests\TestCase\SymfonyIntegrationTestCase;

class SpecificPriceFormatterTest extends SymfonyIntegrationTestCase
{
    /**
     * @var ContextMocker
     */
    protected $contextMocker;

    protected function setUp()
    {
        parent::setUp();
        self::installTestedLanguagePacks();
    }

    protected function tearDown()
    {
        global $kernel;
        unset($kernel);
        parent::tearDown();
        $this->contextMocker->resetContext();
    }

    protected static function installTestedLanguagePacks()
    {
        $countries = [
            'us',
            'fr',
        ];
        $cacheDir = _PS_CACHE_DIR_ . 'sandbox' . DIRECTORY_SEPARATOR;

        foreach ($countries as $country) {
            $xmlContent = (new LocalizationWarmer(_PS_VERSION_, $country))
                ->warmUp($cacheDir);

            (new LocalizationPack())->loadLocalisationPack($xmlContent, false, true);
        }
    }

    /**
     * @dataProvider specificPricesProvider
     *
     * @param $price
     * @param $taxRate
     * @param $ecotaxAmount
     * @param $currencyData
     * @param $specificPrices
     * @param $isTaxIncluded
     * @param $expected
     */
    public function testFormatSpecificPrice(
        $price,
        $taxRate,
        $ecotaxAmount,
        $currencyData,
        $specificPrices,
        $isTaxIncluded,
        $expected
    ) {
        $context = Context::getContext();

        $currency = new Currency();
        $currency->active = true;
        $currency->conversion_rate = $currencyData['conversion_rate'];
        $currency->sign = $currencyData['sign'];
        $currency->iso_code = $currencyData['code'];
        $context->currency = $currency;

        $language = new Language();
        $language->iso_code = 'EN';
        $language->locale = 'en-US';
        $context->language = $language;

        $specificPriceFormatter = new SpecificPriceFormatter(
            $specificPrices[0],
            $isTaxIncluded,
            $context->currency,
            Configuration::get('PS_DISPLAY_DISCOUNT_PRICE')
        );
        $formattedSpecificPrice = $specificPriceFormatter->formatSpecificPrice($price, $taxRate, $ecotaxAmount);

        $priceFormatter = new PriceFormatter();
        $this->assertEquals($priceFormatter->format($expected[0]['discount']), $formattedSpecificPrice['discount']);
        $this->assertEquals($priceFormatter->format($expected[0]['save']), $formattedSpecificPrice['save']);
    }

    public function specificPricesProvider()
    {
        $specificPrices = array(
            0 => array(
                'id_specific_price' => '9',
                'id_specific_price_rule' => '0',
                'id_cart' => '0',
                'id_product' => '10',
                'id_shop' => '1',
                'id_shop_group' => '0',
                'id_currency' => '0',
                'id_country' => '0',
                'id_group' => '0',
                'id_customer' => '0',
                'id_product_attribute' => '0',
                'price' => '15.000000',
                'from_quantity' => '15',
                'reduction' => 0,
                'reduction_tax' => '1',
                'reduction_type' => 'amount',
                'from' => '0000-00-00 00:00:00',
                'to' => '0000-00-00 00:00:00',
                'score' => '48',
                'quantity' => '15',
                'reduction_with_tax' => 0,
                'nextQuantity' => -1,
            ),
        );
        $currencyEur = array(
            'conversion_rate' => 1.0,
            'sign' => '€',
            'code' => 'EUR',
        );
        $currencyDol = array(
            'conversion_rate' => 1.3,
            'sign' => '$',
            'code' => 'USD',
        );

        return array(
            'EUR to USD, without ecotax' => array(
                'price' => 31.2,
                'tax_rate' => 20,
                'ecotax_amount' => 0,
                'currency' => $currencyDol,
                'specific_prices' => $specificPrices,
                'isTaxIncluded' => true,
                'expected' => array(
                    array(
                        'discount' => 7.80,
                        'save' => 117.00,
                    ),
                ),
            ),
            'EUR to EUR, without ecotax' => array(
                'price' => 24,
                'tax_rate' => 20,
                'ecotax_amount' => 0,
                'currency' => $currencyEur,
                'specific_prices' => $specificPrices,
                'isTaxIncluded' => true,
                'expected' => array(
                    array(
                        'discount' => 6.00,
                        'save' => 90.00,
                    ),
                ),
            ),
            'EUR to USD, with ecotax' => array(
                'price' => 31.2,
                'tax_rate' => 20,
                'ecotax_amount' => 0.9,
                'currency' => $currencyDol,
                'specific_prices' => $specificPrices,
                'isTaxIncluded' => true,
                'expected' => array(
                    array(
                        'discount' => 6.63,
                        'save' => 99.45,
                    ),
                ),
            ),
            'EUR to EUR, with ecotax' => array(
                'price' => 24,
                'tax_rate' => 20,
                'ecotax_amount' => 0.9,
                'currency' => $currencyEur,
                'specific_prices' => $specificPrices,
                'isTaxIncluded' => true,
                'expected' => array(
                    array(
                        'discount' => 5.10,
                        'save' => 76.50,
                    ),
                ),
            ),
            'EUR to USD, without any tax' => array(
                'price' => 31.2,
                'tax_rate' => 20,
                'ecotax_amount' => 0,
                'currency' => $currencyDol,
                'specific_prices' => $specificPrices,
                'isTaxIncluded' => false,
                'expected' => array(
                    array(
                        'discount' => 11.70,
                        'save' => 175.50,
                    ),
                ),
            ),
            'EUR to EUR, without any tax' => array(
                'price' => 24,
                'tax_rate' => 20,
                'ecotax_amount' => 0,
                'currency' => $currencyEur,
                'specific_prices' => $specificPrices,
                'isTaxIncluded' => false,
                'expected' => array(
                    array(
                        'discount' => 9.00,
                        'save' => 135.00,
                    ),
                ),
            ),
        );
    }
}
