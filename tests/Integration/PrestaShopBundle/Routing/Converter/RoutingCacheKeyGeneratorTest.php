<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Integration\PrestaShopBundle\Routing\Converter;

use PrestaShop\PrestaShop\Adapter\Module\Module;
use PrestaShop\PrestaShop\Core\Context\ContextBuilderPreparer;
use PrestaShop\PrestaShop\Core\Module\ModuleRepository;
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

        // Language context must be initialized because ModuleRepository depends on it
        /** @var ContextBuilderPreparer $preparer */
        $preparer = self::$kernel->getContainer()->get(ContextBuilderPreparer::class);
        $preparer->prepareLanguageId(1);

        $this->module = self::$kernel->getContainer()->get(ModuleRepository::class)->getModule('demo');
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
