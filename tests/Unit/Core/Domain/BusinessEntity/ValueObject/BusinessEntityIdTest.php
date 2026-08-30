<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */
declare(strict_types=1);

namespace Tests\Unit\Core\Domain\BusinessEntity\ValueObject;

use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\Domain\BusinessEntity\Exception\BusinessEntityConstraintException;
use PrestaShop\PrestaShop\Core\Domain\BusinessEntity\ValueObject\BusinessEntityId;

class BusinessEntityIdTest extends TestCase
{
    public function testItExposesTheValue(): void
    {
        $businessEntityId = new BusinessEntityId(42);

        $this->assertSame(42, $businessEntityId->getValue());
    }

    /**
     * @dataProvider provideInvalidIds
     */
    public function testItRejectsIdsLowerThanOrEqualToZero(int $invalidId): void
    {
        $this->expectException(BusinessEntityConstraintException::class);
        $this->expectExceptionCode(BusinessEntityConstraintException::INVALID_ID);

        new BusinessEntityId($invalidId);
    }

    /**
     * @return array<int, array{int}>
     */
    public function provideInvalidIds(): array
    {
        return [
            [0],
            [-1],
        ];
    }
}
