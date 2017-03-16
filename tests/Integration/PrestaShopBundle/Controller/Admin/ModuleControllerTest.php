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

namespace PrestaShop\PrestaShop\Tests\Integration\PrestaShopBundle\Controller\Admin;

use PrestaShop\PrestaShop\Tests\Integration\PrestaShopBundle\Test\WebTestCase;

/**
 * @group demo
 */
class ModuleControllerTest extends WebTestCase
{
    public function setUp()
    {
        parent::setUp();
        $this->enableDemoMode();
    }

    public function testModuleAction()
    {
        $moduleName = 'test-module';

        $installModuleRoute = $this->router->generate('admin_module_manage_action', array(
            'action' => 'install',
            'module_name' => $moduleName,
        ));
        $this->client->request('POST', $installModuleRoute);

        $response = $this->client->getResponse();
        $responseContent = $response->getContent();

        $decodedContent = json_decode($responseContent, true);

        $this->assertArrayHasKey($moduleName, $decodedContent);

        $this->assertArrayHasKey('status', $decodedContent[$moduleName]);
        $this->assertFalse($decodedContent[$moduleName]['status']);

        $this->assertArrayHasKey('msg', $decodedContent[$moduleName]);

        $this->assertEquals($this->getExpectedErrorMessage(), $decodedContent[$moduleName]['msg']);
    }

    public function testImportModuleAction()
    {
        $importModuleRoute = $this->router->generate('admin_module_import');
        $this->client->request('POST', $importModuleRoute);

        $response = $this->client->getResponse();
        $responseContent = $response->getContent();

        $decodedContent = json_decode($responseContent, true);

        $this->assertArrayHasKey('msg', $decodedContent);
        $this->assertEquals($this->getExpectedErrorMessage(), $decodedContent['msg']);
    }

    /**
     * @return string
     */
    protected function getExpectedErrorMessage()
    {
        return $this->translator->trans(
            'This functionality has been disabled.',
            array(),
            'Admin.Notifications.Error'
        );
    }
}
