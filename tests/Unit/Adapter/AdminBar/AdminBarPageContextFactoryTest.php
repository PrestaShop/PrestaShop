<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\Adapter\AdminBar;

use Module;
use ModuleFrontController;
use ModuleFrontControllerCore;
use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Adapter\AdminBar\AdminBarPageContextFactory;
use Symfony\Contracts\Service\ServiceProviderInterface;

final class AdminBarPageContextFactoryTest extends TestCase
{
    protected function setUp(): void
    {
        if (!class_exists(ModuleFrontController::class, false)) {
            class_alias(ModuleFrontControllerCore::class, ModuleFrontController::class);
        }
    }

    public function testItNormalizesModuleOwnershipAndControllerName(): void
    {
        $resourceProviders = $this->createStub(ServiceProviderInterface::class);
        $resourceProviders->method('has')->willReturn(false);

        $module = new ModuleForAdminBarPageContextFactoryTest();
        $module->name = 'ps_test_adminbar';

        $controller = new class() extends ModuleFrontController {
            public function __construct()
            {
            }

            public function getPageName()
            {
                return 'module-ps_test_adminbar-first';
            }
        };
        $controller->module = $module;

        $pageContext = (new AdminBarPageContextFactory($resourceProviders))->create($controller);

        self::assertNotNull($pageContext);
        self::assertSame('module-ps_test_adminbar-first', $pageContext->getPageName());
        self::assertSame('ps_test_adminbar', $pageContext->getOwnerModule());
        self::assertSame('first', $pageContext->getControllerName());
    }
}

final class ModuleForAdminBarPageContextFactoryTest extends Module
{
    public function __construct()
    {
    }
}
