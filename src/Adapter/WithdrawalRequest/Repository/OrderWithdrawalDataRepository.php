<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\WithdrawalRequest\Repository;

use DateTimeImmutable;
use Doctrine\DBAL\Connection;
use PrestaShop\PrestaShop\Core\Domain\Order\Exception\OrderNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\Order\ValueObject\OrderId;

/**
 * Reads the order data the withdrawal function relies on: who placed the order, in which shop, its current
 * state and when it was delivered.
 */
final class OrderWithdrawalDataRepository
{
    public function __construct(
        private readonly Connection $connection,
        private readonly string $dbPrefix,
    ) {
    }

    /**
     * @return array{id_order: int, id_shop: int, id_customer: int, id_lang: int, reference: string, current_state: int, customer_email: string}
     *
     * @throws OrderNotFoundException
     */
    public function getOrderData(OrderId $orderId): array
    {
        $row = $this->connection->createQueryBuilder()
            ->select('o.id_order, o.id_shop, o.id_customer, o.id_lang, o.reference, o.current_state, c.email AS customer_email')
            ->from($this->dbPrefix . 'orders', 'o')
            ->leftJoin('o', $this->dbPrefix . 'customer', 'c', 'c.id_customer = o.id_customer')
            ->where('o.id_order = :orderId')
            ->setParameter('orderId', $orderId->getValue())
            ->executeQuery()
            ->fetchAssociative();

        if (false === $row) {
            throw new OrderNotFoundException($orderId, sprintf('Order #%d was not found', $orderId->getValue()));
        }

        return [
            'id_order' => (int) $row['id_order'],
            'id_shop' => (int) $row['id_shop'],
            'id_customer' => (int) $row['id_customer'],
            'id_lang' => (int) $row['id_lang'],
            'reference' => (string) $row['reference'],
            'current_state' => (int) $row['current_state'],
            'customer_email' => (string) $row['customer_email'],
        ];
    }

    /**
     * Date the order first reached the delivered state, null when it has not been delivered yet.
     *
     * The "delivery" flag of order states cannot be used: it is also set on "Processing in progress" and
     * "Shipped", which would start the withdrawal period before the consumer receives the goods.
     */
    public function findDeliveryDate(OrderId $orderId, int $deliveredOrderStateId): ?DateTimeImmutable
    {
        $deliveryDate = $this->connection->createQueryBuilder()
            ->select('MIN(oh.date_add)')
            ->from($this->dbPrefix . 'order_history', 'oh')
            ->where('oh.id_order = :orderId')
            ->andWhere('oh.id_order_state = :deliveredOrderStateId')
            ->setParameter('orderId', $orderId->getValue())
            ->setParameter('deliveredOrderStateId', $deliveredOrderStateId)
            ->executeQuery()
            ->fetchOne();

        if (false === $deliveryDate || null === $deliveryDate) {
            return null;
        }

        return new DateTimeImmutable((string) $deliveryDate);
    }
}
