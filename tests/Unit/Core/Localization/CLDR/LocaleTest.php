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

namespace Tests\Unit\Core\Localization\CLDR;

use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\Localization\CLDR\Currency;
use PrestaShop\PrestaShop\Core\Localization\CLDR\CurrencyData;
use PrestaShop\PrestaShop\Core\Localization\CLDR\Locale;
use PrestaShop\PrestaShop\Core\Localization\CLDR\LocaleData;
use PrestaShop\PrestaShop\Core\Localization\CLDR\NumberSymbolsData;

class LocaleTest extends TestCase
{
    /**
     * An instance of the tested CLDR Locale class
     *
     * This Locale instance has been populated with known data/dependencies.
     *
     * @var Locale
     */
    protected $cldrLocale;

    /**
     * A stub NumberSymbolsData object
     *
     * @var NumberSymbolsData
     */
    protected $stubSymbolsData;

    /**
     * A stub CurrencyData object
     *
     * @var CurrencyData
     */
    protected $stubCurrencyData;

    /**
     * @inheritDoc
     */
    protected function setUp()
    {
        $localeData                         = new LocaleData();
        $localeData->localeCode             = 'fr-FR';
        $localeData->numberingSystems       = ['latn'];
        $localeData->defaultNumberingSystem = 'latn';
        $localeData->minimumGroupingDigits  = 1;

        $this->stubSymbolsData                         = new NumberSymbolsData();
        $this->stubSymbolsData->decimal                = ',';
        $this->stubSymbolsData->group                  = ' ';
        $this->stubSymbolsData->list                   = ';';
        $this->stubSymbolsData->percentSign            = '%';
        $this->stubSymbolsData->minusSign              = '-';
        $this->stubSymbolsData->plusSign               = '+';
        $this->stubSymbolsData->exponential            = '^';
        $this->stubSymbolsData->superscriptingExponent = 'E';
        $this->stubSymbolsData->perMille               = '‰';
        $this->stubSymbolsData->infinity               = '∞';
        $this->stubSymbolsData->nan                    = 'NaN';
        $this->stubSymbolsData->timeSeparator          = ':';
        $localeData->numberSymbols                     = ['latn' => $this->stubSymbolsData];

        $localeData->decimalPatterns  = ['latn' => '#,##0.###'];
        $localeData->percentPatterns  = ['latn' => '#,##0.### %'];
        $localeData->currencyPatterns = ['latn' => '#,##0.00# ¤'];

        $this->stubCurrencyData                 = new CurrencyData();
        $this->stubCurrencyData->isoCode        = 'PCE';
        $this->stubCurrencyData->numericIsoCode = 333;
        $this->stubCurrencyData->decimalDigits  = 2;
        $this->stubCurrencyData->displayNames   = [
            'default' => 'PrestaShop Peace',
            'one'     => 'peace',
            'other'   => 'peaces',
        ];
        $this->stubCurrencyData->symbols        = [Currency::SYMBOL_TYPE_DEFAULT => 'PS☮', Currency::SYMBOL_TYPE_NARROW => '☮'];
        $localeData->currencies                 = ['PCE' => $this->stubCurrencyData];

        $this->cldrLocale = new Locale($localeData);
    }

    /**
     * Given a valid CLDR Locale object
     * When asking for the locale code
     * Then the expected value should be retrieved
     */
    public function testGetLocaleCode()
    {
        $this->assertSame(
            'fr-FR',
            $this->cldrLocale->getLocaleCode()
        );
    }

    /**
     * Given a valid CLDR Locale object
     * When asking for the locale's available numbering systems
     * Then the expected systems list should be retrieved
     */
    public function testGetNumberingSystems()
    {
        $this->assertSame(
            ['latn'],
            $this->cldrLocale->getNumberingSystems()
        );
    }

    /**
     * Given a valid CLDR Locale object
     * When asking for the locale's default numbering system
     * Then the expected value should be retrieved
     */
    public function testGetDefaultNumberingSystem()
    {
        $this->assertSame(
            'latn',
            $this->cldrLocale->getDefaultNumberingSystem()
        );
    }

    /**
     * Given a valid CLDR Locale object
     * When asking for the locale minimum grouping digits number
     * Then the expected number should be retrieved
     */
    public function testGetMinimumGroupingDigits()
    {
        $this->assertSame(
            1,
            $this->cldrLocale->getMinimumGroupingDigits()
        );
    }

    /**
     * Given a valid CLDR Locale object
     * When asking for all the locale's number symbols
     * Then the expected number symbols lists should be retrieved (by numbering system)
     */
    public function testGetAllNumberSymbols()
    {
        $this->assertSame(
            ['latn' => $this->stubSymbolsData],
            $this->cldrLocale->getAllNumberSymbols()
        );
    }

