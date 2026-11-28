<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\Order\Repository;

use Doctrine\DBAL\Connection;
use Order;
use PrestaShop\PrestaShop\Core\Domain\Cart\ValueObject\CartId;
use PrestaShop\PrestaShop\Core\Domain\Order\Exception\OrderException;
use PrestaShop\PrestaShop\Core\Domain\Order\Exception\OrderNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\Order\ValueObject\OrderId;
use PrestaShop\PrestaShop\Core\Exception\CoreException;
use PrestaShop\PrestaShop\Core\Repository\AbstractObjectModelRepository;
use PrestaShopException;

class OrderRepository extends AbstractObjectModelRepository
{
    public function __construct(
        private readonly Connection $connection,
        private string $dbPrefix
    ) {
    }

    /**
     * Gets legacy Order
     *
     * @param OrderId $orderId
     *
     * @return Order
     *
     * @throws OrderException
     * @throws CoreException
     */
    public function get(OrderId $orderId): Order
    {
        try {
            $order = new Order($orderId->getValue());

            if ($order->id !== $orderId->getValue()) {
                throw new OrderNotFoundException($orderId, sprintf('%s #%d was not found', Order::class, $orderId->getValue()));
            }
        } catch (PrestaShopException $e) {
            throw new CoreException(
                sprintf(
                    'Error occurred when trying to get %s #%d [%s]',
                    Order::class,
                    $orderId->getValue(),
                    $e->getMessage()
                ),
                0,
                $e
            );
        }

        return $order;
    }

    /**
     * Get Order by cartId
     *
     * @param CartId $cartId
     *
     * @return Order
     *
     * @throws CoreException
     * @throws OrderException
     * @throws OrderNotFoundException
     * @throws \Doctrine\DBAL\Exception
     */
    public function getByCartId(CartId $cartId): Order
    {
        $orderId = $this->connection->createQueryBuilder()
            ->select('id_order')
            ->from($this->dbPrefix . 'orders')
            ->where('id_cart = :cartId')
            ->setParameter('cartId', $cartId->getValue())
            ->execute()
            ->fetchOne();

        if ($orderId) {
            return $this->get(new OrderId((int) $orderId));
        }

        throw new OrderNotFoundException(message: sprintf('Order for cart #%d was not found', $cartId->getValue()));
    }

    /**
     * Prices are displayed tax included or excluded according to the default group of the order customer.
     *
     * @return array{currency_id: int, is_tax_included: bool}
     *
     * @throws OrderNotFoundException
     */
    public function getPriceDisplaySettings(OrderId $orderId): array
    {
        $row = $this->connection->createQueryBuilder()
            ->select('o.id_currency', 'g.price_display_method')
            ->from($this->dbPrefix . 'orders', 'o')
            ->leftJoin('o', $this->dbPrefix . 'customer', 'c', 'c.id_customer = o.id_customer')
            ->leftJoin('c', '`' . $this->dbPrefix . 'group`', 'g', 'g.id_group = c.id_default_group')
            ->where('o.id_order = :orderId')
            ->setParameter('orderId', $orderId->getValue())
            ->setMaxResults(1)
            ->executeQuery()
            ->fetchAssociative();

        if (false === $row) {
            throw new OrderNotFoundException($orderId, sprintf('Order #%d was not found', $orderId->getValue()));
        }

        return [
            'currency_id' => (int) $row['id_currency'],
            'is_tax_included' => (int) $row['price_display_method'] === PS_TAX_INC,
        ];
    }
}
