<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\OrderReturn\Repository;

use Db;
use Order;
use OrderReturn;
use PrestaShop\PrestaShop\Adapter\OrderReturn\Validator\OrderReturnValidator;
use PrestaShop\PrestaShop\Core\Domain\OrderReturn\Exception\DeleteProductFromOrderReturnException;
use PrestaShop\PrestaShop\Core\Domain\OrderReturn\Exception\OrderReturnException;
use PrestaShop\PrestaShop\Core\Domain\OrderReturn\Exception\OrderReturnNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\OrderReturn\ValueObject\OrderReturnId;
use PrestaShop\PrestaShop\Core\Domain\OrderReturn\ValueObject\OrderReturnProductId;
use PrestaShop\PrestaShop\Core\Exception\CoreException;
use PrestaShop\PrestaShop\Core\Repository\AbstractObjectModelRepository;

class OrderReturnRepository extends AbstractObjectModelRepository
{
    /**
     * @var OrderReturnValidator
     */
    private $orderReturnValidator;

    /**
     * @param OrderReturnValidator $orderReturnValidator
     */
    public function __construct(OrderReturnValidator $orderReturnValidator)
    {
        $this->orderReturnValidator = $orderReturnValidator;
    }

    /**
     * Gets legacy OrderReturn
     *
     * @param OrderReturnId $orderReturnId
     *
     * @return OrderReturn
     *
     * @throws OrderReturnException
     * @throws CoreException
     */
    public function get(OrderReturnId $orderReturnId): OrderReturn
    {
        /** @var OrderReturn $orderReturn */
        $orderReturn = $this->getObjectModel(
            $orderReturnId->getValue(),
            OrderReturn::class,
            OrderReturnNotFoundException::class
        );

        return $orderReturn;
    }

    /**
     * @param OrderReturn $orderReturn
     *
     * @throws CoreException
     */
    public function update(OrderReturn $orderReturn): void
    {
        $this->orderReturnValidator->validate($orderReturn);
        $this->updateObjectModel(
            $orderReturn,
            OrderReturnException::class
        );
    }

    /**
     * Deletes the merchandise return and its `order_return_detail` rows. The legacy
     * ObjectModel::delete() does not cascade to `order_return_detail`, so we wipe the
     * detail rows manually first to avoid orphans.
     *
     * @throws OrderReturnException when the ObjectModel exists but delete() returns false
     */
    public function delete(OrderReturnId $orderReturnId): void
    {
        $orderReturn = $this->get($orderReturnId);

        Db::getInstance()->delete(
            'order_return_detail',
            'id_order_return = ' . (int) $orderReturnId->getValue()
        );

        $this->deleteObjectModel($orderReturn, OrderReturnException::class);
    }

    /**
     * Lists classic (non-customized) products attached to a return.
     *
     * Each row is the legacy associative array returned by Order::getProducts() augmented with
     * 'product_quantity' (sum of returned qty for that order line) and 'customizations'
     * (last id_customization seen — 0 when none). Keys match the indexes used by Order::getProducts().
     *
     * @return array<int, array<string, mixed>>
     */
    public function getProductsForReturn(OrderReturnId $orderReturnId): array
    {
        $orderReturn = $this->get($orderReturnId);
        $order = new Order((int) $orderReturn->id_order);

        return OrderReturn::getOrdersReturnProducts($orderReturnId->getValue(), $order);
    }

    /**
     * Counts product lines (rows in `order_return_detail`) attached to a return.
     */
    public function countProductLines(OrderReturnId $orderReturnId): int
    {
        return (int) $this->get($orderReturnId)->countProduct();
    }

    /**
     * Removes a single row from `order_return_detail`.
     *
     * @throws DeleteProductFromOrderReturnException
     */
    public function deleteProductLine(OrderReturnId $orderReturnId, OrderReturnProductId $productId): void
    {
        $deleted = OrderReturn::deleteOrderReturnDetail(
            $orderReturnId->getValue(),
            $productId->getOrderDetailId(),
            $productId->getCustomizationId()
        );

        if (!$deleted) {
            throw new DeleteProductFromOrderReturnException(sprintf(
                'Failed to delete product line (orderReturn=%d, orderDetail=%d, customization=%d).',
                $orderReturnId->getValue(),
                $productId->getOrderDetailId(),
                $productId->getCustomizationId()
            ));
        }
    }

    /**
     * Lists customized products attached to a return.
     *
     * Returns the legacy associative arrays produced by OrderReturn::getReturnedCustomizedProducts(),
     * each enriched with product_id, product_attribute_id, name, reference, id_address_delivery.
     *
     * @return array<int, array<string, mixed>>
     */
    public function getCustomizedProductsForReturn(OrderReturnId $orderReturnId): array
    {
        $orderReturn = $this->get($orderReturnId);

        return OrderReturn::getReturnedCustomizedProducts((int) $orderReturn->id_order);
    }
}
