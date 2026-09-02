<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\Core\ExtraProperty\Schema;

use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\ExtraProperty\Definition\ExtraPropertyDefinition;
use PrestaShop\PrestaShop\Core\ExtraProperty\Definition\ExtraPropertyType;
use PrestaShop\PrestaShop\Core\ExtraProperty\Schema\ColumnDefinitionMapper;

class ColumnDefinitionMapperTest extends TestCase
{
    /**
     * @dataProvider sqlDefinitionProvider
     */
    public function testGetSqlDefinition(ExtraPropertyDefinition $options, string $expected): void
    {
        $this->assertSame($expected, ColumnDefinitionMapper::getSqlDefinition($options));
    }

    public static function sqlDefinitionProvider(): array
    {
        return [
            // INT
            'int nullable' => [
                new ExtraPropertyDefinition(entityName: 'entity', propertyName: 'test', type: ExtraPropertyType::INT, nullable: true),
                'INT(11) NULL',
            ],
            'int not null' => [
                new ExtraPropertyDefinition(entityName: 'entity', propertyName: 'test', type: ExtraPropertyType::INT, nullable: false),
                'INT(11) NOT NULL',
            ],
            'int with default' => [
                new ExtraPropertyDefinition(entityName: 'entity', propertyName: 'test', type: ExtraPropertyType::INT, nullable: false, defaultValue: 0),
                'INT(11) NOT NULL DEFAULT 0',
            ],

            // BOOL
            'bool nullable' => [
                new ExtraPropertyDefinition(entityName: 'entity', propertyName: 'test', type: ExtraPropertyType::BOOL, nullable: true),
                'TINYINT(1) UNSIGNED NULL',
            ],
            'bool not null' => [
                new ExtraPropertyDefinition(entityName: 'entity', propertyName: 'test', type: ExtraPropertyType::BOOL, nullable: false),
                'TINYINT(1) UNSIGNED NOT NULL',
            ],
            'bool with default 1' => [
                new ExtraPropertyDefinition(entityName: 'entity', propertyName: 'test', type: ExtraPropertyType::BOOL, nullable: false, defaultValue: 1),
                'TINYINT(1) UNSIGNED NOT NULL DEFAULT 1',
            ],

            // STRING
            'string default size nullable' => [
                new ExtraPropertyDefinition(entityName: 'entity', propertyName: 'test', type: ExtraPropertyType::STRING, nullable: true),
                'VARCHAR(255) NULL',
            ],
            'string custom size' => [
                new ExtraPropertyDefinition(entityName: 'entity', propertyName: 'test', type: ExtraPropertyType::STRING, nullable: true, size: 64),
                'VARCHAR(64) NULL',
            ],
            'string null size falls back to 255' => [
                new ExtraPropertyDefinition(entityName: 'entity', propertyName: 'test', type: ExtraPropertyType::STRING, nullable: false, size: null),
                'VARCHAR(255) NOT NULL',
            ],
            'string zero size falls back to 255' => [
                new ExtraPropertyDefinition(entityName: 'entity', propertyName: 'test', type: ExtraPropertyType::STRING, nullable: false, size: 0),
                'VARCHAR(255) NOT NULL',
            ],
            'string with quoted default' => [
                new ExtraPropertyDefinition(entityName: 'entity', propertyName: 'test', type: ExtraPropertyType::STRING, nullable: true, defaultValue: 'hello'),
                "VARCHAR(255) NULL DEFAULT 'hello'",
            ],
            'string default with single quote escaping' => [
                new ExtraPropertyDefinition(entityName: 'entity', propertyName: 'test', type: ExtraPropertyType::STRING, nullable: true, defaultValue: "it's"),
                "VARCHAR(255) NULL DEFAULT 'it''s'",
            ],

            // FLOAT
            'float nullable' => [
                new ExtraPropertyDefinition(entityName: 'entity', propertyName: 'test', type: ExtraPropertyType::FLOAT, nullable: true),
                'DECIMAL(20,6) NULL',
            ],
            'float not null with default' => [
                new ExtraPropertyDefinition(entityName: 'entity', propertyName: 'test', type: ExtraPropertyType::FLOAT, nullable: false, defaultValue: 0.0),
                'DECIMAL(20,6) NOT NULL DEFAULT 0',
            ],

            // DATE
            'date nullable' => [
                new ExtraPropertyDefinition(entityName: 'entity', propertyName: 'test', type: ExtraPropertyType::DATE, nullable: true),
                'DATETIME NULL',
            ],
            'date with default' => [
                new ExtraPropertyDefinition(entityName: 'entity', propertyName: 'test', type: ExtraPropertyType::DATE, nullable: false, defaultValue: '2000-01-01 00:00:00'),
                "DATETIME NOT NULL DEFAULT '2000-01-01 00:00:00'",
            ],

            // HTML
            'html nullable' => [
                new ExtraPropertyDefinition(entityName: 'entity', propertyName: 'test', type: ExtraPropertyType::HTML, nullable: true),
                'TEXT NULL',
            ],
            'html not null' => [
                new ExtraPropertyDefinition(entityName: 'entity', propertyName: 'test', type: ExtraPropertyType::HTML, nullable: false),
                'TEXT NOT NULL',
            ],

            // JSON
            'json nullable' => [
                new ExtraPropertyDefinition(entityName: 'entity', propertyName: 'test', type: ExtraPropertyType::JSON, nullable: true),
                'LONGTEXT NULL',
            ],
            'json not null' => [
                new ExtraPropertyDefinition(entityName: 'entity', propertyName: 'test', type: ExtraPropertyType::JSON, nullable: false),
                'LONGTEXT NOT NULL',
            ],

            // CHOICE with enum values
            'choice with values nullable' => [
                new ExtraPropertyDefinition(entityName: 'entity', propertyName: 'test', type: ExtraPropertyType::CHOICE, nullable: true, enumValues: ['pending', 'active', 'closed']),
                "ENUM('pending','active','closed') NULL",
            ],
            'choice with values not null with default' => [
                new ExtraPropertyDefinition(entityName: 'entity', propertyName: 'test', type: ExtraPropertyType::CHOICE, nullable: false, enumValues: ['a', 'b'], defaultValue: 'a'),
                "ENUM('a','b') NOT NULL DEFAULT 'a'",
            ],
            'choice enum value with single-quote escaping' => [
                new ExtraPropertyDefinition(entityName: 'entity', propertyName: 'test', type: ExtraPropertyType::CHOICE, nullable: true, enumValues: ["it's", 'normal']),
                "ENUM('it''s','normal') NULL",
            ],
            // CHOICE without enum values falls back to VARCHAR(64)
            'choice without enum values' => [
                new ExtraPropertyDefinition(entityName: 'entity', propertyName: 'test', type: ExtraPropertyType::CHOICE, nullable: true, enumValues: []),
                'VARCHAR(64) NULL',
            ],
            'choice with null enum values' => [
                new ExtraPropertyDefinition(entityName: 'entity', propertyName: 'test', type: ExtraPropertyType::CHOICE, nullable: true, enumValues: null),
                'VARCHAR(64) NULL',
            ],
        ];
    }

    public function testEnumValuesNonStringItemsAreFiltered(): void
    {
        // enumValues can only contain strings; mixed arrays should filter non-strings
        $options = new ExtraPropertyDefinition(entityName: 'entity', propertyName: 'test',
            type: ExtraPropertyType::CHOICE,
            nullable: true,
            enumValues: ['valid', 'also_valid'],
        );

        $this->assertSame("ENUM('valid','also_valid') NULL", ColumnDefinitionMapper::getSqlDefinition($options));
    }

    public function testNoDefaultValueOmitsDefaultClause(): void
    {
        $options = new ExtraPropertyDefinition(entityName: 'entity', propertyName: 'test', type: ExtraPropertyType::INT, nullable: true, defaultValue: null);

        $this->assertSame('INT(11) NULL', ColumnDefinitionMapper::getSqlDefinition($options));
        $this->assertStringNotContainsString('DEFAULT', ColumnDefinitionMapper::getSqlDefinition($options));
    }
}
