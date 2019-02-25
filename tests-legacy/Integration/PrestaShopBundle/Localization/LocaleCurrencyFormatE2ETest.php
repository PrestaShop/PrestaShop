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

namespace LegacyTests\Unit\Core\Localization\Locale;

use Cache;
use Context;
use Currency;
use PrestaShop\PrestaShop\Core\Localization\Locale\Repository;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\DependencyInjection\Container;
use LegacyTests\Unit\ContextMocker;
use LegacyTests\PrestaShopBundle\Utils\DatabaseCreator as Database;

class LocaleCurrencyFormatE2ETest extends KernelTestCase
{
    /**
     * @var Container
     */
    private $container;

    /**
     * @var ContextMocker
     */
    protected $contextMocker;

    /**
     * @var Repository
     */
    protected $localeRepo;

    /**
     * @var Context
     */
    protected $context;

    protected function setUp()
    {
        parent::setUp();

        $this->installCurrency('EUR');
        $this->installCurrency('RUB');

        // instanciate kernel to get DI container
        $kernel = self::createKernel();
        $kernel->boot();
        $this->container = $kernel->getContainer();

        // mock context to correctly reset coupled tests
        $this->contextMocker = new ContextMocker();
        $this->contextMocker->mockContext();

        $this->localeRepo = $this->container->get('prestashop.core.localization.locale.repository');
        $this->context = Context::getContext();
    }

    protected function tearDown()
    {
        parent::tearDown();
        // correctly reset coupled tests
        $this->contextMocker->resetContext();
    }

    public static function setUpBeforeClass()
    {
        Database::restoreTestDB();
        require_once __DIR__ . '/../../../../config/config.inc.php';
    }

    protected function installCurrency($currencyCode)
    {
        $currency = new Currency();
        $currency->name = $currencyCode;
        $currency->iso_code = $currencyCode;
        $currency->active = 1;
        $currency->conversion_rate = 0.9;
        $currency->precision = 2;
        $currency->save();
        // needed as currencies are cached :/
        $cacheId = 'Currency::getIdByIsoCode_' . pSQL($currencyCode) . '-0';
        Cache::clean($cacheId);
    }

    public function testLocalePriceFormat()
    {
        $this->context->currentLocale = $this->localeRepo->getLocale($this->context->language->locale);

        $price = $this->context->currentLocale->formatPrice('0.0234', 'USD');
        $this->assertEquals('$0.02', $price);

        $price = $this->context->currentLocale->formatPrice('2345', 'USD');
        $this->assertEquals('$2,345.00', $price);

        $price = $this->context->currentLocale->formatPrice('2345.326', 'USD');
        $this->assertEquals('$2,345.33', $price);
    }

    /**
     * @dataProvider localePriceDataProvider
     *
     * @param $localeCode
     * @param $currencyCode
     * @param $price
     * @param $expected
     *
     * @throws \PrestaShop\PrestaShop\Core\Localization\Exception\LocalizationException
     */
    public function testLocalePriceFormatAfterCurrencyInstallation1(
        $localeCode,
        $currencyCode,
        $price,
        $expected
    ) {
        $locale = $this->localeRepo->getLocale($localeCode);
        $price = $locale->formatPrice($price, $currencyCode);
        $this->assertEquals($expected, $price);
    }

    public function localePriceDataProvider()
    {
        // build correct dataProvider array from source
        $prices = [];
        foreach ($this->localePriceProvider() as $localeCode => $currencyData) {
            foreach ($currencyData as $currencyCode => $pricesData) {
                foreach ($pricesData as $priceData) {
                    $prices[$localeCode . ' ' . $currencyCode . ' ' . $priceData['price']] = [
                        $localeCode,
                        $currencyCode,
                        $priceData['price'],
                        $priceData['expected'],
                    ];
                }
            }
        }

        return $prices;
    }

    public function localePriceProvider()
    {
        return [
            'fr-FR' => [
                'EUR' => [
                    [
                        'price' => 0.0234,
                        'expected' => '0,02 €',
                    ],
                    [
                        'price' => 2345,
                        'expected' => '2 345,00 €',
                    ],
                    [
                        'price' => 2345.326,
                        'expected' => '2 345,33 €',
                    ],
                ],
                'USD' => [
                    [
                        'price' => 0.0234,
                        'expected' => '0,02 $',
                    ],
                    [
                        'price' => 2345,
                        'expected' => '2 345,00 $',
                    ],
                    [
                        'price' => 2345.326,
                        'expected' => '2 345,33 $',
                    ],
                ],
                'RUB' => [
                    [
                        'price' => 0.0234,
                        'expected' => '0,02 ₽',
                    ],
                    [
                        'price' => 2345,
                        'expected' => '2 345,00 ₽',
                    ],
                    [
                        'price' => 2345.326,
                        'expected' => '2 345,33 ₽',
                    ],
                ],
            ],
            'en-US' => [
                'EUR' => [
                    [
                        'price' => 0.0234,
                        'expected' => '€0.02',
                    ],
                    [
                        'price' => 2345,
                        'expected' => '€2,345.00',
                    ],
                    [
                        'price' => 2345.326,
                        'expected' => '€2,345.33',
                    ],
                ],
                'USD' => [
                    [
                        'price' => 0.0234,
                        'expected' => '$0.02',
                    ],
                    [
                        'price' => 2345,
                        'expected' => '$2,345.00',
                    ],
                    [
                        'price' => 2345.326,
                        'expected' => '$2,345.33',
                    ],
                ],
                'RUB' => [
                    [
                        'price' => 0.0234,
                        'expected' => '₽0.02',
                    ],
                    [
                        'price' => 2345,
                        'expected' => '₽2,345.00',
                    ],
                    [
                        'price' => 2345.326,
                        'expected' => '₽2,345.33',
                    ],
                ],
            ],
            'ru-RU' => [
                'EUR' => [
                    [
                        'price' => 0.0234,
                        'expected' => '0,02 €',
                    ],
                    [
                        'price' => 2345,
                        'expected' => '2 345,00 €',
                    ],
                    [
                        'price' => 2345.326,
                        'expected' => '2 345,33 €',
                    ],
                ],
                'USD' => [
                    [
                        'price' => 0.0234,
                        'expected' => '0,02 $',
                    ],
                    [
                        'price' => 2345,
                        'expected' => '2 345,00 $',
                    ],
                    [
                        'price' => 2345.326,
                        'expected' => '2 345,33 $',
                    ],
                ],
                'RUB' => [
                    [
                        'price' => 0.0234,
                        'expected' => '0,02 ₽',
                    ],
                    [
                        'price' => 2345,
                        'expected' => '2 345,00 ₽',
                    ],
                    [
                        'price' => 2345.326,
                        'expected' => '2 345,33 ₽',
                    ],
                ],
            ],
        ];
    }
}
