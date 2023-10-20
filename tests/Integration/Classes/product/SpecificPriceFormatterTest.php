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
 * needs please refer to https://devdocs.prestashop-project.org/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

declare(strict_types=1);

namespace Tests\Integration\Classes\product;

use Configuration;
use Context;
use Currency;
use Language;
use LocalizationPack;
use PrestaShop\PrestaShop\Adapter\Product\PriceFormatter;
use PrestaShopBundle\Cache\LocalizationWarmer;
use SpecificPriceFormatter;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Tests\Integration\Utility\ContextMockerTrait;
use Tests\Resources\Resetter\LocalizationPackResetter;

class SpecificPriceFormatterTest extends KernelTestCase
{
    use ContextMockerTrait;

    public static function tearDownAfterClass(): void
    {
        parent::tearDownAfterClass();
        LocalizationPackResetter::resetLocalizationPacks();
    }

    protected function setUp(): void
    {
        parent::setUp();

        self::mockContext();

        // Init Symfony
        self::bootKernel();
        // Global var for SymfonyContainer
        global $kernel;
        $kernel = self::$kernel;

        foreach (['us', 'fr'] as $country) {
            $localizationWarmer = new LocalizationWarmer(_PS_VERSION_, $country);
            $xmlContent = $localizationWarmer->warmUp(_PS_CACHE_DIR_ . 'sandbox' . DIRECTORY_SEPARATOR);

            $localizationPack = new LocalizationPack();
            $localizationPack->loadLocalisationPack($xmlContent, [], true);
        }
    }

    /**
     * @dataProvider dataProviderSpecificPriceFormatter
     *
     * @param float $price
     * @param float $taxRate
     * @param float $ecotaxAmount
     * @param array $currencyData
     * @param array $specificPrices
     * @param bool $isTaxIncluded
     * @param array $expected
     */
    public function testSpecificPriceFormatter(
        float $price,
        float $taxRate,
        float $ecotaxAmount,
        array $currencyData,
        array $specificPrices,
        bool $isTaxIncluded,
        array $expected
    ): void {
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

    public function dataProviderSpecificPriceFormatter(): array
    {
        $specificPrices = [
            0 => [
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
            ],
        ];

        $specificPricesWithoutReductionTax = [
            0 => [
                'id_specific_price' => '10',
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
                'price' => '8.000000',
                'from_quantity' => '10',
                'reduction' => 0,
                'reduction_tax' => '0',
                'reduction_type' => 'amount',
                'from' => '0000-00-00 00:00:00',
                'to' => '0000-00-00 00:00:00',
                'score' => '48',
                'quantity' => '10',
                'reduction_with_tax' => 0,
                'nextQuantity' => -1,
            ],
        ];

        $specificPricesWithoutReductionTaxWithReduction = [
            0 => [
                'id_specific_price' => '11',
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
                'price' => '11.350000',
                'from_quantity' => '3',
                'reduction' => 2,
                'reduction_tax' => '0',
                'reduction_type' => 'amount',
                'from' => '0000-00-00 00:00:00',
                'to' => '0000-00-00 00:00:00',
                'score' => '48',
                'quantity' => '3',
                'reduction_with_tax' => 0,
                'nextQuantity' => -1,
            ],
        ];
        $currencyEur = [
            'conversion_rate' => 1.0,
            'sign' => '€',
            'code' => 'EUR',
        ];
        $currencyDol = [
            'conversion_rate' => 1.3,
            'sign' => '$',
            'code' => 'USD',
        ];

        return [
            'EUR to USD, without ecotax' => [
                'price' => 31.2,
                'tax_rate' => 20,
                'ecotax_amount' => 0,
                'currency' => $currencyDol,
                'specific_prices' => $specificPrices,
                'isTaxIncluded' => true,
                'expected' => [
                    [
                        'discount' => 7.80,
                        'save' => 117.00,
                    ],
                ],
            ],
            'EUR to EUR, without ecotax' => [
                'price' => 24,
                'tax_rate' => 20,
                'ecotax_amount' => 0,
                'currency' => $currencyEur,
                'specific_prices' => $specificPrices,
                'isTaxIncluded' => true,
                'expected' => [
                    [
                        'discount' => 6.00,
                        'save' => 90.00,
                    ],
                ],
            ],
            'EUR to USD, with ecotax' => [
                'price' => 31.2,
                'tax_rate' => 20,
                'ecotax_amount' => 0.9,
                'currency' => $currencyDol,
                'specific_prices' => $specificPrices,
                'isTaxIncluded' => true,
                'expected' => [
                    [
                        'discount' => 6.63,
                        'save' => 99.45,
                    ],
                ],
            ],
            'EUR to EUR, with ecotax' => [
                'price' => 24,
                'tax_rate' => 20,
                'ecotax_amount' => 0.9,
                'currency' => $currencyEur,
                'specific_prices' => $specificPrices,
                'isTaxIncluded' => true,
                'expected' => [
                    [
                        'discount' => 5.10,
                        'save' => 76.50,
                    ],
                ],
            ],
            'EUR to USD, without any tax' => [
                'price' => 31.2,
                'tax_rate' => 20,
                'ecotax_amount' => 0,
                'currency' => $currencyDol,
                'specific_prices' => $specificPrices,
                'isTaxIncluded' => false,
                'expected' => [
                    [
                        'discount' => 11.70,
                        'save' => 175.50,
                    ],
                ],
            ],
            'EUR to EUR, without any tax' => [
                'price' => 24,
                'tax_rate' => 20,
                'ecotax_amount' => 0,
                'currency' => $currencyEur,
                'specific_prices' => $specificPrices,
                'isTaxIncluded' => false,
                'expected' => [
                    [
                        'discount' => 9.00,
                        'save' => 135.00,
                    ],
                ],
            ],
            'EUR to EUR, with taxes, without reduction tax' => [
                'price' => 12.30,
                'tax_rate' => 23,
                'ecotax_amount' => 0,
                'currency' => $currencyEur,
                'specific_prices' => $specificPricesWithoutReductionTax,
                'isTaxIncluded' => true,
                'expected' => [
                    [
                        'discount' => 2.46,
                        'save' => 24.60,
                    ],
                ],
            ],
            'EUR to EUR, with taxes, without reduction tax, with reduction' => [
                'price' => 15.48,
                'tax_rate' => 20,
                'ecotax_amount' => 0,
                'currency' => $currencyEur,
                'specific_prices' => $specificPricesWithoutReductionTaxWithReduction,
                'isTaxIncluded' => true,
                'expected' => [
                    [
                        'discount' => 4.26,
                        'save' => 12.78,
                    ],
                ],
            ],
        ];
    }
}
