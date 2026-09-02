<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace Tests\Unit\Core\Domain\Currency\ValueObject;

use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\Domain\Currency\Exception\CurrencyConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Currency\ValueObject\NumericIsoCode;

class CurrencyNumericIsoCodeTest extends TestCase
{
    /**
     * @dataProvider getIncorrectNumericIsoCodes
     */
    public function testItThrowsAnExceptionOnIncorrectIsoCodeRegex($incorrectNumericIsoCode)
    {
        $this->expectException(CurrencyConstraintException::class);
        $this->expectExceptionCode(CurrencyConstraintException::INVALID_NUMERIC_ISO_CODE);

        $currencyNumericIsoCode = new NumericIsoCode($incorrectNumericIsoCode);
    }

    public function getIncorrectNumericIsoCodes()
    {
        return [
            [
                '',
            ],
            [
                'LTUU',
            ],
            [
                'L',
            ],
            [
                null,
            ],
            [
                false,
            ],
            [
                [],
            ],
            [
                0,
            ],
            [
                '0',
            ],
            [
                '-51',
            ],
            [
                -42,
            ],
            [
                8,
            ],
            [
                '8',
            ],
            [
                '08',
            ],
        ];
    }

    /**
     * @dataProvider getCorrectNumericIsoCodes
     */
    public function testItReturnsRightIsoCode($correctNumericIsoCode)
    {
        $currencyNumericIsoCode = new NumericIsoCode($correctNumericIsoCode);

        $this->assertEquals($correctNumericIsoCode, $currencyNumericIsoCode->getValue());
    }

    public function getCorrectNumericIsoCodes()
    {
        return [
            [
                '008',
            ],
            [
                '981',
            ],
            [
                '049',
            ],
        ];
    }
}