    /**
     * Given a valid CLDR Locale object and a valid + known numbering system
     * When asking for the number symbols of this system
     * Then the expected symbols should be retrieved
     *
     * @throws \PrestaShop\PrestaShop\Core\Localization\Exception\LocalizationException
     */
    public function testGetNumberSymbolsByNumberingSystem()
    {
        $this->assertSame(
            $this->stubSymbolsData,
            $this->cldrLocale->getNumberSymbolsByNumberingSystem('latn')
        );
        $this->assertSame(
            $this->stubSymbolsData,
            $this->cldrLocale->getNumberSymbolsByNumberingSystem()
        );
    }

    /**
     * Given a valid CLDR Locale object and an invalid numbering system
     * When asking for the number symbols of this system
     * Then an exception should be raised
     *
     * @expectedException \PrestaShop\PrestaShop\Core\Localization\Exception\LocalizationException
     */
    public function testGetNumberSymbolsByNumberingSystemWithUnknownNumberingSystem()
    {
        $this->cldrLocale->getNumberSymbolsByNumberingSystem('foobar');
    }

    /**
     * Given a valid CLDR Locale object and a valid numbering system
     * When asking for the decimal pattern of this numbering system
     * Then the expected pattern should be retrieved
     *
     * @throws \PrestaShop\PrestaShop\Core\Localization\Exception\LocalizationException
     */
    public function testGetDecimalPattern()
    {
        $this->assertSame(
            '#,##0.###',
            $this->cldrLocale->getDecimalPattern('latn')
        );
        $this->assertSame(
            '#,##0.###',
            $this->cldrLocale->getDecimalPattern()
        );
    }

    /**
     * Given a valid CLDR Locale object and an invalid numbering system
     * When asking for decimal pattern of this system
     * Then an exception should be raised
     *
     * @expectedException \PrestaShop\PrestaShop\Core\Localization\Exception\LocalizationException
     */
    public function testGetDecimalPatternWithUnknownNumberingSystem()
    {
        $this->cldrLocale->getDecimalPattern('foobar');
    }

    /**
     * Given a valid CLDR Locale object and a valid numbering system
     * When asking for the percentage pattern of this numbering system
     * Then the expected pattern should be retrieved
     *
     * @throws \PrestaShop\PrestaShop\Core\Localization\Exception\LocalizationException
     */
    public function testGetPercentPattern()
    {
        $this->assertSame(
            '#,##0.### %',
            $this->cldrLocale->getPercentPattern('latn')
        );
        $this->assertSame(
            '#,##0.### %',
            $this->cldrLocale->getPercentPattern()
        );
    }

    /**
     * Given a valid CLDR Locale object and an invalid numbering system
     * When asking for percentage pattern of this system
     * Then an exception should be raised
     *
     * @expectedException \PrestaShop\PrestaShop\Core\Localization\Exception\LocalizationException
     */
    public function testGetPercentPatternWithUnknownNumberingSystem()
    {
        $this->cldrLocale->getPercentPattern('foobar');
    }

    /**
     * Given a valid CLDR Locale object and a valid numbering system
     * When asking for the currency (price formatting) pattern of this numbering system
     * Then the expected pattern should be retrieved
     *
     * @throws \PrestaShop\PrestaShop\Core\Localization\Exception\LocalizationException
     */
    public function testGetCurrencyPattern()
    {
        $this->assertSame(
            '#,##0.00# ¤',
            $this->cldrLocale->getCurrencyPattern('latn')
        );
        $this->assertSame(
            '#,##0.00# ¤',
            $this->cldrLocale->getCurrencyPattern()
        );
    }

    /**
     * Given a valid CLDR Locale object and an invalid numbering system
     * When asking for currency (price formatting) pattern of this system
     * Then an exception should be raised
     *
     * @expectedException \PrestaShop\PrestaShop\Core\Localization\Exception\LocalizationException
     */
    public function testGetCurrencyPatternWithUnknownNumberingSystem()
    {
        $this->cldrLocale->getCurrencyPattern('foobar');
    }

    /**
     * Given a valid CLDR Locale object
     * When asking to this locale a given CLDR currency
     * Then the expected CLDR Currency should be retrieved. Or null if the currency code was unknown.
     */
    public function testGetCurrency()
    {
        $currency = $this->cldrLocale->getCurrency('PCE');

        $this->assertInstanceOf(
            Currency::class,
            $currency
        );

        $this->assertSame(
            $this->stubCurrencyData->isoCode,
            $currency->getIsoCode()
        );

        $this->assertNull(
            $this->cldrLocale->getCurrency('FOO'),
            'When asking for an unknown currency, null should be returned'
        );
    }

    /**
     * Given a valid CLDR Locale object
     * When asking to this locale a given CLDR currency data
     * Then the expected CLDR CurrencyData object should be retrieved. Or null if the currency code was unknown.
     */
    public function testGetCurrencyData()
    {
        $currencyData = $this->cldrLocale->getCurrencyData('PCE');

        $this->assertInstanceOf(
            CurrencyData::class,
            $currencyData
        );

        $this->assertSame(
            $this->stubCurrencyData->isoCode,
            $currencyData->isoCode
        );

        $this->assertNull(
            $this->cldrLocale->getCurrency('FOO'),
            'When asking for an unknown currency data, null should be returned'
        );
    }
}
