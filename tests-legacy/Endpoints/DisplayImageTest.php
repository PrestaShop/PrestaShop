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
 * Test class for admin/ajax_products_list.php
 */
class DisplayImageTest extends AbstractEndpointAdminTest
{
    private $filename;
    private $path;
    private $content;

    protected function setUp(): void
    {
        parent::setUp();
        $this->employeeLogin();

        // Create file needed by the endpoint
        $this->content = 'This should be an image stored here, but we don\'t really care in this test';
        $this->filename = md5('wololo.png');
        $this->path = _PS_ROOT_DIR_ . '/upload/' . $this->filename;
        file_put_contents($this->path, $this->content);

    }

    protected function tearDown(): void
    {
        parent::tearDown();
        unlink($this->path);
    }

    public function testAjaxEndpoint()
    {

        $_GET['img'] = $this->filename;
        $_GET['name'] = 'Yo_doge.txt';
        $_GET['token'] = Tools::getAdminTokenLite('AdminCarts');

        ob_start();
        require _PS_ROOT_DIR_ . '/admin-dev/displayImage.php';
        $output = ob_get_clean();

        $this->assertSame($this->content, $output);
    }
}
