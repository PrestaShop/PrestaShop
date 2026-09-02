<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\Core\Foundation\Database\EntityManager;

use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\Foundation\Database\DatabaseInterface;
use PrestaShop\PrestaShop\Core\Foundation\Database\EntityManager\QueryBuilder;

class QueryBuilderTest extends TestCase
{
    private $queryBuilder;

    protected function setUp(): void
    {
        $mockDb = $this->createMock(DatabaseInterface::class);
        $mockDb->method('escape')->withAnyParameters()->willReturn('escaped');

        $this->queryBuilder = new QueryBuilder(
            $mockDb
        );
    }

    public function testQuoteNumberNotQuoted()
    {
        $this->assertEquals('escaped', $this->queryBuilder->quote(42));
        $this->assertEquals('escaped', $this->queryBuilder->quote(4.2));
    }

    public function testQuoteStringQuoted()
    {
        $this->assertEquals('\'escaped\'', $this->queryBuilder->quote('hello'));
    }

    public function testBuildWhereConditionsANDJustOneCondition()
    {
        $this->assertEquals("name = 'escaped'", $this->queryBuilder->buildWhereConditions('AND', [
            'name' => 'some string',
        ]));
    }

    public function testBuildWhereConditionsANDTwoConditions()
    {
        $this->assertEquals("name = 'escaped' AND num = escaped", $this->queryBuilder->buildWhereConditions('AND', [
            'name' => 'some string',
            'num' => 123456,
        ]));
    }

    public function testBuildWhereConditionsArrayValue()
    {
        $this->assertEquals("stuff IN ('escaped', escaped, escaped)", $this->queryBuilder->buildWhereConditions('AND', [
            'stuff' => ['a string', 123, 456452],
        ]));
    }
}
