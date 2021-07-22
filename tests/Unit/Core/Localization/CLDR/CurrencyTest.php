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
        $currencyData->setNumericIsoCode(333);
        $currencyData->setDecimalDigits(2);
        $currencyData->setDisplayNames(['default' => 'PrestaShop Peace', 'one' => 'peace', 'other' => 'peaces']);
        $currencyData->setSymbols([CurrencyInterface::SYMBOL_TYPE_DEFAULT => 'PS☮', CurrencyInterface::SYMBOL_TYPE_NARROW => '☮']);

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

    public function testGetSymbolsWithInvalidSymbolType()
    {
        $this->expectException(LocalizationException::class);

        $this->cldrCurrency->getSymbol('foobar');
    }
}
