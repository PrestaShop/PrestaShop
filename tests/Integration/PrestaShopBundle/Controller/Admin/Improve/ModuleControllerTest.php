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

declare(strict_types=1);

namespace Tests\Integration\PrestaShopBundle\Controller\Admin\Improve;

use Context;
use PrestaShop\PrestaShop\Adapter\LegacyContext;
use PrestaShop\PrestaShop\Core\Module\ModuleCollection;
use PrestaShop\PrestaShop\Core\Module\ModuleRepository;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Routing\Router;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Translation\Translator;

class ModuleControllerTest extends WebTestCase
{
    /**
     * @var KernelBrowser
     */
    protected $client;
    /**
     * @var Router
     */
    protected $router;
    /**
     * @var Translator
     */
    protected $translator;
    /**
     * @var LegacyContext
     */
    protected $context;

    protected function setUp(): void
    {
        parent::setUp();

        $this->client = self::createClient();
        $this->router = self::$kernel->getContainer()->get('router');
        $this->context = self::$kernel->getContainer()->get('prestashop.adapter.legacy.context');

        Context::setInstanceForTesting($this->context->getContext());

        // Enable debug mode
        $configurationMock = $this->getMockBuilder('\PrestaShop\PrestaShop\Adapter\Configuration')
            ->setMethods(['get'])
            ->disableOriginalConstructor()
            ->disableAutoload()
            ->getMock();
        $configurationMock->method('get')
            ->will($this->returnValueMap([
                ['_PS_MODE_DEMO_', null, null, true],
                ['_PS_ROOT_DIR_', null, null, _PS_ROOT_DIR_],
                ['_PS_MODULE_DIR_', null, null, _PS_ROOT_DIR_ . '/tests/Resources/modules/'],
                ['_PS_ALL_THEMES_DIR_', null, null, dirname(__DIR__, 6) . '/themes/'],
            ]));

        self::$kernel->getContainer()->set('prestashop.adapter.legacy.configuration', $configurationMock);

        $moduleRepository = $this->createMock(ModuleRepository::class);
        $moduleRepository->method('getList')->willReturn(new ModuleCollection());
        self::$kernel->getContainer()->set('prestashop.core.admin.module.repository', $moduleRepository);
    }

    public function testModuleAction(): void
    {
        $moduleName = 'test-module';

        $installModuleRoute = $this->router->generate('admin_module_manage_action', [
            'action' => 'install',
            'module_name' => $moduleName,
        ]);
        $this->client->request('POST', $installModuleRoute);

        $response = $this->client->getResponse();
        $responseContent = $response->getContent();

        $decodedContent = json_decode($responseContent, true);

        $this->assertArrayHasKey($moduleName, $decodedContent);

        $this->assertArrayHasKey('status', $decodedContent[$moduleName]);
        $this->assertFalse($decodedContent[$moduleName]['status']);

        $this->assertArrayHasKey('msg', $decodedContent[$moduleName]);

        $this->assertEquals('This functionality has been disabled.', $decodedContent[$moduleName]['msg']);
    }

    public function testImportModuleAction(): void
    {
        $importModuleRoute = $this->router->generate('admin_module_import');
        $this->client->request('POST', $importModuleRoute);

        $response = $this->client->getResponse();
        $responseContent = $response->getContent();

        $decodedContent = json_decode($responseContent, true);

        $this->assertArrayHasKey('msg', $decodedContent);
        $this->assertEquals('This functionality has been disabled.', $decodedContent['msg']);
    }
}
