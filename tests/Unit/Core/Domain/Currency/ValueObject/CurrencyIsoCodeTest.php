<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace Tests\Unit\Core\Domain\Currency\ValueObject;

use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\Domain\Currency\Exception\CurrencyConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Currency\ValueObject\AlphaIsoCode;

/**
 * Class CurrencyIsoCodeTest
 */
class CurrencyIsoCodeTest extends TestCase
{
    /**
     * @dataProvider getIncorrectIsoCodes
     */
    public function testItThrowsAnExceptionOnIncorrectIsoCodeRegex($incorrectIsoCode)
    {
        $this->expectException(CurrencyConstraintException::class);
        $this->expectExceptionCode(CurrencyConstraintException::INVALID_ISO_CODE);

        $currencyIsoCode = new AlphaIsoCode($incorrectIsoCode);
    }

    public function getIncorrectIsoCodes()
    {
        return [
            [
                '',
            ],
            [
                'LTUU',
            ],
            [
                '12345',
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
        ];
    }

    public function testItReturnsRightIsoCode()
    {
        $currencyIsoCode = new AlphaIsoCode('LTU');

        $this->assertEquals('LTU', $currencyIsoCode->getValue());
    }
}
