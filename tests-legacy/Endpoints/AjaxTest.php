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

namespace LegacyTests\Endpoints;

use Context;
use Tools;

class AjaxTest extends AbstractEndpointAdminTest
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->employeeLogin();
    }

    // Referrers calls

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

    // Import calls

    public function testAjaxEndpointForImportAvailableFields()
    {
        $_GET['getAvailableFields'] = 1;
        $_GET['entity'] = 'product';
        $_GET['token'] = Tools::getAdminTokenLite('AdminImport');

        ob_start();
        require _PS_ROOT_DIR_ . '/admin-dev/ajax.php';
        $output = json_decode(ob_get_clean());
        $this->assertTrue(is_array($output));
    }

    public function testAjaxEndpointForProductPack()
    {
        $_GET['ajaxProductPackItems'] = 1;
        $_GET['token'] = Tools::getAdminTokenLite('AdminProducts');

        ob_start();
        require _PS_ROOT_DIR_ . '/admin-dev/ajax.php';
        $output = json_decode(ob_get_clean());
        $this->assertTrue(is_array($output));
    }

    public function testAjaxEndpointForCategoryTree()
    {
        $_GET['getChildrenCategories'] = 1;
        $_GET['id_category_parent'] = 1;
        $_GET['token'] = Tools::getAdminTokenLite('AdminCategories');

        ob_start();
        require _PS_ROOT_DIR_ . '/admin-dev/ajax.php';
        $output = json_decode(ob_get_clean());
        $this->assertNotNull($output);
        if (count($output)) {
            $firstElem = reset($output);
            // Test some properties, not all of them
            $this->assertObjectHasAttribute('id_category', $firstElem);
            $this->assertObjectHasAttribute('name', $firstElem);
            $this->assertObjectHasAttribute('has_children', $firstElem);
        }
    }

    public function testAjaxEndpointForCategorySearch()
    {
        $_GET['searchCategory'] = 1;
        $_GET['token'] = Tools::getAdminTokenLite('AdminCategories');

        $_GET['q'] = 'Home';
        $_GET['limit'] = 10;

        ob_start();
        require _PS_ROOT_DIR_ . '/admin-dev/ajax.php';
        $output = ob_get_clean();
        $this->assertNotEmpty($output);
        // Response sample: "Home Accessories|8"
        $this->assertTrue(strpos($output, '|') !== false);
    }

    public function testAjaxEndpointForCategoryParentId()
    {
        $_GET['getParentCategoriesId'] = 1;
        $_GET['id_category'] = 2;
        $_GET['token'] = Tools::getAdminTokenLite('AdminCategories');

        ob_start();
        require _PS_ROOT_DIR_ . '/admin-dev/ajax.php';
        $output = json_decode(ob_get_clean());
        $this->assertTrue(is_array($output));
        if (count($output)) {
            $firstElem = reset($output);
            // Test some properties, not all of them
            $this->assertObjectHasAttribute('id_category', $firstElem);
        }
    }

    // Email HTML

    public function testAjaxEndpointForEmailHTML()
    {
        $_GET['getEmailHTML'] = 1;
        $_GET['email'] = Context::getContext()->shop->getBaseURI() . 'mails/en/test.html';
        $_GET['token'] = Tools::getAdminTokenLite('AdminTranslations');

        ob_start();
        require _PS_ROOT_DIR_ . '/admin-dev/ajax.php';
        $output = ob_get_clean();

        $this->assertNotEmpty($output);
        $this->assertNotSame($output, strip_tags($output));
    }

    // Notifications

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
