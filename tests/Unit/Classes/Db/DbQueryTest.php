<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\Classes\Db;

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
     * @param DbQuery $dbQuery
     * @param string $expectedValue
     *
     * @dataProvider providerBuild
     */
    public function testBuild(DbQuery $dbQuery, string $expectedValue): void
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
                'SELECT *' . self::BREAK_LINE . 'FROM (SELECT *' . self::BREAK_LINE . 'FROM `' . _DB_PREFIX_ . 'product` p' . self::BREAK_LINE . 'WHERE (p.active = 1)' . self::BREAK_LINE . ') p' . self::BREAK_LINE . 'WHERE (p.visibility in ("both", "search"))',
            ],
        ];
    }
}
