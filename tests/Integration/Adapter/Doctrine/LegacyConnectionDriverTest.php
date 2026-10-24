<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Integration\Adapter\Doctrine;

use Db;
use Doctrine\DBAL\Connection;
use PrestaShop\PrestaShop\Adapter\Doctrine\LegacyConnectionDriver;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class LegacyConnectionDriverTest extends KernelTestCase
{
    private const LEGACY_ROW = 'PS_TEST_SHARED_CONNECTION_LEGACY';
    private const DOCTRINE_ROW = 'PS_TEST_SHARED_CONNECTION_DOCTRINE';

    private Connection $connection;

    protected function setUp(): void
    {
        self::bootKernel();
        $this->connection = self::getContainer()->get('doctrine.dbal.default_connection');
    }

    protected function tearDown(): void
    {
        if (Db::getInstance()->getLink()->inTransaction()) {
            Db::getInstance()->execute('ROLLBACK');
        }
        Db::getInstance()->delete('configuration', '`name` IN ("' . self::LEGACY_ROW . '", "' . self::DOCTRINE_ROW . '")');

        parent::tearDown();
    }

    public function testDoctrineReusesLegacyLink(): void
    {
        $this->assertSame(Db::getInstance()->getLink(), $this->connection->getNativeConnection());
        $this->assertSame(
            Db::getInstance()->getValue('SELECT CONNECTION_ID()', false),
            $this->connection->fetchOne('SELECT CONNECTION_ID()')
        );
    }

    public function testDoctrineTransactionCoversLegacyWrites(): void
    {
        $this->connection->beginTransaction();
        $this->insertLegacyRow();
        $this->connection->rollBack();

        $this->assertFalse($this->rowExists(self::LEGACY_ROW));
    }

    public function testDoctrineRollbackInsideLegacyTransactionKeepsLegacyWrites(): void
    {
        Db::getInstance()->execute('START TRANSACTION');
        $this->insertLegacyRow();
        $this->connection->beginTransaction();
        $this->insertDoctrineRow();
        $this->connection->rollBack();
        Db::getInstance()->execute('COMMIT');

        $this->assertTrue($this->rowExists(self::LEGACY_ROW));
        $this->assertFalse($this->rowExists(self::DOCTRINE_ROW));
    }

    public function testDoctrineCommitInsideLegacyTransactionFollowsLegacyRollback(): void
    {
        Db::getInstance()->execute('START TRANSACTION');
        $this->connection->beginTransaction();
        $this->insertDoctrineRow();
        $this->connection->commit();
        Db::getInstance()->execute('ROLLBACK');

        $this->assertFalse($this->rowExists(self::DOCTRINE_ROW));
    }

    public function testDoctrineCommitAfterLegacyImplicitCommit(): void
    {
        $this->connection->beginTransaction();
        $this->insertDoctrineRow();
        Db::getInstance()->execute('COMMIT');
        $this->connection->commit();

        $this->assertTrue($this->rowExists(self::DOCTRINE_ROW));
    }

    public function testOwnConnectionWithoutLegacyLink(): void
    {
        $instances = Db::$instance;
        Db::$instance = [];
        try {
            $connection = (new LegacyConnectionDriver())->connect($this->connection->getParams());
        } finally {
            Db::$instance = $instances;
        }

        $this->assertNotSame(Db::getInstance()->getLink(), $connection->getNativeConnection());
    }

    private function insertLegacyRow(): void
    {
        Db::getInstance()->insert('configuration', $this->buildRow(self::LEGACY_ROW));
    }

    private function insertDoctrineRow(): void
    {
        $this->connection->insert(_DB_PREFIX_ . 'configuration', $this->buildRow(self::DOCTRINE_ROW));
    }

    private function buildRow(string $name): array
    {
        return [
            'name' => $name,
            'value' => '1',
            'date_add' => date('Y-m-d H:i:s'),
            'date_upd' => date('Y-m-d H:i:s'),
        ];
    }

    private function rowExists(string $name): bool
    {
        return (bool) Db::getInstance()->getValue(
            'SELECT 1 FROM `' . _DB_PREFIX_ . 'configuration` WHERE `name` = "' . $name . '"',
            false
        );
    }
}
