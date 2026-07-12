<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Integration\Adapter\Module;

use PrestaShop\PrestaShop\Adapter\Module\Module;
use PrestaShop\PrestaShop\Adapter\Module\ModuleDataProvider;
use PrestaShop\PrestaShop\Adapter\Module\ModuleHtmlAuthorizationChecker;
use PrestaShop\PrestaShop\Core\Module\ModuleManager;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class ModuleHtmlAuthorizationCheckerTest extends KernelTestCase
{
    public function testCustomerInputAndUnknownModulesAreNotAuthorizedButEnabledModulesAre(): void
    {
        self::bootKernel();

        $container = self::getContainer();

        /** @var Module $moduleAdapter */
        $moduleAdapter = $container->get('prestashop.adapter.legacy.module');

        /** @var ModuleManager $moduleManager */
        $moduleManager = $container->get(ModuleManager::class);

        /** @var ModuleDataProvider $moduleDataProvider */
        $moduleDataProvider = $container->get('prestashop.adapter.data_provider.module');

        $checker = new ModuleHtmlAuthorizationChecker(
            $moduleAdapter,
            $moduleManager
        );

        // Customer customization input carries no module id: never allowed (must be escaped).
        $this->assertFalse($checker->isModuleHtmlAllowed(0));
        $this->assertFalse($checker->isModuleHtmlAllowed(-1));

        // Unknown module id: not allowed.
        $this->assertFalse($checker->isModuleHtmlAllowed(999999));

        // A real, enabled module is allowed
        $moduleId = $moduleDataProvider->getModuleIdByName('ps_featuredproducts');

        $this->assertGreaterThan(
            0,
            $moduleId,
            'The ps_featuredproducts module must be installed in the test fixtures.'
        );

        $this->assertTrue($checker->isModuleHtmlAllowed($moduleId));
    }
}
