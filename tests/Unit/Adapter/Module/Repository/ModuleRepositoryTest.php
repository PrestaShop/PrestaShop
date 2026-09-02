<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\Adapter\Module\Repository;

use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Adapter\Module\Repository\ModuleRepository;

class ModuleRepositoryTest extends TestCase
{
    /**
     * @var ModuleRepository
     */
    private $moduleRepository;

    public function setUp(): void
    {
        parent::setUp();

        $this->moduleRepository = new ModuleRepository(
            _PS_ROOT_DIR_ . '/tests/Unit/Resources/composerLock/',
            _PS_ROOT_DIR_ . '/tests/Resources/modules/'
        );
    }

    public function testNativeModulesMandatoryModules(): void
    {
        $modules = $this->moduleRepository->getNativeModules();
        foreach (ModuleRepository::ADDITIONAL_ALLOWED_MODULES as $mandatoryModule) {
            self::assertContains($mandatoryModule, $modules);
        }
    }

    /**
     * @dataProvider dataProviderNativeModules
     *
     * @param string $moduleName
     * @param bool $isNative
     */
    public function testNativeModulesCheckModules(string $moduleName, bool $isNative): void
    {
        if ($isNative) {
            self::assertContains($moduleName, $this->moduleRepository->getNativeModules());
        } else {
            self::assertNotContains($moduleName, $this->moduleRepository->getNativeModules());
        }
    }

    /**
     * @dataProvider dataProviderOnlyNativeModules
     *
     * @param string $moduleName
     */
    public function testNonNativeModulesCheckModules(string $moduleName): void
    {
        self::assertNotContains($moduleName, $this->moduleRepository->getNonNativeModules());
    }

    public function dataProviderNativeModules(): iterable
    {
        // Native modules
        yield ['blockwishlist', true];
        yield ['ps_banner', true];
        yield ['ps_wirepayment', true];
        // Non native modules
        yield ['ps_checkout', false];
        yield ['azerty', false];
        yield ['', false];
    }

    public function dataProviderOnlyNativeModules(): iterable
    {
        yield ['blockwishlist'];
        yield ['ps_banner'];
        yield ['ps_wirepayment'];
    }
}
