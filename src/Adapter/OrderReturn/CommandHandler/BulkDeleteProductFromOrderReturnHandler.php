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

namespace PrestaShop\PrestaShop\Adapter\OrderReturn\CommandHandler;

use Order;
use OrderReturn;
use PrestaShop\PrestaShop\Adapter\OrderReturn\AbstractOrderReturnHandler;
use PrestaShop\PrestaShop\Core\Domain\OrderReturn\Command\BulkDeleteProductFromOrderReturnCommand;
use PrestaShop\PrestaShop\Core\Domain\OrderReturn\CommandHandler\BulkDeleteProductFromOrderReturnHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\OrderReturn\Exception\BulkDeleteOrderReturnProductException;
use PrestaShop\PrestaShop\Core\Domain\OrderReturn\Exception\OrderReturnException;

class BulkDeleteProductFromOrderReturnHandler extends AbstractOrderReturnHandler implements BulkDeleteProductFromOrderReturnHandlerInterface
{
    /**
     * {@inheritdoc}
     */
    public function handle(BulkDeleteProductFromOrderReturnCommand $command): void
    {
        $errors = [];

        $orderReturn = new OrderReturn($command->getOrderReturnId()->getValue());
        $order = new Order($orderReturn->id_order);
        $details = OrderReturn::getOrdersReturnProducts($command->getOrderReturnId()->getValue(), $order);

        /* Check if products exist in order return */
        foreach ($command->getOrderReturnDetailIds() as $orderReturnDetailId) {
            if (isset($details[$orderReturnDetailId->getValue()])) {
                unset($details[$orderReturnDetailId->getValue()]);
            } else {
                $errors[] = $orderReturnDetailId->getValue();
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

        foreach ($command->getOrderReturnDetailIds() as $orderReturnDetailId) {
            try {
                $this->deleteOrderReturnProduct(
                    $command->getOrderReturnId(),
                    $orderReturnDetailId
                );
            } catch (OrderReturnException $e) {
                $errors[] = $orderReturnDetailId->getValue();
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
}
