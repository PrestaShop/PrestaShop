<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Integration\Classes\db;

use Db;
use PHPUnit\Framework\TestCase;
use PrestaShopDatabaseException;

class DbTest extends TestCase
{
    /**
     * @var Db
     */
    private $firstSlave;

    /**
     * @var Db
     */
    private $secondSlave;

    /**
     * @var Db
     */
    private $master;

    protected function tearDown(): void
    {
        Db::$_slave_servers_loaded = false;
        Db::$_servers = [];
        Db::$instance = [];
    }

    public function testGetInstanceShouldLoadSlavesCorrectlyEvenWhenMasterIsCalled(): void
    {
        $this->loadSlaves(2);

        $this->master = Db::getInstance();

        // When
        $this->firstSlave = Db::getInstance((bool) _PS_USE_SQL_SLAVE_);
        $this->secondSlave = Db::getInstance((bool) _PS_USE_SQL_SLAVE_);

        // Then
        $this->assertNotSame($this->firstSlave, $this->secondSlave);
        $this->assertNotSame($this->master, $this->secondSlave);
        $this->assertNotSame($this->master, $this->firstSlave);

        $this->assertTwoCallsOnFirst_ThenOneOnSecondSlave();

        $this->assertSame($this->master, Db::getInstance());

        $this->assertTwoCallsOnFirst_ThenOneOnSecondSlave();
        $this->assertTwoCallsOnFirst_ThenOneOnSecondSlave();
    }

    private function assertTwoCallsOnFirst_ThenOneOnSecondSlave(): void
    {
        // Third and fourth calls are on first slave
        $this->assertSame($this->firstSlave, Db::getInstance((bool) _PS_USE_SQL_SLAVE_));
        $this->assertSame($this->firstSlave, Db::getInstance((bool) _PS_USE_SQL_SLAVE_));

        // Fifth call is on second slave
        $this->assertSame($this->secondSlave, Db::getInstance(_PS_USE_SQL_SLAVE_));
    }

    private function loadSlaves(int $nbServers = 0): void
    {
        Db::$_slave_servers_loaded = true;
        Db::$_servers = [];
        for ($i = 0; $i <= $nbServers; ++$i) {
            Db::$_servers[] = ['server' => _DB_SERVER_, 'user' => _DB_USER_, 'password' => _DB_PASSWD_, 'database' => _DB_NAME_];
        }
    }

    /**
     * A legitimate read query whose values merely contain "outfile"/"dumpfile" (for example a shop
     * search for such a term) must not be rejected by the write-to-file guard.
     *
     * @dataProvider provideReadQueriesContainingFileKeywords
     */
    public function testExecuteSAcceptsReadQueriesContainingFileKeywordsInValues(string $sql): void
    {
        $result = Db::getInstance()->executeS($sql);

        $this->assertIsArray($result);
    }

    public function provideReadQueriesContainingFileKeywords(): iterable
    {
        yield 'outfile inside a LIKE value' => [
            'SELECT `id_product` FROM `' . _DB_PREFIX_ . 'product` WHERE `reference` LIKE \'%outfile%\'',
        ];
        yield 'dumpfile inside a LIKE value' => [
            'SELECT `id_product` FROM `' . _DB_PREFIX_ . 'product` WHERE `reference` LIKE \'%dumpfile%\'',
        ];
        yield 'keyword split across an escaped quote' => [
            'SELECT `id_product` FROM `' . _DB_PREFIX_ . 'product` WHERE `reference` LIKE \'%out\\\'file%\'',
        ];
    }

    /**
     * A real "INTO OUTFILE"/"INTO DUMPFILE" write clause must still be rejected by executeS(),
     * including when whitespace is obfuscated with a comment.
     *
     * @dataProvider provideWriteToFileQueries
     */
    public function testExecuteSRejectsWriteToFileClause(string $sql): void
    {
        $this->expectException(PrestaShopDatabaseException::class);
        $this->expectExceptionMessage('must be used only');

        Db::getInstance()->executeS($sql);
    }

    public function provideWriteToFileQueries(): iterable
    {
        yield 'into outfile' => [
            'SELECT `id_product` FROM `' . _DB_PREFIX_ . 'product` INTO OUTFILE \'/tmp/ps-guard-test\'',
        ];
        yield 'into dumpfile' => [
            'SELECT `id_product` FROM `' . _DB_PREFIX_ . 'product` INTO DUMPFILE \'/tmp/ps-guard-test\'',
        ];
        yield 'into outfile obfuscated with a comment' => [
            'SELECT `id_product` FROM `' . _DB_PREFIX_ . 'product` INTO/**/OUTFILE \'/tmp/ps-guard-test\'',
        ];
        // Hostile input crafted to make the literal-stripping regex bail must fail closed, not open.
        yield 'into outfile with a backslash-flood unterminated literal' => [
            'SELECT `id_product` FROM `' . _DB_PREFIX_ . 'product` INTO OUTFILE \'' . str_repeat('\\', 60),
        ];
    }
}
