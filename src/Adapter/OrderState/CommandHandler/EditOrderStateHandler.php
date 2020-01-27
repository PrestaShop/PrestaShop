<?php
/**
 * 2007-2019 PrestaShop SA and Contributors
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
 * @copyright 2007-2019 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShop\PrestaShop\Adapter\OrderState\CommandHandler;

use OrderState;
use PrestaShop\PrestaShop\Core\Domain\OrderState\Command\EditOrderStateCommand;
use PrestaShop\PrestaShop\Core\Domain\OrderState\CommandHandler\EditOrderStateHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\OrderState\Exception\OrderStateException;

/**
 * Handles commands which edits given order state with provided data.
 *
 * @internal
 */
final class EditOrderStateHandler extends AbstractOrderStateHandler implements EditOrderStateHandlerInterface
{
    /**
     * {@inheritdoc}
     */
    public function handle(EditOrderStateCommand $command)
    {
        $orderStateId = $command->getOrderStateId();
        $orderState = new OrderState($orderStateId->getValue());

        $this->assertOrderStateWasFound($orderStateId, $orderState);

        $this->updateOrderStateWithCommandData($orderState, $command);

        $this->assertRequiredFieldsAreNotMissing($orderState);

        if (false === $orderState->validateFields(false)) {
            throw new OrderStateException('OrderState contains invalid field values');
        }

        if (false === $orderState->update()) {
            throw new OrderStateException('Failed to update order state');
        }
    }

    private function updateOrderStateWithCommandData(OrderState $orderState, EditOrderStateCommand $command)
    {
        if (null !== $command->getName()) {
            $orderState->name = $command->getName();
        }

        if (null !== $command->getColor()) {
            $orderState->color = $command->getColor();
        }

        if (null !== $command->isLogable()) {
            $orderState->logable = $command->isLogable();
        }

        if (null !== $command->isHiddenOn()) {
            $orderState->hidden = $command->isHiddenOn();
        }

        if (null !== $command->isInvoiceOn()) {
            $orderState->invoice = $command->isInvoiceOn();
        }

        if (null !== $command->isSendEmailOn()) {
            $orderState->send_email = $command->isSendEmailOn();
        }

        if (null !== $command->isPdfInvoiceOn()) {
            $orderState->pdf_invoice = $command->isPdfInvoiceOn();
        }

        if (null !== $command->isPdfDeliveryOn()) {
            $orderState->pdf_delivery = $command->isPdfDeliveryOn();
        }

        if (null !== $command->isShippedOn()) {
            $orderState->shipped = $command->isShippedOn();
        }

        if (null !== $command->isPaidOn()) {
            $orderState->paid = $command->isPaidOn();
        }

        if (null !== $command->isDeliveryOn()) {
            $orderState->delivery = $command->isDeliveryOn();
        }

        if (null !== $command->getTemplate()) {
            $orderState->template = $command->getTemplate();
        }
    }
}
