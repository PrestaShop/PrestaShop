<?php
/**
 * 2007-2020 PrestaShop SA and Contributors
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
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
 * needs please refer to https://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2020 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\OrderReturn\CommandHandler;

use OrderReturn;
use PrestaShop\PrestaShop\Adapter\OrderReturn\AbstractOrderReturnHandler;
use PrestaShop\PrestaShop\Core\Domain\OrderReturn\Command\UpdateOrderReturnStateCommand;
use PrestaShop\PrestaShop\Core\Domain\OrderReturn\CommandHandler\UpdateOrderReturnStateHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\OrderReturn\Exception\OrderReturnException;

class UpdateOrderReturnStateHandler extends AbstractOrderReturnHandler implements UpdateOrderReturnStateHandlerInterface
{
    /**
     * {@inheritdoc}
     */
    public function handle(UpdateOrderReturnStateCommand $command): void
    {
        $orderReturnId = $command->getOrderReturnId();

        $orderReturn = $this->getOrderReturn($orderReturnId);

        $orderReturn = $this->updateOrderReturnWithCommandData($orderReturn, $command);

        $this->assertRequiredFieldsAreNotMissing($orderReturn);

        if (false === $orderReturn->validateFields(false)) {
            throw new OrderReturnException('Order return contains invalid field values');
        }

        if (false === $orderReturn->update()) {
            throw new OrderReturnException('Failed to update order return');
        }
    }

    /**
     * @param OrderReturn $orderReturn
     * @param UpdateOrderReturnStateCommand $command
     *
     * @return OrderReturn
     *
     * @throws OrderReturnException
     * @throws \PrestaShop\PrestaShop\Core\Domain\OrderReturn\Exception\OrderReturnOrderStateConstraintException
     */
    private function updateOrderReturnWithCommandData(OrderReturn $orderReturn, UpdateOrderReturnStateCommand $command): OrderReturn
    {
        /** getOrderReturnState will throw error in case this state does not exist */
        $orderReturnState = $this->getOrderReturnState($command->getOrderReturnStateId());
        $orderReturn->state = $orderReturnState->id;

        return $orderReturn;
    }
}
