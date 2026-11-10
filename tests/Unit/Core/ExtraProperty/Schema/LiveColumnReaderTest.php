<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\Core\ExtraProperty\Schema;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Platforms\PostgreSQLPlatform;
use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\ExtraProperty\Schema\LiveColumnReader;

class LiveColumnReaderTest extends TestCase
{
    public function testFetchColumnsOnMySqlReturnsShowColumnsRows(): void
    {
        $rows = [['Field' => 'id_product', 'Type' => 'int(10) unsigned', 'Null' => 'NO', 'Default' => null, 'Key' => 'PRI']];
        $connection = $this->createMock(Connection::class);
        $connection->method('quoteIdentifier')->willReturnCallback(static fn (string $identifier): string => '`' . $identifier . '`');
        $connection->expects($this->once())->method('fetchAllAssociative')->with('SHOW COLUMNS FROM `ps_product_extra`')->willReturn($rows);

        $this->assertSame($rows, LiveColumnReader::fetchColumns($connection, 'ps_product_extra'));
    }

    public function testFetchColumnsOnPostgreSqlRebuildsShowColumnsRows(): void
    {
        $connection = $this->createMock(Connection::class);
        $connection->method('getDatabasePlatform')->willReturn(new PostgreSQLPlatform());
        $connection->method('quoteIdentifier')->willReturnCallback(static fn (string $identifier): string => '"' . $identifier . '"');
        $connection->method('fetchAllAssociative')->willReturn([
            ['field' => 'id_product', 'type' => 'bigint', 'not_null' => true, 'default_value' => null, 'is_primary' => true, 'check_definition' => null],
            ['field' => 'note', 'type' => 'character varying(500)', 'not_null' => false, 'default_value' => "'it''s'::character varying", 'is_primary' => false, 'check_definition' => null],
            ['field' => 'level', 'type' => 'character varying(255)', 'not_null' => true, 'default_value' => "'low'::character varying", 'is_primary' => false,
                'check_definition' => "CHECK (((level)::text = ANY ((ARRAY['low'::character varying, 'it''s'::character varying])::text[])))"],
            ['field' => 'rank', 'type' => 'integer', 'not_null' => false, 'default_value' => "'-1'::integer", 'is_primary' => false, 'check_definition' => null],
            ['field' => 'price', 'type' => 'numeric(20,6)', 'not_null' => false, 'default_value' => '1.500000', 'is_primary' => false, 'check_definition' => null],
        ]);

        $this->assertSame(
            [
                ['Field' => 'id_product', 'Type' => 'bigint', 'Null' => 'NO', 'Default' => null, 'Key' => 'PRI'],
                ['Field' => 'note', 'Type' => 'varchar(500)', 'Null' => 'YES', 'Default' => "it's", 'Key' => ''],
                ['Field' => 'level', 'Type' => "enum('low','it''s')", 'Null' => 'NO', 'Default' => 'low', 'Key' => ''],
                ['Field' => 'rank', 'Type' => 'integer', 'Null' => 'YES', 'Default' => '-1', 'Key' => ''],
                ['Field' => 'price', 'Type' => 'numeric(20,6)', 'Null' => 'YES', 'Default' => '1.500000', 'Key' => ''],
            ],
            LiveColumnReader::fetchColumns($connection, 'ps_product_extra')
        );
    }
}
