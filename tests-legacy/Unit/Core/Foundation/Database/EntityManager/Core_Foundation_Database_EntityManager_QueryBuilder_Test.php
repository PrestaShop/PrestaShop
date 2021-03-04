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

namespace LegacyTests\Unit\Core\Foundation\Database\EntityManager;

use LegacyTests\TestCase\UnitTestCase;
use Phake;
use PrestaShop\PrestaShop\Core\Foundation\Database\EntityManager\QueryBuilder;

class Core_Foundation_Database_EntityManager_QueryBuilder_Test extends UnitTestCase
{
    private $queryBuilder;

    protected function setUp()
    {
        $mockDb = Phake::mock('\\PrestaShop\\PrestaShop\\Core\\Foundation\\Database\\DatabaseInterface');

        Phake::when($mockDb)->escape(Phake::anyParameters())->thenReturn('escaped');

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
        $this->assertEquals("name = 'escaped'", $this->queryBuilder->buildWhereConditions('AND', array(
            'name' => 'some string',
        )));
    }

    public function testBuildWhereConditionsANDTwoConditions()
    {
        $this->assertEquals("name = 'escaped' AND num = escaped", $this->queryBuilder->buildWhereConditions('AND', array(
            'name' => 'some string',
            'num' => 123456,
        )));
    }

    public function testBuildWhereConditionsArrayValue()
    {
        $this->assertEquals("stuff IN ('escaped', escaped, escaped)", $this->queryBuilder->buildWhereConditions('AND', array(
            'stuff' => array('a string', 123, 456452),
        )));
    }
}
