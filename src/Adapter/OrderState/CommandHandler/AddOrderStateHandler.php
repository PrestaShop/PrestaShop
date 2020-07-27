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

namespace PrestaShop\PrestaShop\Adapter\OrderState\CommandHandler;

use OrderState;
use PrestaShop\PrestaShop\Core\Domain\OrderState\Command\AddOrderStateCommand;
use PrestaShop\PrestaShop\Core\Domain\OrderState\CommandHandler\AddOrderStateHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\OrderState\Exception\OrderStateException;
use PrestaShop\PrestaShop\Core\Domain\OrderState\ValueObject\OrderStateId;

/**
 * Handles command that adds new order state
 *
 * @internal
 */
final class AddOrderStateHandler extends AbstractOrderStateHandler implements AddOrderStateHandlerInterface
{
    /**
     * {@inheritdoc}
     */
    public function handle(AddOrderStateCommand $command)
    {
        $orderState = new OrderState();

        $this->fillOrderStateWithCommandData($orderState, $command);
        $this->assertRequiredFieldsAreNotMissing($orderState);

        if (false === $orderState->validateFields(false)) {
            throw new OrderStateException('Order status contains invalid field values');
        }

        $orderState->add();

        return new OrderStateId((int) $orderState->id);
    }

    private function fillOrderStateWithCommandData(OrderState $orderState, AddOrderStateCommand $command)
    {
        $orderState->name = $command->getLocalizedNames();
        $orderState->color = $command->getColor();
        $orderState->logable = $command->isLoggable();
        $orderState->invoice = $command->isInvoice();
        $orderState->hidden = $command->isHidden();
        $orderState->send_email = $command->isSendEmailEnabled();
        $orderState->pdf_invoice = $command->isPdfInvoice();
        $orderState->pdf_delivery = $command->isPdfDelivery();
        $orderState->shipped = $command->isShipped();
        $orderState->paid = $command->isPaid();
        $orderState->delivery = $command->isDelivery();
        $orderState->template = $command->getLocalizedTemplates();
    }
}
