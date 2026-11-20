<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Integration\Core\ExtraProperty\Value;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopCollection;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopConstraint;
use PrestaShop\PrestaShop\Core\ExtraProperty\Definition\ExtraPropertyDefinition;
use PrestaShop\PrestaShop\Core\ExtraProperty\Definition\ExtraPropertyDefinitionCollection;
use PrestaShop\PrestaShop\Core\ExtraProperty\Definition\ExtraPropertyDefinitionRepositoryInterface;
use PrestaShop\PrestaShop\Core\ExtraProperty\Definition\ExtraPropertyDefinitionShopFilterInterface;
use PrestaShop\PrestaShop\Core\ExtraProperty\Definition\ExtraPropertyScope;
use PrestaShop\PrestaShop\Core\ExtraProperty\Definition\ExtraPropertyType;
use PrestaShop\PrestaShop\Core\ExtraProperty\Value\ExtraPropertyWriter;
use PrestaShop\PrestaShop\Core\Shop\ShopListResolverInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

/**
 * Exercises public writeAll()/toggle operations against real UNIQUE indexes and InnoDB
 * transactions. Temporary tables isolate these tests from installed extra properties.
 */
class ExtraPropertyWriterTest extends KernelTestCase
{
    private Connection $connection;
    private ExtraPropertyWriter $writer;
    private string $table;
    private array $createdTables = [];

    protected function setUp(): void
    {
        parent::setUp();
        self::bootKernel();
        $this->connection = self::getContainer()->get('doctrine.dbal.default_connection');
    }

    protected function tearDown(): void
    {
        if (isset($this->connection)) {
            if ($this->connection->isTransactionActive()) {
                $this->connection->rollBack();
            }
            foreach ($this->createdTables as $table) {
                $this->connection->executeStatement('DROP TEMPORARY TABLE IF EXISTS ' . $table);
            }
        }
        parent::tearDown();
    }

    /**
     * @dataProvider identityProvider
     */
    public function testUniqueValueCannotOverwriteAnotherEntity(array $systemColumns, array $dimensions): void
    {
        $this->createTable($systemColumns);
        $this->write($systemColumns, [[4, ...$dimensions, 'SAME-VALUE', 'Note of customer 4']]);

        try {
            $this->write($systemColumns, [[14, ...$dimensions, 'SAME-VALUE', null]]);
            $this->fail('A UNIQUE value belonging to another entity must be rejected.');
        } catch (UniqueConstraintViolationException) {
            $this->assertSame('Note of customer 4', $this->connection->fetchOne('SELECT note FROM ' . $this->table . ' WHERE id_customer = 4'));
            $this->assertFalse($this->connection->fetchOne('SELECT 1 FROM ' . $this->table . ' WHERE id_customer = 14'));
        }
    }

    public static function identityProvider(): array
    {
        return [
            'COMMON' => [[], []],
            'LANG' => [['id_lang'], [1]],
            'SHOP' => [['id_shop'], [1]],
            'LANG multishop' => [['id_shop', 'id_lang'], [1, 1]],
        ];
    }

    public function testUnchangedValuesAndPartialUpdatesPreserveOtherColumns(): void
    {
        $this->createTable([]);
        $this->write([], [[4, 'SAME', 'keep']]);
        $this->write([], [[4, 'SAME', 'keep']]);
        $this->write([], [[4, 'CHANGED']], ['unique_value']);

        $this->assertSame(['unique_value' => 'CHANGED', 'note' => 'keep'], $this->connection->fetchAssociative('SELECT unique_value, note FROM ' . $this->table));
    }

    /**
     * @dataProvider batchProvider
     */
    public function testBatchFailureRollsBackEarlierRows(array $systemColumns, array $first, array $second, bool $outerTransaction): void
    {
        $this->createTable($systemColumns);
        $this->write($systemColumns, [[4, ...$first, 'FIRST', 'original']]);
        $this->write($systemColumns, [[4, ...$second, 'SECOND', 'keep']]);
        if ($outerTransaction) {
            $this->connection->beginTransaction();
            $this->connection->executeStatement('UPDATE ' . $this->table . ' SET note = ? WHERE unique_value = ?', ['outer change', 'SECOND']);
        }

        try {
            $this->write($systemColumns, [[4, ...$first, 'UPDATED', 'changed'], [4, ...$second, 'UPDATED', 'changed']]);
            $this->fail('The second row must fail.');
        } catch (UniqueConstraintViolationException) {
            $this->assertSame('original', $this->connection->fetchOne('SELECT note FROM ' . $this->table . ' WHERE unique_value = ?', ['FIRST']));
            $this->assertFalse($this->connection->fetchOne('SELECT 1 FROM ' . $this->table . ' WHERE unique_value = ?', ['UPDATED']));
            $this->assertSame($outerTransaction, $this->connection->isTransactionActive());
            $this->assertSame($outerTransaction ? 'outer change' : 'keep', $this->connection->fetchOne('SELECT note FROM ' . $this->table . ' WHERE unique_value = ?', ['SECOND']));
        }
    }

