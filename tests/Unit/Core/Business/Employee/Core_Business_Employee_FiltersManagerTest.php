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

namespace PrestaShop\PrestaShop\Tests\Unit\Core\Business\Employee;

use PrestaShop\PrestaShop\Tests\TestCase\UnitTestCase;
use Core_Business_Employee_FiltersManager as FiltersManager;

class Core_Business_Employee_FiltersManagerTest extends UnitTestCase
{
    private $employee;
    private $idShop;
    private $originalData = [];
    private $data = [];

    public function __construct()
    {
        $filterDatas = json_encode(array(
            'filter1' => 1,
            'filter2' => 2,
            'filter3' => 3,
            'filter4' => 4
        ));

        $this->idShop = 1;
        $this->data = $this->originalData = json_decode($filterDatas, true);
    }

    public function testGet()
    {
        $this->assertEquals(isset($this->data['filter1']) ? $this->data['filter1'] : null, 1);
    }

    public function testSet()
    {
        $this->data['filter5'] = 5;
        $this->assertEquals($this->data['filter5'], 5);
    }

    public function testUnset()
    {
        unset($this->data['filter5']);
        $this->assertArrayNotHasKey('filter5', $this->data);
    }

    public function testRemoveAll()
    {
        $this->data = [];
        $this->assertEmpty($this->data);
    }
}
