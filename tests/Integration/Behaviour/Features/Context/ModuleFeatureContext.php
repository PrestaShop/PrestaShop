<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace Tests\Integration\Behaviour\Features\Context;

use Module;
use PHPUnit\Framework\Assert;

class ModuleFeatureContext extends AbstractPrestaShopFeatureContext
{
    /**
     * @Given the module :module is installed
     */
    public function theModuleIsInstalled(string $module): void
    {
        // Enable the module if needed
        if (!Module::isEnabled($module)) {
            Module::getInstanceByName($module)->enable();
            Module::resetStaticCache();
        }
    }

    /**
     * @Given the module with technical name :technicalName exists
     */
    public function assertModuleExists(string $technicalName): void
    {
        $moduleId = (int) Module::getModuleIdByName($technicalName);
        Assert::assertGreaterThan(0, $moduleId);
        $this->getSharedStorage()->set($technicalName, $moduleId);
    }

    /**
     * @Given the module :module is enabled
     */
    public function isTheModuleEnabled(string $module): void
    {
        Assert::assertTrue(Module::isEnabled($module));
    }

    /**
     * @Given the module :module is disabled
     */
    public function isTheModuleDisabled(string $module): void
    {
        Assert::assertFalse(Module::isEnabled($module));
    }
}
