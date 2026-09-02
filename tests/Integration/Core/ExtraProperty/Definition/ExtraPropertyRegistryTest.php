<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Integration\Core\ExtraProperty\Definition;

use Doctrine\DBAL\Connection;
use PrestaShop\PrestaShop\Core\ExtraProperty\Definition\ExtraPropertyDefinition;
use PrestaShop\PrestaShop\Core\ExtraProperty\Definition\ExtraPropertyDefinitionRepositoryInterface;
use PrestaShop\PrestaShop\Core\ExtraProperty\Definition\ExtraPropertyRegistryInterface;
use PrestaShop\PrestaShop\Core\ExtraProperty\Definition\ExtraPropertyScope;
use PrestaShop\PrestaShop\Core\ExtraProperty\Definition\ExtraPropertyType;
use PrestaShop\PrestaShop\Core\ExtraProperty\Exception\ExtraPropertyRegistryException;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Tests\Resources\DatabaseDump;
use Throwable;

/**
 * Integration test for ExtraPropertyRegistry against a real database: the atomicity
 * contract of register()/unregister().
 *
 * The key regression pinned here: registering a property on an entity whose base table
 * does not exist must throw AND persist nothing — no orphan row in
 * extra_property_definition (a previous version saved the row before running the DDL,
 * leaving behind a definition that could never work).
 */
class ExtraPropertyRegistryTest extends KernelTestCase
{
    private const MODULE = 'extrapropertyregistrytest';
    private const ENTITY = 'product';

