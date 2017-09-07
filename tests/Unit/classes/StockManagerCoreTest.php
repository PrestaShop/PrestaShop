<?php
/**
 * 2007-2017 PrestaShop
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
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2017 PrestaShop SA
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShop\PrestaShop\Tests\Unit\Classes;

/**
 * @group stock_management
 */
class StockManagerCoreTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @param $warehouseIds
     * @param $expectedNormalizedWarehouseIds
     * @dataProvider getWarehouseIds
     */
    public function testPopulateQuantityFields($warehouseIds, $expectedNormalizedWarehouseIds) {
        $stockManager = new \StockManagerCore;

        $normalizedWarehouseIds = $stockManager->normalizeWarehouseIds($warehouseIds);
        $this->assertInternalType('array', $normalizedWarehouseIds,
            'The normalized warehouse ids should be of an array');

        $this->assertEquals($expectedNormalizedWarehouseIds, $normalizedWarehouseIds);
    }

    public function getWarehouseIds()
    {
        return array(
            array(array(''), array(0)),
            array(null, array()),
            array(array("1"), array(1)),
            array("1", array(1)),
        );
    }
}
