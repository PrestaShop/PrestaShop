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
 * needs please refer to https://devdocs.prestashop-project.org/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

declare(strict_types=1);

namespace Tests\Integration\PrestaShopBundle\Controller\Api\Improve\Design;

use Cache;
use Context;
use Db;
use Employee;
use Hook;
use Module;
use PrestaShop\PrestaShop\Adapter\Configuration;
use PrestaShop\PrestaShop\Core\Module\ModuleManager;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase as TestCase;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;

/**
 * The controller installs and uninstalls modules so it needs to clear the cache, that's why it's better isolated
 *
 * @group isolatedProcess
 */
class PositionsControllerTest extends TestCase
{
    /**
     * @var int
     */
    protected $firstModuleId;
    /**
     * @var int
     */
    protected $secondModuleId;
    /**
     * @var int
     */
    protected $hookId;
    /**
     * @var KernelBrowser
     */
    protected $client;
    /**
     * @var RouterInterface
     */
    protected $router;

    protected function setUp(): void
    {
        Cache::clear();
        Module::clearStaticCache();

        parent::setUp();
        self::bootKernel();

        // Unregister all modules hooked on displayHome
        Db::getInstance()->execute(sprintf(
            'DELETE FROM `%shook_module` WHERE `id_hook` = %d',
            _DB_PREFIX_,
            (int) Hook::getIdByName('displayHome')
        ));

        Context::getContext()->employee = new Employee(1);

        // Mock Congiguration
        $configurationMock = $this->getMockBuilder(Configuration::class)
            ->setMethods(['get'])
            ->disableOriginalConstructor()
            ->disableAutoload()
            ->getMock();
        $configurationMock->method('get')->will($this->returnValueMap([
            ['_PS_MODULE_DIR_', null, null, dirname(__DIR__, 6) . '/Resources/modules/'],
            ['_PS_ALL_THEMES_DIR_', null, null, dirname(__DIR__, 7) . '/themes/'],
        ]));

        self::$kernel->getContainer()->set('prestashop.adapter.legacy.configuration', $configurationMock);

        /** @var ModuleManager */
        $moduleManager = self::$kernel->getContainer()->get('prestashop.module.manager');
        $moduleRepository = self::$kernel->getContainer()->get('prestashop.core.admin.module.repository');
        // We use modules present in tests/resources/modules to be independent with the external API
        // We install two modules that are not present in the test db to be sure every step of the install performs correctly
        // And both modules have a common hook displayHome
        // We force the re-installation because modules may be in database but without their hook registered and this way we are sure
        // of their respective positions at each test
        foreach (['ps_banner', 'bankwire'] as $module) {
            if ($moduleManager->isInstalled($module)) {
                $moduleManager->uninstall($module);
            }
            $moduleManager->install($module);
        }

        $this->firstModuleId = $moduleRepository->getModule('ps_banner')->database->get('id');
        $this->secondModuleId = $moduleRepository->getModule('bankwire')->database->get('id');
        $this->hookId = Hook::getIdByName('displayHome');

        $this->client = self::createClient();
        $this->router = self::$container->get('router');
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        // Remove files generate during API calls
        if (file_exists(_PS_THEME_DIR_ . 'shop1.json')) {
            unlink(_PS_THEME_DIR_ . 'shop1.json');
        }
    }

    /**
     * Way = 1 means we increment the position (correspond to "bottom" of grid)
     * Module is unknown so we can't update it
     */
    public function testMoveHookPositionWithUnknownModule(): void
    {
        $this->client->request(
            'POST',
            $this->router->generate(
                'api_improve_design_positions_update'
            ),
            [
                'moduleId' => 999999,
                'hookId' => $this->hookId,
                'way' => 1,
                'positions' => [],
            ]
        );

        $response = $this->client->getResponse();
        $this->assertEquals(
            Response::HTTP_OK,
            $response->getStatusCode()
        );

        $json = json_decode($response->getContent(), true);
        $this->assertArrayHasKey('hasError', $json['data']);
        $this->assertTrue($json['data']['hasError']);
        $this->assertEquals(['This module cannot be loaded.'], $json['data']['errors']);
    }

