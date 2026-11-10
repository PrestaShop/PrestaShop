<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Integration\Core\ExtraProperty;

use Doctrine\DBAL\Connection;
use Module;
use PrestaShop\PrestaShop\Adapter\SymfonyContainer;
use PrestaShop\PrestaShop\Core\ExtraProperty\Definition\ExtraPropertyDefinition;
use PrestaShop\PrestaShop\Core\ExtraProperty\Definition\ExtraPropertyDefinitionRepositoryInterface;
use PrestaShop\PrestaShop\Core\ExtraProperty\Definition\ExtraPropertyRegistryInterface;
use PrestaShop\PrestaShop\Core\ExtraProperty\Definition\ExtraPropertyScope;
use PrestaShop\PrestaShop\Core\ExtraProperty\Definition\ExtraPropertyType;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Tests\Resources\Resetter\ExtraPropertyResetter;
use Throwable;

/**
 * Module::registerExtraProperty() / unregisterExtraProperty(): the module-facing wrappers of the
 * registry. They must inject the module name, turn registry exceptions into `false` + a module
 * error (install code only tests the returned bool), and drop the column on request.
 */
class ModuleExtraPropertyRegistrationTest extends KernelTestCase
{
    private const MODULE = 'extrapropertyregtest';

    private Connection $connection;
    private ExtraPropertyDefinitionRepositoryInterface $repository;
    private ExtraPropertyRegistryInterface $registry;
    private Module $module;

    public static function tearDownAfterClass(): void
    {
        ExtraPropertyResetter::resetExtraProperties();

        parent::tearDownAfterClass();
    }

    protected function setUp(): void
    {
        parent::setUp();
        // KernelTestCase shuts the kernel down after each test: boot a fresh one and drop the
        // container cached by SymfonyContainer, which Module::get() resolves through the global.
        self::bootKernel();
        global $kernel;
        $kernel = self::$kernel;
        SymfonyContainer::resetStaticCache();

        $container = self::getContainer();
        $this->connection = $container->get('doctrine.dbal.default_connection');
        $this->repository = $container->get(ExtraPropertyDefinitionRepositoryInterface::class);
        $this->registry = $container->get(ExtraPropertyRegistryInterface::class);

        // Not installed on purpose: registration only needs the module name and the container.
        $this->module = new class() extends Module {
            public function __construct()
            {
                $this->name = 'extrapropertyregtest';
                parent::__construct();
            }
        };
    }

    protected function tearDown(): void
    {
        $rows = $this->connection->fetchAllAssociative(
            'SELECT entity_name, property_name FROM ' . _DB_PREFIX_ . 'extra_property_definition WHERE module_name = :module',
            ['module' => self::MODULE]
        );
        foreach ($rows as $row) {
            try {
                $this->registry->unregister(
                    new ExtraPropertyDefinition(entityName: $row['entity_name'], propertyName: $row['property_name'], moduleName: self::MODULE),
                    true
                );
            } catch (Throwable) {
                // Cleanup only — must not mask the test result.
            }
        }

        parent::tearDown();
        SymfonyContainer::resetStaticCache();
    }

    public function testRegisterInjectsModuleNameAndCreatesColumn(): void
    {
        $definition = new ExtraPropertyDefinition(entityName: 'product', propertyName: 'reg_note', size: 64);
        $this->assertNull($definition->getModuleName());

        $this->assertTrue($this->module->registerExtraProperty($definition));
        $this->assertSame([], $this->module->getErrors());

        $stored = $this->repository->findDefinitionByModuleAndField('product', self::MODULE, 'reg_note');
        $this->assertNotNull($stored);
        $this->assertSame(self::MODULE, $stored->getModuleName());
        $this->assertSame(
            self::MODULE,
            $this->connection->fetchOne(
                'SELECT module_name FROM ' . _DB_PREFIX_ . 'extra_property_definition WHERE entity_name = :entity AND property_name = :property',
                ['entity' => 'product', 'property' => 'reg_note']
            )
        );
        $this->assertTrue($this->columnExists('product_extra', self::MODULE . '_reg_note'));
    }

    public function testExplicitModuleNameIsKept(): void
    {
        $definition = new ExtraPropertyDefinition(entityName: 'product', propertyName: 'reg_explicit', moduleName: 'extrapropertyregtestother');

        $this->assertTrue($this->module->registerExtraProperty($definition));

        $this->assertNull($this->repository->findDefinitionByModuleAndField('product', self::MODULE, 'reg_explicit'));
        $this->assertNotNull($this->repository->findDefinitionByModuleAndField('product', 'extrapropertyregtestother', 'reg_explicit'));

        // Cleanup of the foreign module name, not covered by tearDown().
        $this->assertTrue($this->module->unregisterExtraProperty($definition, true));
    }

