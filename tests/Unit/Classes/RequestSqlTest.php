<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace Tests\Unit\Classes;

use PHPUnit\Framework\TestCase;
use RequestSql;

class RequestSqlTest extends TestCase
{
    /**
     * @dataProvider provider
     */
    public function testValidateSql(string $sql, bool $valid): void
    {
        $requestSql = $this->createRequestSqlMock();
        $parser = $requestSql->parsingSql($sql);
        $this->assertSame($valid, $requestSql->validateParser($parser, false, $sql));
    }

    public function provider(): iterable
    {
        yield ['select * from ps_table', true];
        yield ['select * from ps_notexistingtable', false];
        yield ['select * from ps_table WHERE EXISTS (select 1 from ps_table)', true];
        yield ['select * from ps_table WHERE EXISTS 1', false];
        yield ['wrong * from ps_table', false];
    }

    private function createRequestSqlMock()
    {
        $requestSql = $this->getMockBuilder(RequestSql::class)
            ->onlyMethods(['getTables'])
            ->getMock()
        ;

        $requestSql->method('getTables')->willReturn([
            'ps_table',
        ]);

        return $requestSql;
    }
}
