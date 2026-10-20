<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Integration\Classes\db;

use Db;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

/**
 * A character column that declares its own charset overrides the one its table declares, which
 * silently narrows what the column can store: a VARCHAR pinned to utf8 (utf8mb3) inside a utf8mb4
 * table rejects any 4-byte character with "Incorrect string value". It also makes the column
 * collate differently from every other column it is compared to (see issue #16636).
 *
 * Tables may legitimately differ from each other - modules ship their own install SQL - so this
 * only asserts the invariant that holds inside a single table.
 */
class DbColumnCollationTest extends KernelTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        self::bootKernel();
    }

    public function testEveryColumnUsesTheCollationOfItsOwnTable(): void
    {
        $mismatches = Db::getInstance()->executeS(
            'SELECT c.TABLE_NAME, c.COLUMN_NAME, c.COLLATION_NAME, t.TABLE_COLLATION
             FROM information_schema.COLUMNS c
             INNER JOIN information_schema.TABLES t
                 ON t.TABLE_SCHEMA = c.TABLE_SCHEMA AND t.TABLE_NAME = c.TABLE_NAME
             WHERE c.TABLE_SCHEMA = DATABASE()
                 AND c.COLLATION_NAME IS NOT NULL
                 AND c.COLLATION_NAME != t.TABLE_COLLATION
             ORDER BY c.TABLE_NAME, c.COLUMN_NAME'
        );

        $this->assertSame(
            [],
            array_map(
                static fn (array $row): string => sprintf(
                    '%s.%s is %s inside a %s table',
                    $row['TABLE_NAME'],
                    $row['COLUMN_NAME'],
                    $row['COLLATION_NAME'],
                    $row['TABLE_COLLATION']
                ),
                $mismatches ?: []
            )
        );
    }
}
