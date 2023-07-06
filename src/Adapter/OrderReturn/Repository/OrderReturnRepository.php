<?php
/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/OSL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to https://devdocs.prestashop.com/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\OrderReturn\Repository;

use Doctrine\DBAL\Connection;
use Exception;
use OrderReturn;
use PrestaShop\PrestaShop\Adapter\Order\Repository\OrderRepository;
use PrestaShop\PrestaShop\Adapter\OrderReturn\Validator\OrderReturnValidator;
use PrestaShop\PrestaShop\Core\Domain\Order\ValueObject\OrderId;
use PrestaShop\PrestaShop\Core\Domain\OrderReturn\Exception\DeleteOrderReturnProductException;
use PrestaShop\PrestaShop\Core\Domain\OrderReturn\Exception\OrderReturnDetailNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\OrderReturn\Exception\OrderReturnException;
use PrestaShop\PrestaShop\Core\Domain\OrderReturn\Exception\OrderReturnNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\OrderReturn\QueryResult\OrderReturnDetail;
use PrestaShop\PrestaShop\Core\Domain\OrderReturn\ValueObject\OrderReturnDetailId;
use PrestaShop\PrestaShop\Core\Domain\OrderReturn\ValueObject\OrderReturnId;
use PrestaShop\PrestaShop\Core\Domain\Product\Customization\ValueObject\CustomizationId;
use PrestaShop\PrestaShop\Core\Exception\CoreException;
use PrestaShop\PrestaShop\Core\Repository\AbstractObjectModelRepository;

class OrderReturnRepository extends AbstractObjectModelRepository
{
    /**
     * @var OrderReturnValidator
     */
    private $orderReturnValidator;

    /**
     * @var Connection
     */
    private $connection;

    /**
     * @var string
     */
    private $dbPrefix;

    /**
     * @var OrderRepository
     */
    private $orderRepository;

    /**
     * @param Connection $connection
     * @param string $dbPrefix
     * @param OrderReturnValidator $orderReturnValidator
     * @param OrderRepository $orderRepository
     */
    public function __construct(
        Connection $connection,
        string $dbPrefix,
        OrderReturnValidator $orderReturnValidator,
        OrderRepository $orderRepository
    ) {
        $this->orderReturnValidator = $orderReturnValidator;
        $this->connection = $connection;
        $this->dbPrefix = $dbPrefix;
        $this->orderRepository = $orderRepository;
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
     * @param OrderReturnDetailId $orderReturnDetailId
     *
     * @throws CoreException
     * @throws DeleteOrderReturnProductException
     * @throws OrderReturnDetailNotFoundException
     * @throws OrderReturnException
     * @throws \Doctrine\DBAL\Driver\Exception
     * @throws \Doctrine\DBAL\Exception
     */
    public function deleteOrderReturnProduct(
        OrderReturnDetailId $orderReturnDetailId
    ): void {
        $orderReturn = $this->get($orderReturnDetailId->getOrderReturnId());

        if ((int) ($orderReturn->countProduct()) <= 1) {
            throw new DeleteOrderReturnProductException(
                'Can\'t delete last product from merchandise return',
                DeleteOrderReturnProductException::LAST_ORDER_RETURN_PRODUCT
            );
        }
        $orderReturnDetail = $this->getOrderReturnDetailByOrderDetailId($orderReturnDetailId);

        $this->deleteOrderReturnDetail(
            $orderReturnDetailId,
            $orderReturnDetail->getCustomizationId()
        );
    }

    /**
     * @param OrderReturnDetailId $orderReturnDetailId
     * @param CustomizationId|null $customizationId
     *
     * @throws \Doctrine\DBAL\Exception
     */
    public function deleteOrderReturnDetail(
        OrderReturnDetailId $orderReturnDetailId,
        ?CustomizationId $customizationId = null
    ): void {
        $this->connection->delete(
            $this->dbPrefix . 'order_return_detail',
            [
                'id_order_detail' => $orderReturnDetailId->getOrderDetailId()->getValue(),
                'id_order_return' => $orderReturnDetailId->getOrderReturnId()->getValue(),
                'id_customization' => $customizationId ? $customizationId->getValue() : 0,
            ]
        );
    }

    /**
     * @param OrderReturnDetailId $orderReturnDetailId
     *
     * @return OrderReturnDetail
     *
     * @throws OrderReturnDetailNotFoundException
     * @throws \Doctrine\DBAL\Driver\Exception
     * @throws \Doctrine\DBAL\Exception
     */
    public function getOrderReturnDetailByOrderDetailId(OrderReturnDetailId $orderReturnDetailId): OrderReturnDetail
    {
        $result = $this->connection->createQueryBuilder()
            ->select('id_order_return, id_order_detail, id_customization, product_quantity')
            ->from($this->dbPrefix . 'order_return_detail')
            ->where('id_order_detail = :orderDetailId')
            ->where('id_order_return = :orderReturnId')
            ->setParameter('orderDetailId', $orderReturnDetailId->getOrderDetailId()->getValue())
            ->setParameter('orderReturnId', $orderReturnDetailId->getOrderReturnId()->getValue())
            ->execute()->fetchAssociative();
        if (!$result) {
            throw new OrderReturnDetailNotFoundException(
                sprintf(
                    'Order return detail with id "%s" not found',
                    $orderReturnDetailId->getOrderDetailId()->getValue()
                )
            );
        }

        return new OrderReturnDetail(
            (int) $result['id_order_return'],
            (int) $result['id_order_detail'],
            (int) $result['product_quantity'],
            (int) $result['id_customization'] ?: null
        );
    }

    /**
     * @param OrderReturnId $orderReturnId
     *
     * @return array<OrderReturnDetail>
     *
     * @throws OrderReturnException
     * @throws OrderReturnNotFoundException
     */
    public function getOrderReturnDetails(OrderReturnId $orderReturnId): array
    {
        $orderId = $this->getOrderId($orderReturnId);
        try {
            $order = $this->orderRepository->get(new OrderId($orderId));
            $details = OrderReturn::getOrdersReturnProducts($orderReturnId->getValue(), $order);
        } catch (Exception $e) {
            throw new OrderReturnException($e->getMessage());
        }

        $return = [];
        foreach ($details as $detail) {
            $return[$detail['id_order_detail']] = new OrderReturnDetail(
                $orderReturnId->getValue(),
                (int) $detail['id_order_detail'],
                (int) $detail['product_quantity'],
                (int) $detail['id_customization'] ?: null
            );
        }

        return $return;
    }

    public function getOrderId(OrderReturnId $orderReturnId): int
    {
        $result = $this->connection->createQueryBuilder()
            ->select('id_order')
            ->from($this->dbPrefix . 'order_return')
            ->where('id_order_return = :orderReturnId')
            ->setParameter('orderReturnId', $orderReturnId->getValue())
            ->execute()->fetchOne();
        if (!$result) {
            throw new OrderReturnNotFoundException(
                sprintf(
                    'Order return with id %d not found',
                    $orderReturnId->getValue()
                )
            );
        }

        return (int) $result;
    }
}
