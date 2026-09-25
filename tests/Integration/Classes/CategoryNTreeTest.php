<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Integration\Classes;

use Category;
use Db;
use PHPUnit\Framework\TestCase;
use Tests\Resources\DatabaseDump;

class CategoryNTreeTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        DatabaseDump::restoreTables(['category']);
    }

    protected function tearDown(): void
    {
        // The connection is shared, so put back the mode PrestaShop sets on connect.
        Db::getInstance()->execute("SET SESSION sql_mode = ''");
        DatabaseDump::restoreTables(['category']);

        parent::tearDown();
    }

    /**
     * PrestaShop clears sql_mode on its own connection, which is the only reason the previous
     * INSERT ... ON DUPLICATE KEY UPDATE survived: it named id_category, nleft and nright while
     * id_parent, date_add and date_upd are NOT NULL with no default. Any strict session rejects
     * that statement, and the Doctrine connection is strict.
     *
     * @see https://github.com/PrestaShop/PrestaShop/issues/28335
     */
    public function testTheTreeIsRegeneratedUnderAStrictSqlMode(): void
    {
        $db = Db::getInstance();
        $db->execute('UPDATE `' . _DB_PREFIX_ . 'category` SET `nleft` = 0, `nright` = 0');
        $this->assertSame(0, $this->countCategoriesWithATree());

        $db->execute("SET SESSION sql_mode = 'STRICT_TRANS_TABLES'");
        $this->assertSame('STRICT_TRANS_TABLES', $db->getValue('SELECT @@session.sql_mode'));

        Category::regenerateEntireNtree();

        $this->assertGreaterThan(0, $this->countCategoriesWithATree());
        $this->assertSame(0, $this->countCategoriesWithABrokenTree());
    }

    private function countCategoriesWithATree(): int
    {
        return (int) Db::getInstance()->getValue(
            'SELECT COUNT(*) FROM `' . _DB_PREFIX_ . 'category` WHERE `nleft` > 0 AND `nright` > `nleft`'
        );
    }

    private function countCategoriesWithABrokenTree(): int
    {
        return (int) Db::getInstance()->getValue(
            'SELECT COUNT(*) FROM `' . _DB_PREFIX_ . 'category` WHERE `nright` <= `nleft`'
        );
    }
}
