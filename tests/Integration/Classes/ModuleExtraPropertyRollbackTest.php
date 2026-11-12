<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Integration\Classes;

use Module;
use PrestaShop\PrestaShop\Core\ExtraProperty\Definition\ExtraPropertyDefinition;
use PrestaShop\PrestaShop\Core\ExtraProperty\Definition\ExtraPropertyDefinitionRepositoryInterface;
use PrestaShop\PrestaShop\Core\ExtraProperty\Definition\ExtraPropertyRegistryInterface;
use PrestaShop\PrestaShop\Core\ExtraProperty\Definition\ExtraPropertyScope;
use PrestaShop\PrestaShop\Core\ExtraProperty\Definition\ExtraPropertyType;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Tests\Resources\Resetter\ExtraPropertyResetter;
use Throwable;

/**
 * The failed-install rollback must remove the extra-property definitions the module registered:
 * once the module registration is undone it can no longer be uninstalled to clean them up itself,
 * and a module-owned definition left behind stays served yet undeletable from the BO.
 */
class ModuleExtraPropertyRollbackTest extends KernelTestCase
{
    public const MODULE = 'moduleextrapropertyrollbacktest';

    private const PROPERTIES = ['rollback_field_a', 'rollback_field_b'];

    private static ExtraPropertyRegistryInterface $registry;

    private static ExtraPropertyDefinitionRepositoryInterface $repository;

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        self::bootKernel();
        // Global var read by legacy code resolving the container (SymfonyContainer::getInstance).
        global $kernel;
        $kernel = self::$kernel;

        $container = self::getContainer();
        self::$registry = $container->get(ExtraPropertyRegistryInterface::class);
        self::$repository = $container->get(ExtraPropertyDefinitionRepositoryInterface::class);
    }

    public static function tearDownAfterClass(): void
    {
        // Registering on 'product' creates a ps_product_extra storage table that has no dump; drop it
        // (and restore the definition tables) so a later test class's resetter does not try to restore it.
        ExtraPropertyResetter::resetExtraProperties();

        parent::tearDownAfterClass();
    }

    protected function tearDown(): void
    {
        foreach (self::PROPERTIES as $property) {
            try {
                self::$registry->unregister(self::definition($property), true);
            } catch (Throwable) {
                // Cleanup only - a failure here must not mask the test result.
            }
        }

        parent::tearDown();
    }

    public function testRollbackRemovesTheModuleExtraPropertyDefinitions(): void
    {
        foreach (self::PROPERTIES as $property) {
            self::$registry->register(self::definition($property));
        }
        self::assertSame(2, $this->countModuleDefinitions());

        $module = new class() extends Module {
        };
        $module->name = self::MODULE;

        self::assertTrue($module->uninstallExtraProperties());
        self::assertSame(0, $this->countModuleDefinitions());
    }

    public function testUninstallIsSkippedWhenNoContainerIsReachable(): void
    {
        // The installer runs module rollback with a context-less module: getContainer() would call
        // ContainerFinder with a null context and throw a TypeError. The method must skip, not crash.
        $module = new class() extends Module {
            public function __construct()
            {
                parent::__construct();
                $this->context = null;
            }
        };
        $module->name = self::MODULE;

        self::assertTrue($module->uninstallExtraProperties());
    }

    private function countModuleDefinitions(): int
    {
        $count = 0;
        foreach (self::$repository->getAllDefinitions() as $definition) {
            if ($definition->getModuleName() === self::MODULE) {
                ++$count;
            }
        }

        return $count;
    }

    private static function definition(string $propertyName): ExtraPropertyDefinition
    {
        return new ExtraPropertyDefinition(
            entityName: 'product',
            propertyName: $propertyName,
            type: ExtraPropertyType::STRING,
            scope: ExtraPropertyScope::COMMON,
            moduleName: self::MODULE,
        );
    }
}
