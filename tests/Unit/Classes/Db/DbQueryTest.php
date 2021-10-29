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
 * needs please refer to https://devdocs.prestashop.com/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

declare(strict_types=1);

namespace Tests\Unit\Classes;

if (!defined('_DB_PREFIX_')) {
    define('_DB_PREFIX_', 'test_');
}

use DbQuery;
use PHPUnit\Framework\TestCase;

class DbQueryTest extends TestCase
{
    public const BREAK_LINE = "\n";

    /*
     * get DbQuery object
     *
     * @return DbQuery
     */
    private function getDbQueryInstance(): DbQuery
    {
        return new DbQuery();
    }

    /**
     * @param mixed $type
     * @param string $expectedType
     *
     * @dataProvider providerType
     */
    public function testType($type, string $expectedType): void
    {
        $dbQuery = $this->getDbQueryInstance();
        $dbQuery->type($type);
        $this->assertSame($expectedType, $dbQuery->getQuery()['type']);
    }

    /**
     * @param mixed $fields
     * @param array $expectedSelect
     *
     * @dataProvider providerSelect
     */
    public function testSelect($fields, array $expectedSelect): void
    {
        $dbQuery = $this->getDbQueryInstance();
        $dbQuery->select($fields);
        $this->assertSame($expectedSelect, $dbQuery->getQuery()['select']);
    }

    /**
     * @param string $table
     * @param string|null $alias
     * @param array $expectedValue
     *
     * @dataProvider providerFrom
     */
    public function testFrom(string $table, ?string $alias, array $expectedValue): void
    {
        $dbQuery = $this->getDbQueryInstance();
        $dbQuery->from($table, $alias);
        $this->assertSame($expectedValue, $dbQuery->getQuery()['from']);
    }

    /**
     * @param DbQuery $table
     * @param string|null $alias
     * @param array $expectedValue
     *
     * @dataProvider providerFromWithDbQuery
     */
    public function testFromWithDbQuery(DbQuery $table, ?string $alias, array $expectedValue): void
    {
        $dbQuery = $this->getDbQueryInstance();
        $dbQuery->from($table, $alias);
        $this->assertSame($expectedValue, $dbQuery->getQuery()['from']);
    }

    /**
     * @param mixed $dbQuery
     * @param mixed $expectedValue
     *
     * @dataProvider providerBuild
     */
    public function testBuild(DbQuery $dbQuery, $expectedValue)
    {
        $this->assertSame(trim($dbQuery->build()), trim($expectedValue));
    }

    public function providerType(): array
    {
        return [
            ['SELECT', 'SELECT'],
            ['DELETE', 'DELETE'],
            ['INVALID_TYPE', 'SELECT'],
            ['select', 'SELECT'],
            ['delete', 'SELECT'],
            [666, 'SELECT'],
            [false, 'SELECT'],
            [null, 'SELECT'],
        ];
    }

    public function providerSelect(): array
    {
        return [
            ['FIELD1', [
                0 => 'FIELD1',
            ]],
            ['FIELD1, FIELD2', [
                0 => 'FIELD1, FIELD2',
            ]],
            [null, []],
            [false, []],
        ];
    }

    public function providerFrom(): array
    {
        return [
            ['table_name', 'alias', [
                0 => '`' . _DB_PREFIX_ . 'table_name` alias',
            ]],
            ['table_name', null, [
                0 => '`' . _DB_PREFIX_ . 'table_name`',
            ]],
        ];
    }

    public function providerFromWithDbQuery(): array
    {
        return [
            [
                (new DbQuery())->select('*')->from('product', 'p'),
                'alias',
                [
                    0 => '(SELECT *' . self::BREAK_LINE . 'FROM `' . _DB_PREFIX_ . 'product` p' . self::BREAK_LINE . ') alias',
                ],
            ],
            [
                (new DbQuery())->select('*')->from('order'),
                'alias',
                [
                    0 => '(SELECT *' . self::BREAK_LINE . 'FROM `' . _DB_PREFIX_ . 'order`' . self::BREAK_LINE . ') alias',
                ],
            ],
        ];
    }

    public function providerBuild(): array
    {
        $simpleSelectQuery = $this->getDbQueryInstance()
            ->select('id_product')
            ->from('product')
        ;

        $simpleSelectQueryWithAlias = $this->getDbQueryInstance()
            ->select('p.name')
            ->from('product', 'p')
        ;

        $simpleSelectQueryWhere = $this->getDbQueryInstance()
            ->select('id_product')
            ->from('product')
            ->where('id_category_default = 1')
        ;

        $simpleSelectQueryWithAliasandWhere = $this->getDbQueryInstance()
            ->select('p.*')
            ->from('product', 'p')
            ->where('p.reference = "testreference"')
        ;

        $subQuery = new DbQuery();
        $subQuery->select('*');
        $subQuery->from('product', 'p');
        $subQuery->where('p.active = 1');

        $selectWithSubQuery = new DbQuery();
        $selectWithSubQuery->select('*');
        $selectWithSubQuery->from($subQuery, 'p');
        $selectWithSubQuery->where('p.visibility in ("both", "search")');

        return [
            [
                $simpleSelectQuery,
                'SELECT id_product' . self::BREAK_LINE . 'FROM `' . _DB_PREFIX_ . 'product`',
            ],
            [
                $simpleSelectQueryWhere,
                'SELECT id_product' . self::BREAK_LINE . 'FROM `' . _DB_PREFIX_ . 'product`' . self::BREAK_LINE . 'WHERE (id_category_default = 1)',
            ],
            [
                $simpleSelectQueryWithAlias,
                'SELECT p.name' . self::BREAK_LINE . 'FROM `' . _DB_PREFIX_ . 'product` p',
            ],
            [
                $simpleSelectQueryWithAliasandWhere,
                'SELECT p.*' . self::BREAK_LINE . 'FROM `' . _DB_PREFIX_ . 'product` p' . self::BREAK_LINE . 'WHERE (p.reference = "testreference")',
            ],
            [
                $selectWithSubQuery,
                'SELECT *' . self::BREAK_LINE . 'FROM (SELECT *' . self::BREAK_LINE . 'FROM `test_product` p' . self::BREAK_LINE . 'WHERE (p.active = 1)' . self::BREAK_LINE . ') p' . self::BREAK_LINE . 'WHERE (p.visibility in ("both", "search"))',
            ],
        ];
    }
}
