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
use PrestaShop\PrestaShop\Core\Domain\OrderReturn\Exception\OrderReturnProductNotFoundException;
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
     * Lists the product lines of a return (rows of `order_return_detail` for this return only),
     * customized lines first, each with the order line reference/name and, for customized lines,
     * the customer-provided data (`customized_data` rows: type + value).
     *
     * @return array<int, array{id_order_detail: int, id_customization: int, product_quantity: int, product_reference: string, product_name: string, customized_data: array<int, array{type: int, value: string}>}>
     *
     * @throws OrderReturnNotFoundException
     */
    public function getProductLines(OrderReturnId $orderReturnId): array
    {
        $this->get($orderReturnId);

        $rows = Db::getInstance()->executeS(sprintf(
            'SELECT ord.`id_order_detail`, ord.`id_customization`, ord.`product_quantity`, od.`product_reference`, od.`product_name`
            FROM `%1$sorder_return_detail` ord
            INNER JOIN `%1$sorder_detail` od ON od.`id_order_detail` = ord.`id_order_detail`
            WHERE ord.`id_order_return` = %2$d
            ORDER BY ord.`id_customization` = 0, ord.`id_order_detail`, ord.`id_customization`',
            _DB_PREFIX_,
            $orderReturnId->getValue()
        )) ?: [];

        $customizationIds = array_filter(array_map(static fn (array $row): int => (int) $row['id_customization'], $rows));
        $customizedData = [];
        if (!empty($customizationIds)) {
            $dataRows = Db::getInstance()->executeS(sprintf(
                'SELECT `id_customization`, `type`, `value` FROM `%scustomized_data`
                WHERE `id_customization` IN (%s)
                ORDER BY `id_customization`, `type`, `index`',
                _DB_PREFIX_,
                implode(',', $customizationIds)
            )) ?: [];
            foreach ($dataRows as $dataRow) {
                $customizedData[(int) $dataRow['id_customization']][] = [
                    'type' => (int) $dataRow['type'],
                    'value' => (string) $dataRow['value'],
                ];
            }
        }

        return array_map(static fn (array $row): array => [
            'id_order_detail' => (int) $row['id_order_detail'],
            'id_customization' => (int) $row['id_customization'],
            'product_quantity' => (int) $row['product_quantity'],
            'product_reference' => (string) $row['product_reference'],
            'product_name' => (string) $row['product_name'],
            'customized_data' => $customizedData[(int) $row['id_customization']] ?? [],
        ], $rows);
    }

    /**
     * Lists classic (non-customized) products attached to a return.
     *
     * Each row is the legacy associative array returned by Order::getProducts() augmented with
     * 'product_quantity' (sum of returned qty for that order line) and 'customizations'
     * (last id_customization seen — 0 when none). Keys match the indexes used by Order::getProducts().
     *
     * @deprecated since 9.2.1, use getProductLines() instead: it merges customized lines into the classic rows
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
     * @throws OrderReturnProductNotFoundException when the line is not part of the order return
     * @throws DeleteProductFromOrderReturnException
     */
    public function deleteProductLine(OrderReturnId $orderReturnId, OrderReturnProductId $productId): void
    {
        // The legacy delete succeeds when no row matches: check the line is part of the return first
        $lineExists = (bool) Db::getInstance()->getValue(sprintf(
            'SELECT 1 FROM `%sorder_return_detail` WHERE `id_order_return` = %d AND `id_order_detail` = %d AND `id_customization` = %d',
            _DB_PREFIX_,
            $orderReturnId->getValue(),
            $productId->getOrderDetailId(),
            $productId->getCustomizationId()
        ));
        if (!$lineExists) {
            throw new OrderReturnProductNotFoundException(sprintf(
                'Order detail %d (customization %d) is not part of order return %d.',
                $productId->getOrderDetailId(),
                $productId->getCustomizationId(),
                $orderReturnId->getValue()
            ));
        }

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
     * @deprecated since 9.2.1, use getProductLines() instead: it lists the customized lines of every return of the order
     *
     * @return array<int, array<string, mixed>>
     */
    public function getCustomizedProductsForReturn(OrderReturnId $orderReturnId): array
    {
        $orderReturn = $this->get($orderReturnId);

        return OrderReturn::getReturnedCustomizedProducts((int) $orderReturn->id_order);
    }
}
