<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\Core\Domain\Alias\ValueObject;

use Generator;
use PHPUnit\Framework\Assert;
use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\Domain\Alias\Exception\AliasConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Alias\ValueObject\AliasId;

class AliasIdTest extends TestCase
{
    public function testItIsSuccessfullyConstructed(): void
    {
        $aliasId = new AliasId(123321);
        Assert::assertSame(123321, $aliasId->getValue());
    }

    /**
     * @dataProvider getInvalidValues
     *
     * @param int $value
     */
    public function testItThrowsExceptionWhenInvalidValueIsProvided(int $value): void
    {
        $this->expectException(AliasConstraintException::class);
        $this->expectExceptionCode(AliasConstraintException::INVALID_ID);

        new AliasId($value);
    }

    /**
     * @return Generator
     */
    public function getInvalidValues(): Generator
    {
        yield [0];
        yield [-1];
        yield [-9999];
    }
}
