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
use PrestaShop\PrestaShop\Core\Domain\Product\Customization\ValueObject\CustomizationId;
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

    /**
     * An order can hold several order details for the same product and combination, they are then only
     * distinguished by their customization (and, for multi invoice orders, by their invoice). So all three
     * identifiers must be part of the criteria, else an arbitrary row would be returned.
     *
     * When several rows still match (same product, combination and customization in different invoices) the
     * most recently created one is returned, as it is the one a product addition has just generated.
     *
     * @param OrderId $orderId
     * @param ProductId $productId
     * @param CombinationId|null $combinationId Null is equivalent to "no combination"
     * @param CustomizationId|null $customizationId Null is equivalent to "no customization"
     *
     * @return OrderDetail|null
     *
     * @throws CoreException
     * @throws PrestaShopException
     */
    public function findByOrderIdAndProductId(
        OrderId $orderId,
        ProductId $productId,
        ?CombinationId $combinationId,
        ?CustomizationId $customizationId = null
    ): ?OrderDetail {
        if (!$this->connection) {
            trigger_deprecation('prestashop/prestashop', '9.2', 'Connection must be set.');
            throw new PrestaShopException('Connection must be set for OrderDetailRepository.');
        }

        $qb = $this->connection->createQueryBuilder();

        $orderDetailId = $qb
            ->select('id_order_detail')
            ->from($this->dbPrefix . 'order_detail')
            ->where('id_order = :orderId')
            ->andWhere('product_id = :productId')
            ->andWhere('product_attribute_id = :combinationId')
            ->andWhere('id_customization = :customizationId')
            ->orderBy('id_order_detail', 'DESC')
            ->setMaxResults(1)
            ->setParameter('orderId', $orderId->getValue())
            ->setParameter('productId', $productId->getValue())
            ->setParameter('combinationId', null !== $combinationId ? $combinationId->getValue() : 0)
            ->setParameter('customizationId', null !== $customizationId ? $customizationId->getValue() : 0)
            ->executeQuery()
            ->fetchOne();

        if ($orderDetailId === false) {
            return null;
        }

        return $this->get(new OrderDetailId((int) $orderDetailId));
    }
}
