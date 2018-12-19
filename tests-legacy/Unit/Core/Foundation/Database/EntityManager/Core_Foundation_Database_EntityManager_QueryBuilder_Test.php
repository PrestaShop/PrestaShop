<?php
/**
 * 2007-2018 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
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
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2018 PrestaShop SA
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace LegacyTests\Unit\Core\Foundation\Database\EntityManager;

use LegacyTests\TestCase\UnitTestCase;
use PrestaShop\PrestaShop\Core\Foundation\Database\EntityManager\QueryBuilder;
use Phake;

class Core_Foundation_Database_EntityManager_QueryBuilder_Test extends UnitTestCase
{
    private $queryBuilder;

    public function setUp()
    {
        $mockDb = Phake::mock('\\PrestaShop\\PrestaShop\\Core\\Foundation\\Database\\DatabaseInterface');

        Phake::when($mockDb)->escape(Phake::anyParameters())->thenReturn('escaped');

        $this->queryBuilder = new QueryBuilder(
            $mockDb
        );
    }

    public function testQuoteNumberNotQuoted()
    {
        static::assertEquals('escaped', $this->queryBuilder->quote(42));
        static::assertEquals('escaped', $this->queryBuilder->quote(4.2));
    }

    public function testQuoteStringQuoted()
    {
        static::assertEquals('\'escaped\'', $this->queryBuilder->quote('hello'));
    }

    public function testBuildWhereConditionsANDJustOneCondition()
    {
        static::assertEquals("name = 'escaped'", $this->queryBuilder->buildWhereConditions('AND', array(
            'name' => 'some string',
        )));
    }

    public function testBuildWhereConditionsANDTwoConditions()
    {
        static::assertEquals("name = 'escaped' AND num = escaped", $this->queryBuilder->buildWhereConditions('AND', array(
            'name' => 'some string',
            'num' => 123456,
        )));
    }

    public function testBuildWhereConditionsArrayValue()
    {
        static::assertEquals("stuff IN ('escaped', escaped, escaped)", $this->queryBuilder->buildWhereConditions('AND', array(
            'stuff' => array('a string', 123, 456452),
        )));
    }
}
