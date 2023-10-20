<?php
/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/OSL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to https://devdocs.prestashop-project.org/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
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
