<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Integration\Classes;

use PHPUnit\Framework\TestCase;
use SQLUtils;

class SQLUtilsTest extends TestCase
{
    /**
     * @dataProvider providerSQLRetrieveFilter
     */
    public function testSQLRetrieveFilter(array $input, string $expected): void
    {
        $actual = SQLUtils::getSQLRetrieveFilter($input[0], $input[1], $input[2] ?? null);
        $this->assertEquals($expected, $actual);
    }

    public function providerSQLRetrieveFilter(): iterable
    {
        yield [
            ['name', 'a'],
            ' AND `name` = "a"' . PHP_EOL,
        ];
        yield [
            ['price', '18.2'],
            ' AND `price` = "18.2"' . PHP_EOL,
        ];
        yield [
            ['name', '[19.2, 19.8]', 'test.'],
            ' AND `test`.`name` BETWEEN "19.2" AND " 19.8"' . PHP_EOL,
        ];
        yield [
            ['name', '%[19.2]'],
            ' AND `name` LIKE "%19.2"' . PHP_EOL,
        ];
        yield [
            ['name', '>[19.2]'],
            ' AND `name` > "19.2"' . PHP_EOL,
        ];
        yield [
            ['name', '<[19.2]'],
            ' AND `name` < "19.2"' . PHP_EOL,
        ];
        yield [
            ['name', '![19.2]'],
            ' AND `name` != "19.2"' . PHP_EOL,
        ];
        yield [
            ['name', '[19.2|20|25]'],
            ' AND (`name` = "19.2" OR `name` = "20" OR `name` = "25")' . PHP_EOL,
        ];
        // Date range: date-only bounds should include the full last day (fixes #10822)
        yield [
            ['date_add', '[2018-07-01,2018-07-31]'],
            ' AND `date_add` BETWEEN "2018-07-01 00:00:00" AND "2018-07-31 23:59:59"' . PHP_EOL,
        ];
        // Date range with table alias
        yield [
            ['date_add', '[2020-01-01,2020-12-31]', 'main.'],
            ' AND `main`.`date_add` BETWEEN "2020-01-01 00:00:00" AND "2020-12-31 23:59:59"' . PHP_EOL,
        ];
        // Date range with full datetime values should remain untouched
        yield [
            ['date_add', '[2018-07-01 00:00:00,2018-07-31 14:30:00]'],
            ' AND `date_add` BETWEEN "2018-07-01 00:00:00" AND "2018-07-31 14:30:00"' . PHP_EOL,
        ];
    }
}
