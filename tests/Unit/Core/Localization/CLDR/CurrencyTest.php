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
use PrestaShop\PrestaShop\Core\Localization\Exception\LocalizationException;

class CurrencyTest extends TestCase
{
    /**
     * An instance of the tested CLDR Currency class
     *
     * This Currency instance has been populated with known data/dependencies.
     *
     * @var Currency
     */
    protected $cldrCurrency;

    /**
     * @inheritDoc
     */
    protected function setUp()
    {
        $currencyData                 = new CurrencyData();
        $currencyData->isoCode        = 'PCE';
        $currencyData->numericIsoCode = 333;
        $currencyData->decimalDigits  = 2;
        $currencyData->displayNames   = ['default' => 'PrestaShop Peace', 'one' => 'peace', 'other' => 'peaces'];
        $currencyData->symbols        = [Currency::SYMBOL_TYPE_DEFAULT => 'PS☮', Currency::SYMBOL_TYPE_NARROW => '☮'];

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
            333,
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

    /**
     * @expectedException \PrestaShop\PrestaShop\Core\Localization\Exception\LocalizationException
     */
    public function testGetSymbolsWithInvalidSymbolType()
    {
        $this->cldrCurrency->getSymbol('foobar');
    }
}
