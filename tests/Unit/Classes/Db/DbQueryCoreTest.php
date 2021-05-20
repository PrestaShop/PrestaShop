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

use DbQuery;
use PHPUnit\Framework\TestCase;

class DbQueryFake extends DbQuery
{
    public function getQuery()
    {
        return $this->query;
    }
}

class DbQueryCoreTest extends TestCase
{
    public DbQuery $dbQuery;

    public function __construct($name = null, array $data = [], $dataName = '')
    {
        $this->dbQuery = new DbQueryFake();
        parent::__construct($name, $data, $dataName);
    }

    /**
     * @param mixed $type
     * @param string $expectedType
     *
     * @dataProvider providerType
     */
    public function testType($type, string $expectedType): void
    {
        $dbQuery = $this->dbQuery;
        $dbQuery->type($type);
        $this->assertSame($expectedType, $dbQuery->getQuery()['type']);
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
}
