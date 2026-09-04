<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Integration\PrestaShopBundle\Service\Database;

use Db;
use Doctrine\DBAL\Connection;
use Doctrine\ORM\EntityManagerInterface;
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
    private EntityManagerInterface $entityManager;

    protected function setUp(): void
    {
        self::bootKernel();
        $container = self::getContainer();
        $this->transactionManager = $container->get(TransactionManagerInterface::class);
        $this->connection = $container->get('doctrine.dbal.default_connection');
        $this->entityManager = $container->get('doctrine.orm.entity_manager');
    }

    protected function tearDown(): void
    {
        $this->connection->executeStatement(
            'DELETE FROM ' . _DB_PREFIX_ . 'tag WHERE name LIKE :name',
            ['name' => self::TAG_NAME_PREFIX . '%']
        );

        // The legacy Db singleton is static and outlives the kernel, so a DbDoctrine left behind here would
        // be reused by the next test while pointing at the previous boot's (now discarded) Doctrine
        // connection. Dropping it keeps each test starting from a plain, legacy-only Db instance.
        Db::deleteTestingInstance();

        parent::tearDown();
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
        // Start from a plain, legacy-only Db instance so switchConnection() has to take its rebuild path:
        // reusing an already-shared DbDoctrine is a deliberate no-op (that is what makes a transactional
        // handler dispatching another transactional command work), so it would not throw here.
        Db::deleteTestingInstance();
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

    /**
     * PrestaShop command handlers throw domain exceptions as ordinary control flow, and BO controllers catch
     * them and re-render the page. EntityManager::wrapInTransaction() closes the manager on any failure, which
     * would make every later Doctrine call in that request fail with "The EntityManager is closed" - so the
     * rollback must not take the manager down with it.
     */
    public function testTheEntityManagerStaysUsableAfterARolledBackTransaction(): void
    {
        $id = $this->uniqueTagId();

        try {
            $this->transactionManager->executeInTransaction(function () use ($id) {
                $this->addTagViaObjectModel($id . '-l');

                throw new Exception('Domain-style failure the caller handles itself');
            });
            $this->fail('Expected exception was not thrown');
        } catch (Exception $e) {
            $this->assertSame('Domain-style failure the caller handles itself', $e->getMessage());
        }

        $this->assertTrue($this->entityManager->isOpen());
        // flush() throws EntityManagerClosed on a closed manager, so this proves it is still usable.
        $this->entityManager->flush();
        $this->assertSame(0, $this->countTagsMatching($id . '%'));
    }

    public function testANestedTransactionalCallIsNotRejectedAsAConflictingTransaction(): void
    {
        $id = $this->uniqueTagId();

        $this->transactionManager->executeInTransaction(function () use ($id) {
            $this->addTagViaObjectModel($id . '-outer');

            // Re-entering while the shared connection already has an open transaction must reuse it,
            // not trip the "connection has an uncommitted transaction" guard.
            $this->transactionManager->executeInTransaction(function () use ($id) {
                $this->addTagViaDoctrine($id . '-inner');
            });
        });

        $this->assertSame(2, $this->countTagsMatching($id . '%'));
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