    public static function batchProvider(): array
    {
        return [
            'LANG' => [['id_lang'], [1], [2], false],
            'SHOP' => [['id_shop'], [1], [2], false],
            'LANG multishop' => [['id_shop', 'id_lang'], [1, 1], [1, 2], false],
            'LANG outer transaction' => [['id_lang'], [1], [2], true],
            'SHOP outer transaction' => [['id_shop'], [1], [2], true],
        ];
    }

    public function testSuccessfulBatchDoesNotCommitOuterTransaction(): void
    {
        $this->createTable(['id_shop']);
        $this->connection->beginTransaction();
        $this->write(['id_shop'], [[4, 1, null, 'one'], [4, 2, null, 'one']]);
        $this->assertTrue($this->connection->isTransactionActive());
        $this->connection->rollBack();

        $this->assertSame(0, (int) $this->connection->fetchOne('SELECT COUNT(*) FROM ' . $this->table));
    }

    public function testUpdateUsesEveryDimensionOfTheIdentity(): void
    {
        $this->createTable(['id_shop', 'id_lang']);
        foreach ([[4, 1, 1, 'ONE', 'one'], [4, 1, 2, 'TWO', 'two'], [4, 2, 1, 'THREE', 'three']] as $row) {
            $this->write(['id_shop', 'id_lang'], [$row]);
        }
        $this->write(['id_shop', 'id_lang'], [[4, 1, 1, 'CHANGED', null]]);

        $this->assertSame([null, 'two', 'three'], $this->connection->fetchFirstColumn('SELECT note FROM ' . $this->table . ' ORDER BY id_shop, id_lang'));
    }

    public function testUniqueConflictOnExistingIdentityLeavesBothRowsUntouched(): void
    {
        $this->createTable([]);
        $this->write([], [[4, 'TAKEN', 'four']]);
        $this->write([], [[14, 'OTHER', 'fourteen']]);
        try {
            $this->write([], [[14, 'TAKEN', null]]);
            $this->fail('Updating an existing row must enforce UNIQUE too.');
        } catch (UniqueConstraintViolationException) {
            $this->assertSame(['four', 'fourteen'], $this->connection->fetchFirstColumn('SELECT note FROM ' . $this->table . ' ORDER BY id_customer'));
        }
    }

    /**
     * @dataProvider outerTransactionProvider
     */
    public function testWriteAllRollsBackEarlierScopes(bool $outerTransaction): void
    {
        $this->createTable([]);
        $commonTable = $this->table;
        $this->write([], [[14, 'OWN', 'original']]);
        $this->createTable(['id_shop']);
        $this->write(['id_shop'], [[4, 1, 'TAKEN', 'keep']]);
        if ($outerTransaction) {
            $this->connection->beginTransaction();
            $this->connection->executeStatement('UPDATE ' . $this->table . ' SET note = ? WHERE id_customer = 4', ['outer change']);
        }
        $this->configureWriter([$this->definition('note', []), $this->definition('unique_value', ['id_shop'])], [1]);

        try {
            $this->writer->writeAll('issue43296_writer', 'id_customer', 14, [
                ExtraPropertyDefinition::CORE_MODULE_KEY => ['note' => 'changed', 'unique_value' => 'TAKEN'],
            ], ShopConstraint::shop(1));
            $this->fail('A SHOP failure must roll back the earlier COMMON update.');
        } catch (UniqueConstraintViolationException) {
            $this->assertSame('original', $this->connection->fetchOne('SELECT note FROM ' . $commonTable . ' WHERE id_customer = 14'));
            $this->assertSame($outerTransaction ? 'outer change' : 'keep', $this->connection->fetchOne('SELECT note FROM ' . $this->table . ' WHERE id_customer = 4'));
            $this->assertSame($outerTransaction, $this->connection->isTransactionActive());
        }
    }

    public static function outerTransactionProvider(): array
    {
        return ['owned transaction' => [false], 'outer transaction' => [true]];
    }

