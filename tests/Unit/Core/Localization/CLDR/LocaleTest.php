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
 * needs please refer to https://devdocs.prestashop.com/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

declare(strict_types=1);

namespace Tests\Unit\Core\Localization\CLDR;

use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\Localization\CLDR\Currency;
use PrestaShop\PrestaShop\Core\Localization\CLDR\CurrencyData;
use PrestaShop\PrestaShop\Core\Localization\CLDR\CurrencyInterface;
use PrestaShop\PrestaShop\Core\Localization\CLDR\Locale;
use PrestaShop\PrestaShop\Core\Localization\CLDR\LocaleData;
use PrestaShop\PrestaShop\Core\Localization\CLDR\NumberSymbolsData;
use PrestaShop\PrestaShop\Core\Localization\Exception\LocalizationException;

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
     * {@inheritDoc}
     */
    protected function setUp(): void
    {
        $localeData = new LocaleData();
        $localeData->setLocaleCode('fr-FR');
        $localeData->setNumberingSystems(['latn']);
        $localeData->setDefaultNumberingSystem('latn');
        $localeData->setMinimumGroupingDigits(1);

        $this->stubSymbolsData = new NumberSymbolsData();
        $this->stubSymbolsData->setDecimal(',');
        $this->stubSymbolsData->setGroup(' ');
        $this->stubSymbolsData->setList(';');
        $this->stubSymbolsData->setPercentSign('%');
        $this->stubSymbolsData->setMinusSign('-');
        $this->stubSymbolsData->setPlusSign('+');
        $this->stubSymbolsData->setExponential('^');
        $this->stubSymbolsData->setSuperscriptingExponent('E');
        $this->stubSymbolsData->setPerMille('‰');
        $this->stubSymbolsData->setInfinity('∞');
        $this->stubSymbolsData->setNan('NaN');
        $this->stubSymbolsData->setTimeSeparator(':');
        $localeData->setNumberSymbols(['latn' => $this->stubSymbolsData]);

        $localeData->setDecimalPatterns(['latn' => '#,##0.###']);
        $localeData->setPercentPatterns(['latn' => '#,##0.### %']);
        $localeData->setCurrencyPatterns(['latn' => '#,##0.00# ¤']);

        $this->stubCurrencyData = new CurrencyData();
        $this->stubCurrencyData->setIsoCode('PCE');
        $this->stubCurrencyData->setNumericIsoCode(333);
        $this->stubCurrencyData->setDecimalDigits(2);
        $this->stubCurrencyData->setDisplayNames([
            'default' => 'PrestaShop Peace',
            'one' => 'peace',
            'other' => 'peaces',
        ]);
        $this->stubCurrencyData->setSymbols([CurrencyInterface::SYMBOL_TYPE_DEFAULT => 'PS☮', CurrencyInterface::SYMBOL_TYPE_NARROW => '☮']);
        $localeData->setCurrencies(['PCE' => $this->stubCurrencyData]);

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
     */
    public function testGetNumberSymbolsByNumberingSystemWithUnknownNumberingSystem()
    {
        $this->expectException(LocalizationException::class);

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
     */
    public function testGetDecimalPatternWithUnknownNumberingSystem()
    {
        $this->expectException(LocalizationException::class);

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
     */
    public function testGetPercentPatternWithUnknownNumberingSystem()
    {
        $this->expectException(LocalizationException::class);

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
     */
    public function testGetCurrencyPatternWithUnknownNumberingSystem()
    {
        $this->expectException(LocalizationException::class);

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
            $this->stubCurrencyData->getIsoCode(),
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
            $this->stubCurrencyData->getIsoCode(),
            $currencyData->getIsoCode()
        );

        $this->assertNull(
            $this->cldrLocale->getCurrency('FOO'),
            'When asking for an unknown currency data, null should be returned'
        );
    }
}
