<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Integration\PrestaShopBundle\Service\Database;

use Db;
use Doctrine\DBAL\Connection;
use Exception;
use PrestaShop\PrestaShop\Core\Repository\TransactionManagerInterface;
use PrestaShopException;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Tag;

/**
 * Proves that legacy ObjectModel writes and Doctrine DBAL writes performed inside
 * TransactionManager::executeInTransaction() share the same physical transaction:
 * they must commit together and roll back together.
 */
class TransactionManagerTest extends KernelTestCase
{
    // Tag.name is capped at 32 chars (see Tag::$definition) - keep generated names well under that.
    private const TAG_NAME_PREFIX = 'txm-';

    private TransactionManagerInterface $transactionManager;
    private Connection $connection;

    protected function setUp(): void
    {
        self::bootKernel();
        $container = self::getContainer();
        $this->transactionManager = $container->get(TransactionManagerInterface::class);
        $this->connection = $container->get('doctrine.dbal.default_connection');
    }

    protected function tearDown(): void
    {
        $this->connection->executeStatement(
            'DELETE FROM ' . _DB_PREFIX_ . 'tag WHERE name LIKE :name',
            ['name' => self::TAG_NAME_PREFIX . '%']
        );
    }

    public function testLegacyAndDoctrineWritesAreCommittedTogether(): void
    {
        $id = $this->uniqueTagId();

        $this->transactionManager->executeInTransaction(function () use ($id) {
            $this->addTagViaObjectModel($id . '-l');
            $this->addTagViaDoctrine($id . '-d');
        });

        $this->assertSame(2, $this->countTagsMatching($id . '%'));
    }

    public function testLegacyAndDoctrineWritesAreRolledBackTogether(): void
    {
        $id = $this->uniqueTagId();

        try {
            $this->transactionManager->executeInTransaction(function () use ($id) {
                $this->addTagViaObjectModel($id . '-l');
                $this->addTagViaDoctrine($id . '-d');

                throw new Exception('Forced failure to trigger a rollback');
            });
            $this->fail('Expected exception was not thrown');
        } catch (Exception $e) {
            $this->assertSame('Forced failure to trigger a rollback', $e->getMessage());
        }

        $this->assertSame(0, $this->countTagsMatching($id . '%'));
    }

    public function testSharingTheConnectionFailsIfTheLegacyConnectionHasAPendingTransaction(): void
    {
        $legacyDb = Db::getInstance();
        $legacyDb->execute('START TRANSACTION');

        try {
            $this->expectException(PrestaShopException::class);
            $this->transactionManager->executeInTransaction(function () {
                $this->fail('The callable must not run once sharing the connection has failed');
            });
        } finally {
            $legacyDb->execute('ROLLBACK');
        }
    }

    private function uniqueTagId(): string
    {
        return self::TAG_NAME_PREFIX . substr(md5(uniqid('', true)), 0, 8);
    }

    private function addTagViaObjectModel(string $name): void
    {
        $tag = new Tag();
        $tag->id_lang = 1;
        $tag->name = $name;
        $tag->add();
    }

    private function addTagViaDoctrine(string $name): void
    {
        $this->connection->insert(_DB_PREFIX_ . 'tag', [
            'id_lang' => 1,
            'name' => $name,
        ]);
    }

    private function countTagsMatching(string $namePattern): int
    {
        return (int) $this->connection->fetchOne(
            'SELECT COUNT(*) FROM ' . _DB_PREFIX_ . 'tag WHERE name LIKE :name',
            ['name' => $namePattern]
        );
    }
}
