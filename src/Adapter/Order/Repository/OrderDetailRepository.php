<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Adapter\Order\Repository;

use Doctrine\DBAL\Connection;
use OrderDetail;
use PrestaShop\PrestaShop\Core\Domain\Order\Exception\OrderDetailNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\Order\ValueObject\OrderId;
use PrestaShop\PrestaShop\Core\Domain\Product\Combination\ValueObject\CombinationId;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\ProductId;
use PrestaShop\PrestaShop\Core\Domain\Shipment\ValueObject\OrderDetailId;
use PrestaShop\PrestaShop\Core\Exception\CoreException;
use PrestaShop\PrestaShop\Core\Repository\AbstractObjectModelRepository;
use PrestaShopException;

class OrderDetailRepository extends AbstractObjectModelRepository
{
    public function __construct(
        private readonly ?Connection $connection = null,
        private ?string $dbPrefix = null
    ) {
    }

    /**
     * Gets legacy Order detail
     *
     * @param OrderDetailId $orderDetailId
     *
     * @return OrderDetail
     *
     * @throws CoreException
     */
    public function get(OrderDetailId $orderDetailId): OrderDetail
    {
        /** @var OrderDetail $orderDetail */
        $orderDetail = $this->getObjectModel(
            $orderDetailId->getValue(),
            OrderDetail::class,
            OrderDetailNotFoundException::class
        );

        return $orderDetail;
    }

    public function findByOrderIdAndProductId(
        OrderId $orderId,
        ProductId $productId,
        ?CombinationId $combinationId
    ): ?OrderDetail {
        if (!$this->connection) {
            trigger_deprecation('prestashop/prestashop', '9.2', 'Connection must be set.');
            throw new PrestaShopException('Connection must be set for OrderDetailRepository.');
        }

        $qb = $this->connection->createQueryBuilder();

        $qb
            ->select('id_order_detail')
            ->from($this->dbPrefix . 'order_detail')
            ->where('id_order = :orderId')
            ->andWhere('product_id = :productId')
            ->setParameter('orderId', $orderId->getValue())
            ->setParameter('productId', $productId->getValue());

        if ($combinationId !== null) {
            $qb
                ->andWhere('product_attribute_id = :combinationId')
                ->setParameter('combinationId', $combinationId->getValue());
        }

        $orderDetailId = $qb
            ->execute()
            ->fetchOne();

        if ($orderDetailId === false) {
            return null;
        }

        return $this->get(new OrderDetailId((int) $orderDetailId));
    }
}
