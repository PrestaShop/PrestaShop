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

namespace Tests\Unit\Core\Localization\Currency;

use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\Localization\Currency\CurrencyData;
use PrestaShop\PrestaShop\Core\Localization\Currency\DataSourceInterface as CurrencyDataSourceInterface;
use PrestaShop\PrestaShop\Core\Localization\Currency\Repository as CurrencyRepository;
use PrestaShop\PrestaShop\Core\Localization\Exception\LocalizationException;

class RepositoryTest extends TestCase
{
    /**
     * An instance of the tested CurrencyRepository class
     *
     * This Locale CurrencyRepository has been populated with stub data source dependency.
     *
     * @var CurrencyRepository
     */
    protected $currencyRepository;

    protected function setUp()
    {
        $dataSource = $this->createMock(CurrencyDataSourceInterface::class);
        $dataSource
            ->method('getDataByCurrencyCode')
            ->willReturnCallback(
                function ($isoCode) {
                    $data = new CurrencyData();

                    switch ($isoCode) {
                        case 'EUR':
                            $data->isActive       = true;
                            $data->conversionRate = 1;
                            $data->isoCode        = 'EUR';
                            $data->numericIsoCode = 978;
                            $data->symbols        = ['fr-FR' => '€', 'en-US' => '€'];
                            $data->precision      = 2;
                            $data->names          = ['fr-FR' => 'euro', 'en-US' => 'euro'];
                            break;

                        case 'PCE':
                            $data->isActive       = true;
                            $data->conversionRate = 1;
                            $data->isoCode        = 'PCE';
                            $data->numericIsoCode = 999;
                            $data->symbols        = ['fr-FR' => '☮', 'en-US' => '☮'];
                            $data->precision      = 2;
                            $data->names          = ['fr-FR' => 'paix', 'en-US' => 'peace'];
                            break;

                        default:
                            throw new LocalizationException('Unknown currency code : ' . $isoCode);
                    }

                    return $data;
                }
            );

        /** @var $dataSource CurrencyDataSourceInterface */
        $this->currencyRepository = new CurrencyRepository($dataSource);
    }

    /**
     * Given a valid currency code
     * When asking the currency repository for the corresponding Currency
     * Then the expected Currency instance should be returned
     *
     * @param string $currencyCode
     *  Alphabetic ISO 4217 currency code passed to retreive the wanted Currency instance
     *
     * @param array $expectedNames
     *  Expected currency names, indexed by locale code
     *
     * @param array $expectedSymbols
     *  Expected currency symbols, indexed by locale code
     *
     * @throws \PrestaShop\PrestaShop\Core\Localization\Exception\LocalizationException
     * @dataProvider provideValidCurrencyCodes
     */
    public function testGetCurrency($currencyCode, $expectedNames, $expectedSymbols)
    {
        $currency = $this->currencyRepository->getCurrency($currencyCode);
        foreach ($expectedNames as $localeCode => $name) {
            $this->assertSame($name, $currency->getName($localeCode));
        }

        foreach ($expectedSymbols as $localeCode => $symbol) {
            $this->assertSame($symbol, $currency->getSymbol($localeCode));
        }
    }

    /**
     * Provide valid currency codes and the expected results
     *
     * Each data set item is structured as following :
     *  'Data set identifier' => [
     *      '<Currency ISO code to pass>',
     *      [<Expected names to receive>],
     *      [<Expected symbols to receive>]
     *  ]
     *
     * @return array
     */
    public function provideValidCurrencyCodes()
    {
        return [
            'French euro' => [
                'EUR',
                ['fr-FR' => 'euro', 'en-US' => 'euro'],
                ['fr-FR' => '€', 'en-US' => '€'],
            ],
            'Peace money' => [
                'PCE',
                ['fr-FR' => 'paix', 'en-US' => 'peace'],
                ['fr-FR' => '☮', 'en-US' => '☮'],
            ],
        ];
    }

    /**
     * Given an unknown or invalid currency code
     * When asking the currency repository for the corresponding Currency
     * Then an exception should be raised
     *
     * @expectedException \PrestaShop\PrestaShop\Core\Localization\Exception\LocalizationException
     */
    public function testGetCurrencyWithUnknownCode()
    {
        $this->currencyRepository->getCurrency('foobar');
    }
}
