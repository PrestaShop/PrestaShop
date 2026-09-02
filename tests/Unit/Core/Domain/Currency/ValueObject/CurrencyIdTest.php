<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */
declare(strict_types=1);

namespace Tests\Unit\Core\Domain\Currency\ValueObject;

use Generator;
use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\Domain\Currency\Exception\CurrencyConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Currency\ValueObject\CurrencyId;

class CurrencyIdTest extends TestCase
{
    /**
     * @dataProvider getValidValues
     *
     * @param int $value
     */
    public function testCreateWithPositiveValue(int $value): void
    {
        $currencyId = new CurrencyId($value);

        $this->assertEquals($value, $currencyId->getValue());
    }

    /**
     * @dataProvider getInvalidValues
     *
     * @param int $value
     */
    public function testItThrowsExceptionWhenProvidingInvalidValue(int $value): void
    {
        $this->expectException(CurrencyConstraintException::class);
        $this->expectExceptionCode(CurrencyConstraintException::INVALID_ID);

        new CurrencyId($value);
    }

    /**
     * @return Generator
     */
    public function getValidValues(): Generator
    {
        yield [
            10,
        ];

        yield [
            15,
        ];
    }

    /**
     * @return Generator
     */
    public function getInvalidValues(): Generator
    {
        yield [
            0,
        ];

        yield [
            -1,
        ];
    }
}
