<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\PrestaShopBundle\Doctrine\Type;

use DateTime;
use Doctrine\DBAL\Platforms\PostgreSQLPlatform;
use Doctrine\DBAL\Types\Type;
use PHPUnit\Framework\TestCase;
use PrestaShopBundle\Doctrine\Type\ZeroDateAwareDateTimeType;
use PrestaShopBundle\Doctrine\Type\ZeroDateAwareDateType;

class ZeroDateAwareTypeTest extends TestCase
{
    /**
     * @dataProvider getTypes
     */
    public function testConvertToPHPValueWithPostgreSQLZeroDateReturnsNull(Type $type): void
    {
        $this->assertNull($type->convertToPHPValue('-infinity', new PostgreSQLPlatform()));
    }

    /**
     * @dataProvider getTypes
     */
    public function testConvertToPHPValueWithNullReturnsNull(Type $type): void
    {
        $this->assertNull($type->convertToPHPValue(null, new PostgreSQLPlatform()));
    }

    /**
     * @dataProvider getDates
     */
    public function testConvertToPHPValueWithDateReturnsDateTime(Type $type, string $value, string $expected): void
    {
        $date = $type->convertToPHPValue($value, new PostgreSQLPlatform());

        $this->assertInstanceOf(DateTime::class, $date);
        $this->assertSame($expected, $date->format('Y-m-d H:i:s'));
    }

    public function getTypes(): iterable
    {
        yield [new ZeroDateAwareDateType()];
        yield [new ZeroDateAwareDateTimeType()];
    }

    public function getDates(): iterable
    {
        yield [new ZeroDateAwareDateType(), '2021-10-10', '2021-10-10 00:00:00'];
        yield [new ZeroDateAwareDateTimeType(), '2021-10-10 12:34:56', '2021-10-10 12:34:56'];
    }
}
