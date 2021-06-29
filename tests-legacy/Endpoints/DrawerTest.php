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

use Tools;

/**
 * Test class for admin/drawer.php
 */
class DrawerTest extends AbstractEndpointAdminTest
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->employeeLogin();
    }

    public function testEndpoint()
    {
        $_GET['type'] = 'pie';
        $_GET['option'] = 'currency';
        $_GET['layers'] = '1';
        $_GET['width'] = '100%';
        $_GET['height'] = '270';
        $_GET['render'] = 'graphnvd3';
        $_GET['module'] = 'statspersonalinfos';
        $_GET['id_employee'] = '1';
        $_GET['id_lang'] = '1';
        $_GET['token'] = Tools::getAdminTokenLite('AdminStats');

        ob_start();
        require _PS_ROOT_DIR_ . '/admin-dev/drawer.php';
        $output = json_decode(ob_get_clean());

        $this->assertNotNull($output);

    }
}