    public function testShopScopeOnEntityWithoutShopTableReturnsFalseWithError(): void
    {
        // Orders have no orders_shop table: a SHOP-scoped property has no base table to mirror.
        $definition = new ExtraPropertyDefinition(entityName: 'orders', propertyName: 'reg_shop_flag', type: ExtraPropertyType::BOOL, scope: ExtraPropertyScope::SHOP);

        $this->assertFalse($this->module->registerExtraProperty($definition));

        $errors = $this->module->getErrors();
        $this->assertCount(1, $errors);
        $this->assertStringContainsString(_DB_PREFIX_ . 'orders_shop', $errors[0]);
        $this->assertSame(0, $this->countModuleRows());
        $this->assertFalse($this->connection->createSchemaManager()->tablesExist([_DB_PREFIX_ . 'orders_extra_shop']));
    }

    public function testDestructiveReRegistrationReturnsFalseWithErrorAndKeepsPreviousDefinition(): void
    {
        $this->assertTrue($this->module->registerExtraProperty(
            new ExtraPropertyDefinition(entityName: 'product', propertyName: 'reg_shrink', size: 128)
        ));

        $this->assertFalse($this->module->registerExtraProperty(
            new ExtraPropertyDefinition(entityName: 'product', propertyName: 'reg_shrink', size: 32)
        ));

        $this->assertCount(1, $this->module->getErrors());
        $this->assertStringContainsString('destructive', $this->module->getErrors()[0]);
        $stored = $this->repository->findDefinitionByModuleAndField('product', self::MODULE, 'reg_shrink');
        $this->assertNotNull($stored);
        $this->assertSame(128, $stored->getSize());
    }

    public function testErrorsAccumulateAcrossFailedRegistrations(): void
    {
        $this->assertFalse($this->module->registerExtraProperty(
            new ExtraPropertyDefinition(entityName: 'orders', propertyName: 'reg_shop_a', scope: ExtraPropertyScope::SHOP)
        ));
        $this->assertFalse($this->module->registerExtraProperty(
            new ExtraPropertyDefinition(entityName: 'orders', propertyName: 'reg_shop_b', scope: ExtraPropertyScope::SHOP)
        ));

        $this->assertCount(2, $this->module->getErrors());
    }

    public function testUnregisterWithDropDataRemovesRowAndColumn(): void
    {
        $definition = new ExtraPropertyDefinition(entityName: 'product', propertyName: 'reg_drop');
        $this->assertTrue($this->module->registerExtraProperty($definition));
        $this->assertTrue($this->columnExists('product_extra', self::MODULE . '_reg_drop'));

        // Same module-less definition as the registration: the module name is injected again.
        $this->assertTrue($this->module->unregisterExtraProperty($definition, true));

        $this->assertSame([], $this->module->getErrors());
        $this->assertNull($this->repository->findDefinitionByModuleAndField('product', self::MODULE, 'reg_drop'));
        $this->assertFalse($this->columnExists('product_extra', self::MODULE . '_reg_drop'));
    }

    public function testUnregisterWithoutDropDataKeepsColumn(): void
    {
        $definition = new ExtraPropertyDefinition(entityName: 'product', propertyName: 'reg_keep');
        $this->assertTrue($this->module->registerExtraProperty($definition));

        $this->assertTrue($this->module->unregisterExtraProperty($definition));

        $this->assertNull($this->repository->findDefinitionByModuleAndField('product', self::MODULE, 'reg_keep'));
        $this->assertTrue($this->columnExists('product_extra', self::MODULE . '_reg_keep'));

        $this->connection->executeStatement(
            'ALTER TABLE ' . _DB_PREFIX_ . 'product_extra DROP COLUMN `' . self::MODULE . '_reg_keep`'
        );
    }

    /**
     * The domain check only runs once the module name is injected: it must fail like any other
     * registry error, on both wrappers.
     */
    public function testForeignLabelDomainReturnsFalseWithError(): void
    {
        $definition = new ExtraPropertyDefinition(
            entityName: 'product',
            propertyName: 'reg_foreign_domain',
            labelWording: 'Label',
            labelDomain: 'Modules.Othermodule.Admin',
        );

        $this->assertFalse($this->module->registerExtraProperty($definition));
        $this->assertFalse($this->module->unregisterExtraProperty($definition, true));

        $errors = $this->module->getErrors();
        $this->assertCount(2, $errors);
        $this->assertStringContainsString('must belong to the module', $errors[0]);
        $this->assertStringContainsString('must belong to the module', $errors[1]);
        $this->assertSame(0, $this->countModuleRows());
    }

    public function testUnregisterUnknownPropertyIsANoOp(): void
    {
        $this->assertTrue($this->module->unregisterExtraProperty(
            new ExtraPropertyDefinition(entityName: 'product', propertyName: 'reg_never_registered'),
            true
        ));
        $this->assertSame([], $this->module->getErrors());
    }

    private function columnExists(string $table, string $column): bool
    {
        $schemaManager = $this->connection->createSchemaManager();
        if (!$schemaManager->tablesExist([_DB_PREFIX_ . $table])) {
            return false;
        }

        return $schemaManager->introspectTable(_DB_PREFIX_ . $table)->hasColumn($column);
    }

    private function countModuleRows(): int
    {
        return (int) $this->connection->fetchOne(
            'SELECT COUNT(*) FROM ' . _DB_PREFIX_ . 'extra_property_definition WHERE module_name = :module',
            ['module' => self::MODULE]
        );
    }
}
