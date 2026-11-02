<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShopBundle\Entity\Repository;

use Closure;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Doctrine\ORM\EntityRepository;
use PrestaShopBundle\Entity\CustomerTradeCreditOperation;

/**
 * Balance and operations of customer trade credit.
 *
 * Writes go through DBAL on purpose: the balance row must be locked with SELECT ... FOR UPDATE
 * and operations rely on the unique reference for idempotency, which the ORM unit of work does not express.
 */
class CustomerTradeCreditRepository extends EntityRepository
{
    private const OPERATION_COLUMNS = [
        'id_customer', 'type', 'amount', 'balance_after', 'id_cart', 'id_order', 'id_order_slip', 'id_employee', 'reference',
    ];

    private const OPERATION_CRITERIA = ['id_cart', 'id_order', 'id_order_slip'];

    private string $tablePrefix = '';

    public function setTablePrefix(string $tablePrefix): void
    {
        $this->tablePrefix = $tablePrefix;
    }

    /**
     * Runs the callable in a database transaction and returns its result.
     */
    public function transactional(callable $callable): mixed
    {
        return $this->getConnection()->transactional(Closure::fromCallable($callable));
    }

    public function findBalance(int $customerId): ?string
    {
        $amount = $this->getConnection()->fetchOne(
            'SELECT amount FROM ' . $this->balanceTable() . ' WHERE id_customer = ?',
            [$customerId]
        );

        return false === $amount ? null : (string) $amount;
    }

    /**
     * Locks the balance row until the end of the current transaction.
     */
    public function lockBalance(int $customerId): ?string
    {
        $amount = $this->getConnection()->fetchOne(
            'SELECT amount FROM ' . $this->balanceTable() . ' WHERE id_customer = ? FOR UPDATE',
            [$customerId]
        );

        return false === $amount ? null : (string) $amount;
    }

    /**
     * Grants the default credit when the customer has no balance yet.
     *
     * @return bool true when the credit was granted by this call
     */
    public function grantIfMissing(int $customerId, string $amount): bool
    {
        return $this->transactional(function () use ($customerId, $amount): bool {
            try {
                $this->createBalance($customerId, $amount);
            } catch (UniqueConstraintViolationException) {
                return false;
            }

            return $this->addOperation([
                'id_customer' => $customerId,
                'type' => CustomerTradeCreditOperation::TYPE_GRANT,
                'amount' => $amount,
                'balance_after' => $amount,
                'reference' => sprintf('grant:customer:%d', $customerId),
            ]);
        });
    }

    public function createBalance(int $customerId, string $amount): void
    {
        $this->getConnection()->executeStatement(
            'INSERT INTO ' . $this->balanceTable() . ' (id_customer, amount, date_add, date_upd) VALUES (?, ?, NOW(), NOW())',
            [$customerId, $amount]
        );
    }

    public function updateBalance(int $customerId, string $amount): void
    {
        $this->getConnection()->executeStatement(
            'UPDATE ' . $this->balanceTable() . ' SET amount = ?, date_upd = NOW() WHERE id_customer = ?',
            [$amount, $customerId]
        );
    }

    /**
     * @param array<string, int|string|null> $row
     *
     * @return bool false when an operation with the same reference already exists
     */
    public function addOperation(array $row): bool
    {
        $row = array_intersect_key($row, array_flip(self::OPERATION_COLUMNS));
        $columns = array_keys($row);

        try {
            $this->getConnection()->executeStatement(
                sprintf(
                    'INSERT INTO %s (%s, date_add) VALUES (%s, NOW())',
                    $this->operationTable(),
                    implode(', ', $columns),
                    implode(', ', array_fill(0, count($columns), '?'))
                ),
                array_values($row)
            );
        } catch (UniqueConstraintViolationException) {
            return false;
        }

        return true;
    }

    /**
     * @param array<string, int> $criteria keys among id_cart, id_order, id_order_slip
     */
    public function sumOperations(int $customerId, string $type, array $criteria = []): string
    {
        $criteria = array_intersect_key($criteria, array_flip(self::OPERATION_CRITERIA));
        $sql = 'SELECT CAST(COALESCE(SUM(amount), 0) AS DECIMAL(20,6)) FROM ' . $this->operationTable()
            . ' WHERE id_customer = ? AND type = ?';
        $params = [$customerId, $type];

        foreach ($criteria as $column => $value) {
            $sql .= ' AND ' . $column . ' = ?';
            $params[] = $value;
        }

        return (string) $this->getConnection()->fetchOne($sql, $params);
    }

    /**
     * @return array<string, mixed>|null
     */
    public function findOperation(string $reference): ?array
    {
        $row = $this->getConnection()->fetchAssociative(
            'SELECT * FROM ' . $this->operationTable() . ' WHERE reference = ?',
            [$reference]
        );

        return false === $row ? null : $row;
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function findLastOperations(int $customerId, int $limit): array
    {
        return $this->getConnection()->fetchAllAssociative(
            'SELECT * FROM ' . $this->operationTable() . ' WHERE id_customer = ?'
            . ' ORDER BY id_customer_trade_credit_operation DESC LIMIT ' . max(1, $limit),
            [$customerId]
        );
    }

    /**
     * Sum of the totals (tax incl.) of the orders created from a cart; several with multi-shipping.
     */
    public function getCartOrdersTotal(int $cartId): string
    {
        return (string) $this->getConnection()->fetchOne(
            'SELECT CAST(COALESCE(SUM(total_paid_tax_incl), 0) AS DECIMAL(20,6)) FROM ' . $this->tablePrefix . 'orders WHERE id_cart = ?',
            [$cartId]
        );
    }

    private function getConnection(): Connection
    {
        return $this->getEntityManager()->getConnection();
    }

    private function balanceTable(): string
    {
        return $this->tablePrefix . 'customer_trade_credit';
    }

    private function operationTable(): string
    {
        return $this->tablePrefix . 'customer_trade_credit_operation';
    }
}
