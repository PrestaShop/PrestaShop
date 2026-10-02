<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\Core\ExtraProperty\Schema;

use Closure;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Schema\AbstractSchemaManager;
use Doctrine\DBAL\Schema\Column;
use Doctrine\DBAL\Schema\Index;
use Doctrine\DBAL\Schema\Table;
use Doctrine\DBAL\Types\Type;
use Doctrine\DBAL\Types\Types;
use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\ExtraProperty\Definition\ExtraPropertyDefinition;
use PrestaShop\PrestaShop\Core\ExtraProperty\Definition\ExtraPropertyScope;
use PrestaShop\PrestaShop\Core\ExtraProperty\Definition\ExtraPropertySqlIndex;
use PrestaShop\PrestaShop\Core\ExtraProperty\Exception\ExtraPropertyRegistryException;
use PrestaShop\PrestaShop\Core\ExtraProperty\Schema\ExtraPropertySchemaManager;
use Psr\Log\NullLogger;

/**
 * Covers ExtraPropertySchemaManager::assertLangBaseTableShopCoherence() (via
 * ensureExtraTableAndColumn() on a missing extra lang table): the multilang_shop
 * safeguard only applies to entities whose class is an ObjectModel.
 */
class ExtraPropertySchemaManagerLangCoherenceTest extends TestCase
{
    /** @var list<string> */
    private array $createdTables = [];

    /**
     * "attribute" classifies to PHP's native \Attribute class, which exists but is not an
     * ObjectModel (the PrestaShop one is ProductAttribute): it must not be read as one.
     */
    public function testLangPropertyCanBeCreatedOnAnEntityNamedLikeANonObjectModelClass(): void
    {
        $manager = $this->buildManager(['id_attribute', 'id_lang']);

        $columnAdded = $manager->ensureExtraTableAndColumn($this->langDefinition('attribute'));

        $this->assertTrue($columnAdded);
        $this->assertSame(['ps_attribute_lang => ps_attribute_extra_lang'], $this->createdTables);
    }

    public function testLangPropertyCanBeCreatedOnAMultilangShopEntityWithAShopAwareLangTable(): void
    {
        $manager = $this->buildManager(['id_product', 'id_shop', 'id_lang']);

        $columnAdded = $manager->ensureExtraTableAndColumn($this->langDefinition('product'));

        $this->assertTrue($columnAdded);
        $this->assertSame(['ps_product_lang => ps_product_extra_lang'], $this->createdTables);
    }

    public function testLangPropertyIsRefusedOnAMultilangShopEntityWithoutAShopAwareLangTable(): void
    {
        $manager = $this->buildManager(['id_product', 'id_lang']);

        $this->expectException(ExtraPropertyRegistryException::class);
        $this->expectExceptionCode(ExtraPropertyRegistryException::SCHEMA_FAILURE);

        try {
            $manager->ensureExtraTableAndColumn($this->langDefinition('product'));
        } finally {
            $this->assertSame([], $this->createdTables);
        }
    }

    /**
     * @param list<string> $baseTablePrimaryKeyColumns Primary key of the base {entity}_lang table
     */
    private function buildManager(array $baseTablePrimaryKeyColumns): ExtraPropertySchemaManager
    {
        $baseTable = new Table(
            'ps_base_lang',
            array_map(
                static fn (string $columnName): Column => new Column($columnName, Type::getType(Types::INTEGER)),
                $baseTablePrimaryKeyColumns
            ),
            [new Index('PRIMARY', $baseTablePrimaryKeyColumns, true, true)]
        );
        $schemaManager = $this->createMock(AbstractSchemaManager::class);
        $schemaManager->method('introspectTable')->willReturn($baseTable);

        $connection = $this->createMock(Connection::class);
        $connection->method('createSchemaManager')->willReturn($schemaManager);
        $connection->method('quoteIdentifier')->willReturnCallback(
            static fn (string $identifier): string => '`' . $identifier . '`'
        );

        // The base lang table exists, the extra lang table does not: the coherence check runs,
        // then the table and column creation are recorded instead of executed.
        return new class($connection, 'ps_', new NullLogger(), fn (string $table) => $this->createdTables[] = $table) extends ExtraPropertySchemaManager {
            public function __construct(Connection $connection, string $prefix, NullLogger $logger, private readonly Closure $recordTableCreation)
            {
                parent::__construct($connection, $prefix, $logger);
            }

            protected function tableExists(string $tableName): bool
            {
                return !str_contains($tableName, '_extra');
            }

            protected function columnExists(string $tableName, string $columnName): bool
            {
                return false;
            }

            protected function createExtraTableFromBaseTable(string $baseTableName, string $extraTableName): void
            {
                ($this->recordTableCreation)($baseTableName . ' => ' . $extraTableName);
            }

            protected function syncExtraColumnIndex(string $extraTableName, string $columnName, ExtraPropertySqlIndex $sqlIndex): void
            {
            }
        };
    }

    private function langDefinition(string $entityName): ExtraPropertyDefinition
    {
        return new ExtraPropertyDefinition(
            entityName: $entityName,
            propertyName: 'test_field',
            scope: ExtraPropertyScope::LANG,
            moduleName: 'mymodule',
        );
    }
}
