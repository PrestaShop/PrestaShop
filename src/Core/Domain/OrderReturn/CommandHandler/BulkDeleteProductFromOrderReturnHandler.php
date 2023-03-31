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

namespace PrestaShop\PrestaShop\Core\Domain\OrderReturn\CommandHandler;

use PrestaShop\PrestaShop\Adapter\Order\Repository\OrderRepository;
use PrestaShop\PrestaShop\Adapter\OrderReturn\Repository\OrderReturnRepository;
use PrestaShop\PrestaShop\Core\Domain\Order\Exception\OrderException;
use PrestaShop\PrestaShop\Core\Domain\Order\ValueObject\OrderId;
use PrestaShop\PrestaShop\Core\Domain\OrderReturn\Command\BulkDeleteProductFromOrderReturnCommand;
use PrestaShop\PrestaShop\Core\Domain\OrderReturn\Exception\BulkDeleteOrderReturnProductException;
use PrestaShop\PrestaShop\Core\Domain\OrderReturn\Exception\OrderReturnException;
use PrestaShop\PrestaShop\Core\Domain\OrderReturn\ValueObject\OrderReturnDetailId;
use PrestaShop\PrestaShop\Core\Domain\OrderReturn\ValueObject\OrderReturnId;
use PrestaShop\PrestaShop\Core\Exception\CoreException;

class BulkDeleteProductFromOrderReturnHandler implements BulkDeleteProductFromOrderReturnHandlerInterface
{
    /**
     * @var OrderReturnRepository
     */
    private $orderReturnRepository;

    public function __construct(OrderReturnRepository $orderReturnRepository) {
        $this->orderReturnRepository = $orderReturnRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function handle(BulkDeleteProductFromOrderReturnCommand $command): void
    {
        $this->validate($command->getOrderReturnId(), $command->getOrderReturnDetailIds());
        foreach ($command->getOrderReturnDetailIds() as $orderReturnDetailId) {
            try {
                $this->orderReturnRepository->deleteOrderReturnProduct(
                    $orderReturnDetailId
                );
            } catch (OrderReturnException $e) {
                $errors[] = $orderReturnDetailId->getOrderDetailId()->getValue();
            }
        }

        if (!empty($errors)) {
            throw new BulkDeleteOrderReturnProductException(
                $errors,
                'Failed to delete some of merchandise return products',
                BulkDeleteOrderReturnProductException::UNEXPECTED_ERROR
            );
        }
    }

    /**
     * @param OrderReturnId $orderReturnId
     * @param array<OrderReturnDetailId> $orderReturnDetailIds
     *
     * @throws BulkDeleteOrderReturnProductException
     * @throws OrderReturnException
     * @throws CoreException
     */
    private function validate(OrderReturnId $orderReturnId, array $orderReturnDetailIds): void
    {
        $errors = [];
        $details = $this->orderReturnRepository->getOrderReturnDetails($orderReturnId);

        /* Check if products exist in order return */
        foreach ($orderReturnDetailIds as $orderReturnDetailId) {
            if (isset($details[$orderReturnDetailId->getOrderDetailId()->getValue()])) {
                unset($details[$orderReturnDetailId->getOrderDetailId()->getValue()]);
            } else {
                $errors[] = $orderReturnDetailId->getOrderDetailId()->getValue();
            }
        }

        if (!empty($errors)) {
            throw new BulkDeleteOrderReturnProductException(
                $errors,
                'Some order details don\'t exist in order return',
                BulkDeleteOrderReturnProductException::CANT_DELETE_PRODUCT_NOT_PART_OF_ORDER_RETURN
            );
        }

        /* If there would be no details left after delete then order return would invalid. */
        if (empty($details)) {
            throw new BulkDeleteOrderReturnProductException(
                [],
                'Order return must have at least one product left',
                BulkDeleteOrderReturnProductException::CANT_DELETE_ALL_PRODUCTS
            );
        }
    }
}
