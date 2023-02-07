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
use Order;
use OrderReturn;
use PrestaShop\PrestaShop\Adapter\OrderReturn\Validator\OrderReturnValidator;
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
use PrestaShopException;

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
     * @param OrderReturnValidator $orderReturnValidator
     */
    public function __construct(
        Connection $connection,
        string $dbPrefix,
        OrderReturnValidator $orderReturnValidator
    ) {
        $this->orderReturnValidator = $orderReturnValidator;
        $this->connection = $connection;
        $this->dbPrefix = $dbPrefix;
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
     * @param OrderReturnId $orderReturnId
     * @param OrderReturnDetailId $orderReturnDetailId
     *
     * @throws CoreException
     * @throws DeleteOrderReturnProductException
     * @throws OrderReturnException
     */
    public function deleteOrderReturnProduct(
        OrderReturnId $orderReturnId,
        OrderReturnDetailId $orderReturnDetailId
    ): void {
        $orderReturn = $this->get($orderReturnId);

        if ((int) ($orderReturn->countProduct()) <= 1) {
            throw new DeleteOrderReturnProductException(
                'Can\'t delete last product from merchandise return',
                DeleteOrderReturnProductException::LAST_ORDER_RETURN_PRODUCT
            );
        }
        $orderReturnDetail = $this->getOrderReturnDetailByOrderDetailId($orderReturnDetailId->getValue());

        $this->deleteOrderReturnDetail(
            $orderReturnDetail->getOrderReturnId(),
            $orderReturnDetailId,
            $orderReturnDetail->getCustomizationId()
        );
    }

    /**
     * @param OrderReturnId $orderReturnId
     * @param OrderReturnDetailId $orderReturnDetailId
     * @param CustomizationId|null $customizationId
     *
     * @throws DeleteOrderReturnProductException
     */
    public function deleteOrderReturnDetail(
        OrderReturnId $orderReturnId,
        OrderReturnDetailId $orderReturnDetailId,
        ?CustomizationId $customizationId = null
    ): void {
        try {
            if (!OrderReturn::deleteOrderReturnDetail(
                $orderReturnId->getValue(),
                $orderReturnDetailId->getValue(),
                $customizationId ? $customizationId->getValue() : 0
            )) {
                throw new DeleteOrderReturnProductException(
                    'Failed to delete merchandise return detail',
                    DeleteOrderReturnProductException::UNEXPECTED_ERROR
                );
            }
        } catch (PrestaShopException $e) {
            throw new DeleteOrderReturnProductException(
                'Failed to delete merchandise return detail',
                DeleteOrderReturnProductException::UNEXPECTED_ERROR
            );
        }
    }

    /**
     * @param int $orderDetailId
     *
     * @return OrderReturnDetail
     *
     * @throws OrderReturnDetailNotFoundException
     * @throws \Doctrine\DBAL\Driver\Exception
     * @throws \Doctrine\DBAL\Exception
     */
    public function getOrderReturnDetailByOrderDetailId(int $orderDetailId): OrderReturnDetail
    {
        $result = $this->connection->createQueryBuilder()
            ->select('id_order_return, id_order_detail, id_customization, product_quantity')
            ->from($this->dbPrefix . 'order_return_detail')
            ->where('id_order_detail = :orderDetailId')
            ->setParameter('orderDetailId', $orderDetailId)
            ->execute()->fetchAssociative();
        if (!$result) {
            throw new OrderReturnDetailNotFoundException(sprintf('Order return detail with id "%s" not found', $orderDetailId));
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
     * @param Order $order
     *
     * @return array<OrderReturnDetail>
     *
     * @throws OrderReturnException
     */
    public function getOrderReturnDetails(OrderReturnId $orderReturnId, Order $order): array
    {
        try {
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
}
