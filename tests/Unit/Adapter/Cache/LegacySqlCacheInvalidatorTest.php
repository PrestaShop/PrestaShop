<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\Adapter\Cache;

use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Adapter\Cache\LegacySqlCacheInvalidator;

class LegacySqlCacheInvalidatorTest extends TestCase
{
    /**
     * @dataProvider provideStatements
     */
    public function testItRecognisesTheStatementsThatChangeRows(string $sql, bool $expected): void
    {
        $this->assertSame($expected, (new LegacySqlCacheInvalidator(true))->shouldInvalidate($sql));
    }

    public static function provideStatements(): array
    {
        return [
            'insert' => ['INSERT INTO ps_link_block (id_hook) VALUES (2)', true],
            'update' => ['UPDATE ps_link_block SET id_hook = 2', true],
            'delete' => ['DELETE FROM ps_link_block WHERE id_link_block = 1', true],
            'replace' => ['REPLACE INTO ps_link_block (id_hook) VALUES (2)', true],
            'truncate' => ['TRUNCATE TABLE ps_link_block', true],
            'alter' => ['ALTER TABLE ps_link_block ADD COLUMN foo INT', true],
            'drop' => ['DROP TABLE ps_link_block', true],
            'create' => ['CREATE TABLE ps_link_block (id INT)', true],
            'rename' => ['RENAME TABLE ps_link_block TO ps_link_block_old', true],
            'lower case' => ['update ps_link_block set id_hook = 2', true],
            'leading whitespace and newline' => ["\n    UPDATE ps_link_block SET id_hook = 2", true],
            'select' => ['SELECT id_link_block FROM ps_link_block', false],
            // The column is named date_upd and the table name contains "update"; neither is a write.
            'select of columns whose names start like a verb' => ['SELECT date_upd, id FROM ps_updates', false],
            'select mentioning a verb further along' => ["SELECT id FROM ps_log WHERE message = 'UPDATE failed'", false],
            'show' => ['SHOW TABLES', false],
            'describe' => ['DESCRIBE ps_link_block', false],
            'set' => ["SET SESSION time_zone = '+02:00'", false],
            'empty' => ['', false],
        ];
    }

    /**
     * Shops that do not use the legacy SQL cache, which is the default, must pay nothing and must
     * never cause a cache backend to be built. The flag is the same one Db reads to decide whether
     * it caches at all.
     */
    public function testItDoesNothingWhileTheLegacyCacheIsDisabled(): void
    {
        $invalidator = new LegacySqlCacheInvalidator(false);

        $this->assertFalse($invalidator->shouldInvalidate('UPDATE ps_link_block SET id_hook = 2'));

        // Reaching Cache::getInstance() here would fatal, since no legacy cache is configured.
        $invalidator->invalidate('UPDATE ps_link_block SET id_hook = 2');
        $this->addToAssertionCount(1);
    }
}