    public function testToggleUniformizesShopsWithoutCommittingOuterTransaction(): void
    {
        $this->createTable(['id_shop']);
        $this->connection->executeStatement('ALTER TABLE ' . $this->table . ' ADD flag TINYINT NULL');
        $this->connection->executeStatement('INSERT INTO ' . $this->table . ' (id_customer, id_shop, flag) VALUES (4, 1, 1), (4, 2, 0)');
        $definition = $this->definition('flag', ['id_shop'], ExtraPropertyType::BOOL);
        $this->configureWriter([$definition], [1, 2]);
        $this->connection->beginTransaction();

        $this->writer->toggleExtraProperty($definition, 4, ShopCollection::shops([1, 2]));

        $this->assertSame([0, 0], array_map('intval', $this->connection->fetchFirstColumn('SELECT flag FROM ' . $this->table . ' ORDER BY id_shop')));
        $this->assertTrue($this->connection->isTransactionActive());
        $this->connection->rollBack();
        $this->assertSame([1, 0], array_map('intval', $this->connection->fetchFirstColumn('SELECT flag FROM ' . $this->table . ' ORDER BY id_shop')));
    }

    private function createTable(array $systemColumns): void
    {
        $this->table = $this->connection->quoteIdentifier(_DB_PREFIX_ . ExtraPropertyDefinition::buildExtraTableName('issue43296_writer', $this->scope($systemColumns)));
        $dimensions = '';
        foreach ($systemColumns as $column) {
            $dimensions .= ', ' . $this->connection->quoteIdentifier($column) . ' INT NOT NULL';
        }
        $keys = implode(', ', array_map(fn (string $column): string => $this->connection->quoteIdentifier($column), ['id_customer', ...$systemColumns]));
        $this->connection->executeStatement('CREATE TEMPORARY TABLE ' . $this->table . ' (id_customer INT NOT NULL' . $dimensions . ', unique_value VARCHAR(255) NULL, note VARCHAR(255) NULL, PRIMARY KEY (' . $keys . '), UNIQUE KEY unique_value (unique_value)) ENGINE=InnoDB');
        $this->createdTables[] = $this->table;
    }

    private function write(array $systemColumns, array $rows, array $dataColumns = ['unique_value', 'note']): void
    {
        $definitions = array_map(fn (string $column): ExtraPropertyDefinition => $this->definition($column, $systemColumns), $dataColumns);
        $shopIndex = array_search('id_shop', $systemColumns, true);
        $langIndex = array_search('id_lang', $systemColumns, true);
        $shopIds = false === $shopIndex ? [1] : array_values(array_unique(array_column($rows, $shopIndex + 1)));
        $this->configureWriter($definitions, $shopIds);
        $values = [];
        foreach ($rows as $row) {
            $this->assertSame($rows[0][0], $row[0]);
            foreach ($dataColumns as $index => $column) {
                $value = $row[1 + count($systemColumns) + $index];
                if (false === $langIndex) {
                    $values[$column] = $value;
                } else {
                    $values[$column][$row[1 + $langIndex]] = $value;
                }
            }
        }
        $this->writer->writeAll('issue43296_writer', 'id_customer', $rows[0][0], [ExtraPropertyDefinition::CORE_MODULE_KEY => $values], ShopCollection::shops($shopIds));
    }

    private function scope(array $systemColumns): ExtraPropertyScope
    {
        if (in_array('id_lang', $systemColumns, true)) {
            return ExtraPropertyScope::LANG;
        }

        return [] === $systemColumns ? ExtraPropertyScope::COMMON : ExtraPropertyScope::SHOP;
    }

    private function definition(string $column, array $systemColumns, ExtraPropertyType $type = ExtraPropertyType::STRING): ExtraPropertyDefinition
    {
        return new ExtraPropertyDefinition(
            entityName: 'customer',
            propertyName: $column,
            type: $type,
            scope: $this->scope($systemColumns),
            multiShop: in_array('id_shop', $systemColumns, true),
            tableName: 'issue43296_writer',
            primaryKeyName: 'id_customer'
        );
    }

    private function configureWriter(array $definitions, array $shopIds): void
    {
        $repository = $this->createStub(ExtraPropertyDefinitionRepositoryInterface::class);
        $repository->method('getAllDefinitions')->willReturn(new ExtraPropertyDefinitionCollection($definitions));
        $resolver = $this->createStub(ShopListResolverInterface::class);
        $resolver->method('resolveShopIds')->willReturn($shopIds);
        $resolver->method('resolveRepresentativeShopId')->willReturn($shopIds[0]);
        $filter = $this->createStub(ExtraPropertyDefinitionShopFilterInterface::class);
        $filter->method('filterByShopIds')->willReturnArgument(0);
        $filter->method('getAvailableShopIds')->willReturnCallback(static fn (ExtraPropertyDefinition $definition, array $ids): array => $ids);
        $this->writer = new ExtraPropertyWriter($this->connection, _DB_PREFIX_, $repository, $resolver, $filter);
    }
}