    private static ExtraPropertyRegistryInterface $registry;
    private static ExtraPropertyDefinitionRepositoryInterface $repository;
    private static Connection $connection;

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
        self::$connection = $container->get('doctrine.dbal.default_connection');
    }

    public static function tearDownAfterClass(): void
    {
        DatabaseDump::restoreTables(['extra_property_definition']);
        DatabaseDump::removeExtraTables();

        parent::tearDownAfterClass();
    }

    protected function tearDown(): void
    {
        // Best-effort cleanup of everything this test registered (rows + columns), so
        // each test starts from a blank slate whatever the previous one did.
        $rows = self::$connection->fetchAllAssociative(
            'SELECT entity_name, property_name FROM ' . _DB_PREFIX_ . 'extra_property_definition WHERE module_name = :module',
            ['module' => self::MODULE]
        );
        foreach ($rows as $row) {
            try {
                self::$registry->unregister(self::definition(entityName: $row['entity_name'], propertyName: $row['property_name']), true);
            } catch (Throwable) {
                // Cleanup only — a failure here must not mask the test result.
            }
        }

        parent::tearDown();
    }

    public function testRegisteringOnAnUnknownEntityThrowsAndPersistsNothing(): void
    {
        $definition = self::definition(entityName: 'nonexistent_entity', propertyName: 'ghost_field');

        try {
            self::$registry->register($definition);
            $this->fail('An ExtraPropertyRegistryException should have been thrown.');
        } catch (ExtraPropertyRegistryException $exception) {
            // Expected: the base table "nonexistent_entity" does not exist.
            $this->assertSame(ExtraPropertyRegistryException::BASE_TABLE_NOT_FOUND, $exception->getCode());
        }

        // The whole point of the DDL-first ordering: nothing was persisted.
        $this->assertNull(
            self::$repository->findDefinitionByModuleAndField('nonexistent_entity', self::MODULE, 'ghost_field'),
            'a failed registration must not leave an orphan definition row'
        );
        $this->assertSame(0, $this->countDefinitionRows('nonexistent_entity'));
        $this->assertFalse(
            self::$connection->createSchemaManager()->tablesExist([_DB_PREFIX_ . 'nonexistent_entity_extra']),
            'no extra table may be created for a nonexistent entity'
        );
    }

    public function testSuccessfulRegistrationCreatesRowTableAndColumn(): void
    {
        $definition = self::definition(propertyName: 'happy_field');

        $id = self::$registry->register($definition);

        $this->assertGreaterThan(0, $id);
        $stored = self::$repository->findDefinitionByModuleAndField(self::ENTITY, self::MODULE, 'happy_field');
        $this->assertNotNull($stored);

        $schemaManager = self::$connection->createSchemaManager();
        $extraTable = _DB_PREFIX_ . self::ENTITY . '_extra';
        $this->assertTrue($schemaManager->tablesExist([$extraTable]));
        $this->assertTrue($schemaManager->introspectTable($extraTable)->hasColumn($definition->getStorageColumnName()));
    }

    public function testDestructiveUpdateThrowsAndLeavesThePreviousDefinitionIntact(): void
    {
        self::$registry->register(self::definition(propertyName: 'shrink_field', size: 128));

        try {
            self::$registry->register(self::definition(propertyName: 'shrink_field', size: 32));
            $this->fail('An ExtraPropertyRegistryException should have been thrown.');
        } catch (ExtraPropertyRegistryException $exception) {
            // Expected: a STRING size decrease risks truncating stored data.
            $this->assertSame(ExtraPropertyRegistryException::DESTRUCTIVE_SCHEMA_CHANGE, $exception->getCode());
        }

        $stored = self::$repository->findDefinitionByModuleAndField(self::ENTITY, self::MODULE, 'shrink_field');
        $this->assertNotNull($stored);
        $this->assertSame(128, $stored->getSize(), 'the stored definition must keep its previous size');

        $column = self::$connection->createSchemaManager()
            ->introspectTable(_DB_PREFIX_ . self::ENTITY . '_extra')
            ->getColumn($stored->getStorageColumnName());
        $this->assertSame(128, $column->getLength(), 'the live column must keep its previous size');
    }

    public function testScopeConflictThrowsAndLeavesTheOriginalDefinitionIntact(): void
    {
        self::$registry->register(self::definition(propertyName: 'conflict_field', scope: ExtraPropertyScope::COMMON));

        try {
            self::$registry->register(self::definition(propertyName: 'conflict_field', scope: ExtraPropertyScope::LANG));
            $this->fail('An ExtraPropertyRegistryException should have been thrown.');
        } catch (ExtraPropertyRegistryException $exception) {
            // Expected: same (entity, module, property) cannot live under two scopes.
            $this->assertSame(ExtraPropertyRegistryException::SCOPE_CONFLICT, $exception->getCode());
        }

        $stored = self::$repository->findDefinitionByModuleAndField(self::ENTITY, self::MODULE, 'conflict_field');
        $this->assertNotNull($stored);
        $this->assertSame(ExtraPropertyScope::COMMON, $stored->getScope());
        $this->assertFalse(
            self::$connection->createSchemaManager()->tablesExist([_DB_PREFIX_ . self::ENTITY . '_extra_lang']),
            'the refused LANG registration must not have created the lang extra table'
        );
    }

    public function testUnregisterWithDropColumnRemovesRowAndColumn(): void
    {
        $definition = self::definition(propertyName: 'drop_field');
        self::$registry->register($definition);

        self::$registry->unregister($definition, true);

        $this->assertNull(self::$repository->findDefinitionByModuleAndField(self::ENTITY, self::MODULE, 'drop_field'));
        $schemaManager = self::$connection->createSchemaManager();
        $extraTable = _DB_PREFIX_ . self::ENTITY . '_extra';
        if ($schemaManager->tablesExist([$extraTable])) {
            // The table may survive when other definitions still use it — the column must not.
            $this->assertFalse($schemaManager->introspectTable($extraTable)->hasColumn($definition->getStorageColumnName()));
        }
    }

    public function testUnregisterWithoutDropColumnKeepsTheColumnAndReRegisteringHeals(): void
    {
        $definition = self::definition(propertyName: 'keep_field');
        self::$registry->register($definition);

        self::$registry->unregister($definition, false);

        $this->assertNull(self::$repository->findDefinitionByModuleAndField(self::ENTITY, self::MODULE, 'keep_field'));
        $extraTable = _DB_PREFIX_ . self::ENTITY . '_extra';
        $this->assertTrue(
            self::$connection->createSchemaManager()->introspectTable($extraTable)->hasColumn($definition->getStorageColumnName()),
            'without dropColumn the storage column must survive the unregistration'
        );

        // Re-registering adopts the leftover column (create-vs-sync branching) — the
        // documented recovery path for an unreferenced column.
        $id = self::$registry->register($definition);
        $this->assertGreaterThan(0, $id);
        $this->assertNotNull(self::$repository->findDefinitionByModuleAndField(self::ENTITY, self::MODULE, 'keep_field'));
    }

    private function countDefinitionRows(string $entityName): int
    {
        return (int) self::$connection->fetchOne(
            'SELECT COUNT(*) FROM ' . _DB_PREFIX_ . 'extra_property_definition WHERE entity_name = :entity AND module_name = :module',
            ['entity' => $entityName, 'module' => self::MODULE]
        );
    }

    private static function definition(
        string $entityName = self::ENTITY,
        string $propertyName = 'test_field',
        ExtraPropertyScope $scope = ExtraPropertyScope::COMMON,
        ?int $size = null,
    ): ExtraPropertyDefinition {
        return new ExtraPropertyDefinition(
            entityName: $entityName,
            propertyName: $propertyName,
            type: ExtraPropertyType::STRING,
            scope: $scope,
            moduleName: self::MODULE,
            size: $size,
        );
    }
}
