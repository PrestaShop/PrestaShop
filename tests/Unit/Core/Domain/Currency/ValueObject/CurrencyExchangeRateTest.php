<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
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
