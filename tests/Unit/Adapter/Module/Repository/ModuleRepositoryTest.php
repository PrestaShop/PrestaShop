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

    /**
     * @var string
     */
    private $rootDir;

    public function setUp(): void
    {
        parent::setUp();

        // Built at runtime: a committed composer.lock would be scanned by Dependabot
        $this->rootDir = sys_get_temp_dir() . '/' . uniqid('ps_module_repository_', true);
        mkdir($this->rootDir);
        file_put_contents($this->rootDir . '/composer.lock', json_encode([
            'packages' => [
                ['name' => 'prestashop/blockwishlist', 'type' => 'prestashop-module'],
                ['name' => 'prestashop/ps_banner', 'type' => 'prestashop-module'],
                ['name' => 'prestashop/ps_wirepayment', 'type' => 'prestashop-module'],
                ['name' => 'symfony/symfony', 'type' => 'library'],
            ],
        ]));

        $this->moduleRepository = new ModuleRepository(
            $this->rootDir,
            _PS_ROOT_DIR_ . '/tests/Resources/modules/'
        );
    }

    public function tearDown(): void
    {
        unlink($this->rootDir . '/composer.lock');
        rmdir($this->rootDir);

        parent::tearDown();
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
        yield ['symfony', false];
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
