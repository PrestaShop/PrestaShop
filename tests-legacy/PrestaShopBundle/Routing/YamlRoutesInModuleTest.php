<?php
/**
 * 2007-2019 PrestaShop and Contributors
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
 * needs please refer to https://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2019 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace LegacyTests\PrestaShopBundle\Routing;

use LegacyTests\TestCase\Module as HelperModule;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\Routing\Route;

class YamlRoutesInModuleTest extends KernelTestCase
{
    /**
     * @var Container
     */
    private $container;

    /**
     * @var Module
     */
    private $module;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $kernel = self::createKernel();
        $kernel->boot();

        $this->container = $kernel->getContainer();

        HelperModule::addModule('demo');

        $this->module = $this->container->get('prestashop.core.admin.module.repository')->getModule('demo');
        $this->module->onInstall();
        $this->container->get('prestashop.core.cache.clearer.cache_clearer_chain')->clear();
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown()
    {
        HelperModule::removeModule('demo');
        $this->module->onUninstall();
    }

    public function testRoutesAreRegistered()
    {
        $router = $this->container->get('router');
        $route = $router->getRouteCollection()->get('demo_admin_demo');

        $this->assertInstanceOf(Route::class, $route);

        $this->assertEquals('/modules/demo/demo', $route->getPath());
        $this->assertEquals([
            '_controller' => 'PsTest\Controller\Admin\DemoController::demoAction',
        ], $route->getDefaults());
    }
}
