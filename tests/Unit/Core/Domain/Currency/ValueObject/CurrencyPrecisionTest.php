<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace Tests\Unit\Core\Domain\Currency\ValueObject;

use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\Domain\Currency\Exception\CurrencyConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Currency\ValueObject\Precision;

class CurrencyPrecisionTest extends TestCase
{
    /**
     * @dataProvider getIncorrectPrecision
     */
    public function testItThrowsAnExceptionOnIncorrectIsoCodeRegex($incorrectPrecision)
    {
        $this->expectException(CurrencyConstraintException::class);
        $this->expectExceptionCode(CurrencyConstraintException::INVALID_PRECISION);

        new Precision($incorrectPrecision);
    }

    public function getIncorrectPrecision()
    {
        return [
            [
                '-42',
            ],
            [
                -42,
            ],
            [
                7,
            ],
            [
                '981',
            ],
            [
                49,
            ],
            [
                8,
            ],
        ];
    }

    /**
     * @dataProvider getCorrectPrecisions
     */
    public function testItReturnsRightPrecision($correctNumericIsoCode, $expectedValue)
    {
        $precision = new Precision($correctNumericIsoCode);

        $this->assertEquals($expectedValue, $precision->getValue());
    }

    public function getCorrectPrecisions()
    {
        return [
            [
                '006',
                6,
            ],
            [
                '6',
                6,
            ],
            [
                '2',
                2,
            ],
            [
                '0',
                0,
            ],
            [
                4,
                4,
            ],
            [
                0,
                0,
            ],
        ];
    }
}
