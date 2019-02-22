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

namespace LegacyTests\Endpoints;

use Tools;

class AjaxTest extends AbstractEndpointAdminTest
{
    protected function setUp()
    {
        parent::setUp();
        $this->employeeLogin();
    }

    public function testAjaxEndpointForReferrersFilterCase()
    {
        $_GET['ajaxReferrers'] = 1;
        $_GET['ajaxProductFilter'] = 1;
        $_GET['token'] = Tools::getAdminTokenLite('AdminReferrers');

        ob_start();
        require _PS_ROOT_DIR_ . '/admin-dev/ajax.php';
        $output = json_decode(ob_get_clean());

        $this->assertTrue(is_array($output));
        if (count($output)) {
            $firstElem = reset($output);
            // Test some properties, not all of them
            $this->assertObjectHasAttribute('id_product', $firstElem);
            $this->assertObjectHasAttribute('product_name', $firstElem);
            $this->assertObjectHasAttribute('sales', $firstElem);
        }
    }

    public function testAjaxEndpointForReferrersFillCase()
    {
        $_GET['ajaxReferrers'] = 1;
        $_GET['ajaxFillProducts'] = 1;
        $_GET['token'] = Tools::getAdminTokenLite('AdminReferrers');

        ob_start();
        require _PS_ROOT_DIR_ . '/admin-dev/ajax.php';
        $output = json_decode(ob_get_clean());
        $this->assertTrue(is_array($output)); 
    }

    public function testAjaxEndpointForGettingNotifications()
    {
        $_POST['getNotifications'] = 1;

        ob_start();
        require _PS_ROOT_DIR_ . '/admin-dev/ajax.php';
        $output = json_decode(ob_get_clean());

        $this->assertNotNull($output);
        $this->assertObjectHasAttribute('order', $output);
        $this->assertObjectHasAttribute('customer_message', $output);
        $this->assertObjectHasAttribute('customer', $output);
    }

    public function testAjaxEndpointForMarkingNotificationAsRead()
    {
        $_POST['updateElementEmployee'] = 1;
        $_POST['updateElementEmployeeType'] = 'order';'incompatibleValue';

        ob_start();
        require _PS_ROOT_DIR_ . '/admin-dev/ajax.php';
        $output = ob_get_clean();

        $this->assertSame('1', $output);
    }

    public function testAjaxEndpointForMarkingNotificationAsReadButWithWrongInputData()
    {
        $_POST['updateElementEmployee'] = 1;
        $_POST['updateElementEmployeeType'] = 'incompatibleValue';

        ob_start();
        require _PS_ROOT_DIR_ . '/admin-dev/ajax.php';
        $output = ob_get_clean();

        $this->assertEmpty($output);
    }
}
