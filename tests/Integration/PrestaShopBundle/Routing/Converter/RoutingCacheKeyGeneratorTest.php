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

namespace Tests\Integration\PrestaShopBundle\Routing\Converter;

use PrestaShop\PrestaShop\Adapter\Module\Module;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Routing\Route;
use Tools;

/**
 * These tests clear the cache manually, so it's better to run it isolated.
 *
 * @group isolatedProcess
 */
class RoutingCacheKeyGeneratorTest extends KernelTestCase
{
    /**
     * @var Module
     */
    private $module;

    /**
     * {@inheritdoc}
     */
    protected function setUp(): void
    {
        self::bootKernel();

        $dirResources = dirname(__DIR__, 4);
        if (is_dir($dirResources . '/Resources/modules_tests/demo')) {
            Tools::recurseCopy($dirResources . '/Resources/modules_tests/demo', _PS_MODULE_DIR_ . '/demo');
        }

        $this->module = self::$kernel->getContainer()->get('prestashop.core.admin.module.repository')->getModule('demo');
        $this->module->onInstall();
        self::$kernel->getContainer()->get('prestashop.core.cache.clearer.cache_clearer_chain')->clear();
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown(): void
    {
        $dirResources = dirname(__DIR__, 4);
        if (is_dir($dirResources . '/Resources/modules_tests/demo')) {
            Tools::deleteDirectory($dirResources . '/Resources/modules_tests/demo');
        }
        $this->module->onUninstall();
        parent::tearDown();
    }

    public function testRoutesAreRegistered(): void
    {
        $router = self::$kernel->getContainer()->get('router');
        $route = $router->getRouteCollection()->get('demo_admin_demo');

        $this->assertInstanceOf(Route::class, $route);

        $this->assertEquals('/modules/demo/demo', $route->getPath());
        $this->assertEquals([
            '_controller' => 'PsTest\Controller\Admin\DemoController::demoAction',
        ], $route->getDefaults());
    }
}
