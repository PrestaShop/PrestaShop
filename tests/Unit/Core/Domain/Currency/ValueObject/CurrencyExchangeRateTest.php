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

namespace Tests\Unit\Core\Domain\Currency\ValueObject;

use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\Domain\Currency\Exception\CurrencyConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Currency\ValueObject\ExchangeRate;

/**
 * Class CurrencyExchangeRateTest
 */
class CurrencyExchangeRateTest extends TestCase
{
    /**
     * @dataProvider getIncorrectExchangeRates
     */
    public function testItThrowsAnExceptionOnIncorrectExchangeRate($incorrectExchangeRate)
    {
        $this->expectException(CurrencyConstraintException::class);
        $this->expectExceptionCode(CurrencyConstraintException::INVALID_EXCHANGE_RATE);

        $exchangeRate = new ExchangeRate($incorrectExchangeRate);
    }

    public function getIncorrectExchangeRates()
    {
        return [
            [
                0,
            ],
            [
                '-1',
            ],
            [
                '4.294.967.295,000',
            ],
        ];
    }

    /**
     * @dataProvider getCorrectExchangeRates
     */
    public function testItGetsExpectedExchangeRate($correctRate)
    {
        $exchangeRate = new ExchangeRate($correctRate);

        $this->assertEquals($correctRate, $exchangeRate->getValue());
    }

    public function getCorrectExchangeRates()
    {
        yield [1.55];
        yield [1];
        yield [0.55];
    }
}
