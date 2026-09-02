<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */
declare(strict_types=1);

namespace Tests\Unit\Core\Domain\Customer\Group\ValueObject;

use Generator;
use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\Domain\Customer\Group\Exception\GroupConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Customer\Group\ValueObject\GroupId;

class GroupIdTest extends TestCase
{
    /**
     * @dataProvider getValidValues
     *
     * @param int $value
     */
    public function testCreateWithPositiveValue(int $value): void
    {
        $groupId = new GroupId($value);

        $this->assertEquals($value, $groupId->getValue());
    }

    /**
     * @dataProvider getInvalidValues
     *
     * @param int $value
     */
    public function testItThrowsExceptionWhenProvidingInvalidValue(int $value): void
    {
        $this->expectException(GroupConstraintException::class);
        $this->expectExceptionCode(GroupConstraintException::INVALID_ID);

        new GroupId($value);
    }

    /**
     * @return Generator
     */
    public function getValidValues(): Generator
    {
        yield [
            1,
        ];

        yield [
            150,
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
            -10,
        ];
    }
}