    /**
     * Way = 1 means we increment the position (correspond to "bottom" of grid)
     * Positions are not defined, so the new position is defined only by the way parameter
     * First module can be "put down" so eevrything works fine
     */
    public function testWithoutPositionsFromFirstToSecondSuccess(): void
    {
        $this->client->request(
            'POST',
            $this->router->generate(
                'api_improve_design_positions_update'
            ),
            [
                'moduleId' => $this->firstModuleId,
                'hookId' => $this->hookId,
                'way' => 1,
                'positions' => [],
            ]
        );

        $response = $this->client->getResponse();
        $this->assertEquals(
            Response::HTTP_OK,
            $response->getStatusCode()
        );

        $json = json_decode($response->getContent(), true);
        $this->assertArrayNotHasKey('hasError', $json['data']);
        $this->assertEquals([], $json['data']);
    }

    /**
     * Way = 1 means we increment the position (correspond to "bottom" of grid)
     * Positions are not defined, so the new position is defined only by the way parameter
     * This time we try to put down the second module which is already the last one so the request fails
     */
    public function testWithoutPositionsFromLastToLastFailure(): void
    {
        $this->client->request(
            'POST',
            $this->router->generate(
                'api_improve_design_positions_update'
            ),
            [
                'moduleId' => $this->secondModuleId,
                'hookId' => $this->hookId,
                'way' => 1,
                'positions' => [],
            ]
        );

        $response = $this->client->getResponse();
        $this->assertEquals(
            Response::HTTP_OK,
            $response->getStatusCode()
        );

        $json = json_decode($response->getContent(), true);
        $this->assertArrayHasKey('hasError', $json['data']);
        $this->assertTrue($json['data']['hasError']);
        $this->assertEquals(['Cannot update module position.'], $json['data']['errors']);
    }

    /**
     * Way = 0 means we decrement the position (correspond to "top" of grid)
     * Positions are defined, so they indicate the expected order
     * This time we try to put up the second module so it is supposed to work
     */
    public function testWithPositionsFromSecondToFirstSuccess(): void
    {
        $this->client->request(
            'POST',
            $this->router->generate(
                'api_improve_design_positions_update'
            ),
            [
                'moduleId' => $this->secondModuleId,
                'hookId' => $this->hookId,
                'way' => 0,
                'positions' => [
                    sprintf('%d_%d', $this->secondModuleId, $this->hookId),
                    sprintf('%d_%d', $this->firstModuleId, $this->hookId),
                ],
            ]
        );

        $response = $this->client->getResponse();
        $this->assertEquals(
            Response::HTTP_OK,
            $response->getStatusCode()
        );

        $json = json_decode($response->getContent(), true);
        $this->assertArrayNotHasKey('hasError', $json['data']);
        $this->assertEquals([], $json['data']);
    }

    /**
     * Way = 0 means we decrement the position (correspond to "top" of grid)
     * Positions are defined, so they indicate the expected order
     * This time we try to put up the first module which is already at the top so it fails
     */
    public function testWithPositionsFromFirstToFirstFailure(): void
    {
        $this->client->request(
            'POST',
            $this->router->generate(
                'api_improve_design_positions_update'
            ),
            [
                'moduleId' => $this->firstModuleId,
                'hookId' => $this->hookId,
                'way' => 0,
                'positions' => [
                    sprintf('%d_%d', $this->firstModuleId, $this->hookId),
                    sprintf('%d_%d', $this->secondModuleId, $this->hookId),
                ],
            ]
        );

        $response = $this->client->getResponse();
        $this->assertEquals(
            Response::HTTP_OK,
            $response->getStatusCode()
        );

        $json = json_decode($response->getContent(), true);
        $this->assertArrayHasKey('hasError', $json['data']);
        $this->assertTrue($json['data']['hasError']);
        $this->assertEquals(['Cannot update module position.'], $json['data']['errors']);
    }
}
