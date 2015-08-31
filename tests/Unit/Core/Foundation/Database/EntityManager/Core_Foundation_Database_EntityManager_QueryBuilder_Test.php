<?php
/**
 * 2007-2015 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
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
 *  @author 	PrestaShop SA <contact@prestashop.com>
 *  @copyright  2007-2015 PrestaShop SA
 *  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *  International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShop\PrestaShop\Tests\Unit\Core\Foundation\Database\EntityManager;

use PrestaShop\PrestaShop\Tests\TestCase\UnitTestCase;
use Core_Foundation_Database_EntityManager_QueryBuilder;
use Phake;

class Core_Foundation_Database_EntityManager_QueryBuilder_Test extends UnitTestCase
{
    private $queryBuilder;

    public function setup()
    {
        $mockDb = Phake::mock('Core_Foundation_Database_DatabaseInterface');

        Phake::when($mockDb)->escape(Phake::anyParameters())->thenReturn('escaped');

        $this->queryBuilder = new Core_Foundation_Database_EntityManager_QueryBuilder(
            $mockDb
        );
    }

    public function test_quote_number_not_quoted()
    {
        $this->assertEquals('escaped', $this->queryBuilder->quote(42));
        $this->assertEquals('escaped', $this->queryBuilder->quote(4.2));
    }

    public function test_quote_string_quoted()
    {
        $this->assertEquals('\'escaped\'', $this->queryBuilder->quote('hello'));
    }

    public function test_buildWhereConditions_AND_just_one_condition()
    {
        $this->assertEquals("name = 'escaped'", $this->queryBuilder->buildWhereConditions('AND', array(
            'name' => 'some string'
        )));
    }

    public function test_buildWhereConditions_AND_two_conditions()
    {
        $this->assertEquals("name = 'escaped' AND num = escaped", $this->queryBuilder->buildWhereConditions('AND', array(
            'name' => 'some string',
            'num' => 123456
        )));
    }

    public function test_buildWhereConditions_arrayValue()
    {
        $this->assertEquals("stuff IN ('escaped', escaped, escaped)", $this->queryBuilder->buildWhereConditions('AND', array(
            'stuff' => array('a string', 123, 456452)
        )));
    }
}
