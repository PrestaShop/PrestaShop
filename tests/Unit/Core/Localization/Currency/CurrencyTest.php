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

namespace Tests\Unit\Core\Localization\Currency;

use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\Localization\Currency;
use PrestaShop\PrestaShop\Core\Localization\Exception\LocalizationException;

class CurrencyTest extends TestCase
{
    /**
     * Currency to be tested
     *
     * @var Currency
     */
    protected $currency;

    /**
     * {@inheritDoc}
     */
    protected function setUp(): void
    {
        $this->currency = new Currency(
            true,
            1,
            'EUR',
            978,
            ['fr-FR' => '€'],
            2,
            ['fr-FR' => 'euro']
        );
    }

    /**
     * Given a valid Currency instance
     * When asking if it is active
     * Then the expected boolean value should be returned
     */
    public function testIsActive()
    {
        $this->assertSame(
            true,
            $this->currency->isActive(),
            'Wrong result for isActive()'
        );
    }

    /**
     * Given a valid Currency instance
     * When requesting its conversion rate
     * Then the expected numeric value should be returned
     */
    public function testGetConversionRate()
    {
        $this->assertSame(
            1,
            $this->currency->getConversionRate(),
            'Wrong result for getConversionRate()'
        );
    }

    /**
     * Given a valid Currency instance
     * When requesting its alphabetic ISO code
     * Then the expected code should be returned
     */
    public function testGetIsoCode()
    {
        $this->assertSame(
            'EUR',
            $this->currency->getIsoCode(),
            'Wrong result for getIsoCode()'
        );
    }

    /**
     * Given a valid Currency instance
     * When requesting its numeric ISO code
     * Then the expected code should be returned
     */
    public function testGetNumericIsoCode()
    {
        $this->assertSame(
            978,
            $this->currency->getNumericIsoCode(),
            'Wrong result for getNumericIsoCode()'
        );
    }

    /**
     * Given a valid Currency instance and a valid + known locale code
     * When requesting the currency symbol for the said locale code
     * Then the expected symbol should be returned
     */
    public function testGetSymbol()
    {
        /* @noinspection PhpUnhandledExceptionInspection */
        $this->assertSame(
            '€',
            $this->currency->getSymbol('fr-FR'),
            'Wrong result for getSymbol()'
        );
        /* @noinspection end */
    }

    /**
     * Given a valid Currency instance and un unknown or invalid locale code
     * When requesting the currency symbol for the said locale code
     * Then an exception should be raised
     */
    public function testGetSymbolWithUnknownLocaleCode()
    {
        $this->expectException(LocalizationException::class);

        $this->currency->getSymbol('foobar');
    }

    /**
     * Given a valid Currency instance
     * When requesting its decimal precision
     * Then the expected value should be returned
     */
    public function testGetDecimalPrecision()
    {
        $this->assertSame(
            2,
            $this->currency->getDecimalPrecision(),
            'Wrong result for getDecimalPrecision()'
        );
    }

    /**
     * Given a valid Currency instance and a valid + known locale code
     * When requesting the currency name for the said locale code
     * Then the expected name should be returned
     */
    public function testGetName()
    {
        /* @noinspection PhpUnhandledExceptionInspection */
        $this->assertSame(
            'euro',
            $this->currency->getName('fr-FR'),
            'Wrong result for getName()'
        );
        /* @noinspection end */
    }

    /**
     * Given a valid Currency instance and an invalid or unknown locale code
     * When requesting the currency name for the said locale code
     * Then an exception should be raised
     */
    public function testGetNameWithUnknownLocaleCode()
    {
        $this->expectException(LocalizationException::class);

        $this->currency->getName('foobar');
    }
}
