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
    }
}
