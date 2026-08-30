<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\Core\ExtraProperty\Schema;

use Doctrine\DBAL\Connection;
use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\ExtraProperty\Definition\ExtraPropertyDefinition;
use PrestaShop\PrestaShop\Core\ExtraProperty\Definition\ExtraPropertySqlIndex;
use PrestaShop\PrestaShop\Core\ExtraProperty\Definition\ExtraPropertyType;
use PrestaShop\PrestaShop\Core\ExtraProperty\Schema\ExtraPropertySchemaManager;
use Psr\Log\NullLogger;

/**
 * Covers ExtraPropertySchemaManager::syncExtraColumnDefinition() (via
 * ensureExtraTableAndColumn() on an existing column): the live column state from
 * SHOW COLUMNS is compared with the declared definition and, when they differ in a
 * non-destructive way, one ALTER TABLE … MODIFY COLUMN re-applies the definition.
 */
class ExtraPropertySchemaManagerSyncTest extends TestCase
{
    /** @var list<string> */
    private array $statements = [];

    /**
     * @dataProvider syncProvider
     *
     * @param array<string, mixed> $liveColumn SHOW COLUMNS row for the existing column
     * @param string|null $expectedSqlFragment Expected fragment of the MODIFY statement, null = no ALTER expected
     */
    public function testExistingColumnIsSyncedOnlyWhenDefinitionDiffers(
        array $liveColumn,
        ExtraPropertyDefinition $definition,
        ?string $expectedSqlFragment,
    ): void {
        $manager = $this->buildManager([$liveColumn + ['Field' => 'mymodule_test_field']]);

        $manager->ensureExtraTableAndColumn($definition);

        if (null === $expectedSqlFragment) {
            $this->assertSame([], $this->statements, 'No ALTER expected when the live column matches the definition');
        } else {
            $this->assertCount(1, $this->statements);
            $this->assertStringContainsString('MODIFY COLUMN', $this->statements[0]);
            $this->assertStringContainsString('`mymodule_test_field`', $this->statements[0]);
            $this->assertStringContainsString($expectedSqlFragment, $this->statements[0]);
        }
    }

    public static function syncProvider(): array
    {
        return [
            'matching nullable string column — untouched' => [
                ['Type' => 'varchar(255)', 'Null' => 'YES', 'Default' => null],
                self::definition(),
                null,
            ],
            'nullable relaxing is applied' => [
                ['Type' => 'varchar(255)', 'Null' => 'NO', 'Default' => null],
                self::definition(nullable: true),
                'VARCHAR(255) NULL',
            ],
            'size increase is applied' => [
                ['Type' => 'varchar(64)', 'Null' => 'YES', 'Default' => null],
                self::definition(size: 500),
                'VARCHAR(500) NULL',
            ],
            'implicit 255 size matches explicit varchar(255) — untouched' => [
                ['Type' => 'varchar(255)', 'Null' => 'YES', 'Default' => null],
                self::definition(size: null),
                null,
            ],
            'default change is applied' => [
                ['Type' => 'varchar(255)', 'Null' => 'YES', 'Default' => 'old'],
                self::definition(defaultValue: 'new'),
                "DEFAULT 'new'",
            ],
            'default removal is applied' => [
                ['Type' => 'varchar(255)', 'Null' => 'YES', 'Default' => 'old'],
                self::definition(defaultValue: null),
                'VARCHAR(255) NULL',
            ],
            'mariadb quoted default literal matches — untouched' => [
                ['Type' => 'varchar(255)', 'Null' => 'YES', 'Default' => "'new'"],
                self::definition(defaultValue: 'new'),
                null,
            ],
            'bool default 0 matches declared false — untouched' => [
                ['Type' => 'tinyint(1) unsigned', 'Null' => 'YES', 'Default' => '0'],
                self::definition(type: ExtraPropertyType::BOOL, defaultValue: false),
                null,
            ],
            'numeric default formatting difference matches — untouched' => [
                ['Type' => 'decimal(20,6)', 'Null' => 'YES', 'Default' => '1.500000'],
                self::definition(type: ExtraPropertyType::FLOAT, defaultValue: 1.5),
                null,
            ],
            'enum value addition is applied' => [
                ['Type' => "enum('a','b')", 'Null' => 'YES', 'Default' => null],
                self::definition(type: ExtraPropertyType::CHOICE, enumValues: ['a', 'b', 'c']),
                "ENUM('a','b','c')",
            ],
            'identical enum — untouched' => [
                ['Type' => "enum('a','b')", 'Null' => 'YES', 'Default' => null],
                self::definition(type: ExtraPropertyType::CHOICE, enumValues: ['a', 'b']),
                null,
            ],
        ];
    }

    /**
     * @param array<int, array<string, mixed>> $showColumnsRows
     */
    private function buildManager(array $showColumnsRows): ExtraPropertySchemaManager
    {
        $connection = $this->createMock(Connection::class);
        $connection->method('quoteIdentifier')->willReturnCallback(
            static fn (string $identifier): string => '`' . $identifier . '`'
        );
        $connection->method('fetchAllAssociative')->willReturn($showColumnsRows);
        $connection->method('executeStatement')->willReturnCallback(
            function (string $sql): int {
                $this->statements[] = $sql;

                return 1;
            }
        );

        // Table and column exist (sync path); index sync is out of scope here.
        return new class($connection, 'ps_', new NullLogger()) extends ExtraPropertySchemaManager {
            protected function tableExists(string $tableName): bool
            {
                return true;
            }

            protected function columnExists(string $tableName, string $columnName): bool
            {
                return true;
            }

            protected function syncExtraColumnIndex(string $extraTableName, string $columnName, ExtraPropertySqlIndex $sqlIndex): void
            {
            }
        };
    }

    private static function definition(
        ExtraPropertyType $type = ExtraPropertyType::STRING,
        ?int $size = null,
        bool $nullable = true,
        ?array $enumValues = null,
        int|float|string|bool|null $defaultValue = null,
    ): ExtraPropertyDefinition {
        return new ExtraPropertyDefinition(
            entityName: 'product',
            propertyName: 'test_field',
            type: $type,
            moduleName: 'mymodule',
            enumValues: $enumValues,
            defaultValue: $defaultValue,
            nullable: $nullable,
            size: $size,
        );
    }
}
