<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Integration\PrestaShopBundle\Entity\Repository;

use Doctrine\DBAL\Configuration;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DriverManager;
use Doctrine\DBAL\Exception\LockWaitTimeoutException;
use Doctrine\DBAL\Schema\DefaultSchemaManagerFactory;
use PrestaShopBundle\Entity\CustomerTradeCreditOperation;
use PrestaShopBundle\Entity\Repository\CustomerTradeCreditRepository;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Tests\Resources\DatabaseDump;

class CustomerTradeCreditRepositoryTest extends KernelTestCase
{
    private CustomerTradeCreditRepository $repository;

    private Connection $connection;

    protected function setUp(): void
    {
        self::bootKernel();
        $this->repository = self::getContainer()->get(CustomerTradeCreditRepository::class);
        $this->connection = self::getContainer()->get('doctrine.dbal.default_connection');
    }

    protected function tearDown(): void
    {
        DatabaseDump::restoreTables(['customer_trade_credit', 'customer_trade_credit_operation']);
        parent::tearDown();
    }

    public function testFindBalanceReturnsNullWhenNotGranted(): void
    {
        $this->assertNull($this->repository->findBalance(1));
    }

    public function testGrantIfMissingCreatesBalanceOnce(): void
    {
        $this->assertTrue($this->repository->grantIfMissing(1, '50000.00'));
        $this->assertFalse($this->repository->grantIfMissing(1, '50000.00'));

        $this->assertSame('50000.000000', $this->repository->findBalance(1));
        $this->assertNotNull($this->repository->findOperation('grant:customer:1'));
        $this->assertSame(1, $this->countOperations(1));
    }

    public function testGrantIfMissingDoesNothingWhenAdminCreatedBalance(): void
    {
        $this->repository->createBalance(1, '100.00');

        $this->assertFalse($this->repository->grantIfMissing(1, '50000.00'));
        $this->assertSame('100.000000', $this->repository->findBalance(1));
        $this->assertNull($this->repository->findOperation('grant:customer:1'));
    }

    public function testAddOperationIsIdempotentByReference(): void
    {
        $operation = $this->operationRow('refund:order:10', CustomerTradeCreditOperation::TYPE_REFUND, '25.00');

        $this->assertTrue($this->repository->addOperation($operation));
        $this->assertFalse($this->repository->addOperation($operation));
        $this->assertSame(1, $this->countOperations(1));
    }

    public function testLockBalanceBlocksConcurrentTransaction(): void
    {
        $this->repository->createBalance(1, '50000.00');

        $configuration = new Configuration();
        $configuration->setSchemaManagerFactory(new DefaultSchemaManagerFactory());
        $other = DriverManager::getConnection($this->connection->getParams(), $configuration);
        $other->beginTransaction();
        $other->fetchOne(
            'SELECT amount FROM ' . _DB_PREFIX_ . 'customer_trade_credit WHERE id_customer = 1 FOR UPDATE'
        );

        try {
            $this->connection->executeStatement('SET SESSION innodb_lock_wait_timeout = 1');
            $this->expectException(LockWaitTimeoutException::class);
            $this->repository->transactional(fn () => $this->repository->lockBalance(1));
        } finally {
            $other->rollBack();
            $other->close();
        }
    }

    public function testSumOperationsByCart(): void
    {
        $this->repository->addOperation($this->operationRow('refund:slip:1', CustomerTradeCreditOperation::TYPE_REFUND, '10.00', ['id_cart' => 5]));
        $this->repository->addOperation($this->operationRow('refund:slip:2', CustomerTradeCreditOperation::TYPE_REFUND, '2.50', ['id_cart' => 5]));
        $this->repository->addOperation($this->operationRow('refund:slip:3', CustomerTradeCreditOperation::TYPE_REFUND, '99.00', ['id_cart' => 6]));

        $this->assertSame(
            '12.500000',
            $this->repository->sumOperations(1, CustomerTradeCreditOperation::TYPE_REFUND, ['id_cart' => 5])
        );
    }

    private function operationRow(string $reference, string $type, string $amount, array $extra = []): array
    {
        return array_merge([
            'id_customer' => 1,
            'type' => $type,
            'amount' => $amount,
            'balance_after' => '0.00',
            'reference' => $reference,
        ], $extra);
    }

    private function countOperations(int $customerId): int
    {
        return (int) $this->connection->fetchOne(
            'SELECT COUNT(*) FROM ' . _DB_PREFIX_ . 'customer_trade_credit_operation WHERE id_customer = ?',
            [$customerId]
        );
    }
}
