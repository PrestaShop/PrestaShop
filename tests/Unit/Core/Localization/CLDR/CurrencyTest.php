<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\Core\Localization\CLDR;

use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\Localization\CLDR\Currency;
use PrestaShop\PrestaShop\Core\Localization\CLDR\CurrencyData;
use PrestaShop\PrestaShop\Core\Localization\CLDR\CurrencyInterface;
use PrestaShop\PrestaShop\Core\Localization\Exception\LocalizationException;

class CurrencyTest extends TestCase
{
    /**
     * An instance of the tested CLDR Currency class
     *
     * This Currency instance has been populated with known data/dependencies.
     *
     * @var CurrencyInterface
     */
    protected $cldrCurrency;

    /**
     * {@inheritDoc}
     */
    protected function setUp(): void
    {
        $currencyData = new CurrencyData();
        $currencyData->setIsoCode('PCE');
        $currencyData->setNumericIsoCode('333');
        $currencyData->setDecimalDigits(2);
        $currencyData->setDisplayNames(['default' => 'PrestaShop Peace', 'one' => 'peace', 'other' => 'peaces']);
        $currencyData->setSymbols([
            CurrencyInterface::SYMBOL_TYPE_DEFAULT => 'PS☮',
            CurrencyInterface::SYMBOL_TYPE_NARROW => '☮',
        ]);

        $this->cldrCurrency = new Currency($currencyData);
    }

    /**
     * Given a valid CLDR Currency object
     * When asking the ISO code
     * Then the expected value should be returned
     */
    public function testGetIsoCode()
    {
        $this->assertSame(
            'PCE',
            $this->cldrCurrency->getIsoCode()
        );
    }

    /**
     * Given a valid CLDR Currency object
     * When asking the numeric ISO code
     * Then the expected value should be returned
     */
    public function testGetNumericIsoCode()
    {
        $this->assertSame(
            '333',
            $this->cldrCurrency->getNumericIsoCode()
        );
    }

    /**
     * Given a valid CLDR Currency object
     * When asking the decimal digits (number of digits to use in the fraction part of the currency)
     * Then the expected value should be returned
     */
    public function testGetDecimalDigits()
    {
        $this->assertSame(
            2,
            $this->cldrCurrency->getDecimalDigits()
        );
    }

    /**
     * Given a valid CLDR Currency object and a valid count context (no count context is valid)
     * When asking the display name of the currency for this count context
     * Then the expected value should be returned
     */
    public function testGetDisplayName()
    {
        $this->assertSame(
            'PrestaShop Peace',
            $this->cldrCurrency->getDisplayName('default')
        );
        $this->assertSame(
            'peace',
            $this->cldrCurrency->getDisplayName('one')
        );
        $this->assertSame(
            'PrestaShop Peace',
            $this->cldrCurrency->getDisplayName()
        );
    }

    /**
     * Given a valid CLDR Currency object and a valid symbol type
     * When asking the currency symbol of this type
     * Then the expected symbol should be returned
     *
     * @throws LocalizationException
     */
    public function testGetSymbols()
    {
        $this->assertSame(
            '☮',
            $this->cldrCurrency->getSymbol()
        );
        $this->assertSame(
            'PS☮',
            $this->cldrCurrency->getSymbol('default')
        );
    }

    public function testGetSymbolsWithInvalidSymbolType()
    {
        $this->expectException(LocalizationException::class);

        $this->cldrCurrency->getSymbol('foobar');
    }

    /**
     * @dataProvider getEmptyDisplayNames
     */
    public function testFallbackWhenEmptyDisplayNames($displayNamesData): void
    {
        $currencyData = new CurrencyData();
        $currencyData->setIsoCode('PCE');
        $currencyData->setNumericIsoCode('333');
        $currencyData->setDecimalDigits(2);
        $currencyData->setDisplayNames($displayNamesData);
        $currencyData->setSymbols([
            CurrencyInterface::SYMBOL_TYPE_DEFAULT => 'PS☮',
            CurrencyInterface::SYMBOL_TYPE_NARROW => '☮',
        ]);

        $cldrCurrency = new Currency($currencyData);
        $this->assertEquals(
            'PCE',
            $cldrCurrency->getDisplayName()
        );
        $this->assertEquals(
            'PCE',
            $cldrCurrency->getDisplayName(CurrencyInterface::DISPLAY_NAME_COUNT_DEFAULT)
        );
        $this->assertEquals(
            'PCE',
            $cldrCurrency->getDisplayName(CurrencyInterface::DISPLAY_NAME_COUNT_ONE)
        );
        $this->assertEquals(
            'PCE',
            $cldrCurrency->getDisplayName(CurrencyInterface::DISPLAY_NAME_COUNT_OTHER)
        );
    }

    public function getEmptyDisplayNames(): iterable
    {
        yield 'empty array' => [[]];
        yield 'null values' => [null];
        yield 'array without proper context' => [['useless_context' => 'toto']];
    }
}
