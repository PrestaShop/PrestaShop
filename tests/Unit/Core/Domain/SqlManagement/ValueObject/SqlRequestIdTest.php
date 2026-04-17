<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\Core\Domain\SqlManagement\ValueObject;

use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\Domain\SqlManagement\Exception\SqlRequestException;
use PrestaShop\PrestaShop\Core\Domain\SqlManagement\ValueObject\SqlRequestId;

class SqlRequestIdTest extends TestCase
{
    public function testValidValues(): void
    {
        $vo = new SqlRequestId(1);
        $this->assertEquals(1, $vo->getValue());
    }

    /**
     * @dataProvider getInvalidValues
     */
    public function testInvalidValues($sqlRequestId): void
    {
        $this->expectException(SqlRequestException::class);
        new SqlRequestId($sqlRequestId);
    }

    public function getInvalidValues(): iterable
    {
        return [
            [0],
            ['-1'],
            ['1.1'],
            ['a'],
            ['+'],
        ];
    }
}
