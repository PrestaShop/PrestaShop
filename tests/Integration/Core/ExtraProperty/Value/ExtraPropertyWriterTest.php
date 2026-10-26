<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Integration\Core\ExtraProperty\Value;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use PrestaShop\PrestaShop\Core\ExtraProperty\Definition\ExtraPropertyDefinitionRepositoryInterface;
use PrestaShop\PrestaShop\Core\ExtraProperty\Definition\ExtraPropertyDefinitionShopFilterInterface;
use PrestaShop\PrestaShop\Core\ExtraProperty\Value\ExtraPropertyWriter;
use PrestaShop\PrestaShop\Core\Shop\ShopListResolverInterface;
use ReflectionMethod;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

/**
 * Exercises the writer's storage batches against real UNIQUE indexes and InnoDB
 * transactions. Temporary tables isolate these tests from installed extra properties.
 * Scope routing and toggle targets are covered by the writer's unit tests.
 */
class ExtraPropertyWriterTest extends KernelTestCase
{
    private Connection $connection;
    private ExtraPropertyWriter $writer;
    private string $table;

    protected function setUp(): void
    {
        parent::setUp();
        self::bootKernel();
        $this->connection = self::getContainer()->get('doctrine.dbal.default_connection');
        $this->table = $this->connection->quoteIdentifier(_DB_PREFIX_ . 'issue43296_writer');
        $this->writer = new ExtraPropertyWriter(
            $this->connection,
            _DB_PREFIX_,
            $this->createStub(ExtraPropertyDefinitionRepositoryInterface::class),
            $this->createStub(ShopListResolverInterface::class),
            $this->createStub(ExtraPropertyDefinitionShopFilterInterface::class)
        );
    }

    protected function tearDown(): void
    {
        if (isset($this->connection)) {
            if ($this->connection->isTransactionActive()) {
                $this->connection->rollBack();
            }
            $this->connection->executeStatement('DROP TEMPORARY TABLE IF EXISTS ' . $this->table);
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
        $this->write($systemColumns, [[4, ...$first, 'FIRST', 'original'], [4, ...$second, 'TAKEN', 'keep']]);
        if ($outerTransaction) {
            $this->connection->beginTransaction();
            $this->connection->executeStatement('UPDATE ' . $this->table . ' SET note = ? WHERE unique_value = ?', ['outer change', 'TAKEN']);
        }

        try {
            $this->write($systemColumns, [[4, ...$first, 'UPDATED', 'changed'], [14, ...$second, 'TAKEN', null]]);
            $this->fail('The second row must fail.');
        } catch (UniqueConstraintViolationException) {
            $this->assertSame('original', $this->connection->fetchOne('SELECT note FROM ' . $this->table . ' WHERE unique_value = ?', ['FIRST']));
            $this->assertFalse($this->connection->fetchOne('SELECT 1 FROM ' . $this->table . ' WHERE unique_value = ?', ['UPDATED']));
            $this->assertSame($outerTransaction, $this->connection->isTransactionActive());
            $this->assertSame($outerTransaction ? 'outer change' : 'keep', $this->connection->fetchOne('SELECT note FROM ' . $this->table . ' WHERE unique_value = ?', ['TAKEN']));
        }
    }

    public static function batchProvider(): array
    {
        return [
            'LANG' => [['id_lang'], [1], [2], false],
            'SHOP' => [['id_shop'], [1], [2], false],
            'LANG multishop' => [['id_shop', 'id_lang'], [1, 1], [2, 1], false],
            'LANG outer transaction' => [['id_lang'], [1], [2], true],
            'SHOP outer transaction' => [['id_shop'], [1], [2], true],
        ];
    }

    public function testSuccessfulBatchDoesNotCommitOuterTransaction(): void
    {
        $this->createTable(['id_shop']);
        $this->connection->beginTransaction();
        $this->write(['id_shop'], [[4, 1, 'FIRST', 'one'], [4, 2, 'SECOND', 'two']]);
        $this->assertTrue($this->connection->isTransactionActive());
        $this->connection->rollBack();

        $this->assertSame(0, (int) $this->connection->fetchOne('SELECT COUNT(*) FROM ' . $this->table));
    }

    public function testUpdateUsesEveryDimensionOfTheIdentity(): void
    {
        $this->createTable(['id_shop', 'id_lang']);
        $this->write(['id_shop', 'id_lang'], [[4, 1, 1, 'ONE', 'one'], [4, 1, 2, 'TWO', 'two'], [4, 2, 1, 'THREE', 'three']]);
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

    private function createTable(array $systemColumns): void
    {
        $dimensions = '';
        foreach ($systemColumns as $column) {
            $dimensions .= ', ' . $this->connection->quoteIdentifier($column) . ' INT NOT NULL';
        }
        $keys = implode(', ', array_map(fn (string $column): string => $this->connection->quoteIdentifier($column), ['id_customer', ...$systemColumns]));
        $this->connection->executeStatement('CREATE TEMPORARY TABLE ' . $this->table . ' (id_customer INT NOT NULL' . $dimensions . ', unique_value VARCHAR(255) NULL, note VARCHAR(255) NULL, PRIMARY KEY (' . $keys . '), UNIQUE KEY unique_value (unique_value)) ENGINE=InnoDB');
    }

    private function write(array $systemColumns, array $rows, array $dataColumns = ['unique_value', 'note']): void
    {
        $method = new ReflectionMethod(ExtraPropertyWriter::class, 'writeRows');
        $method->setAccessible(true);
        $method->invoke($this->writer, _DB_PREFIX_ . 'issue43296_writer', 'id_customer', $systemColumns, $dataColumns, $rows);
    }
}
