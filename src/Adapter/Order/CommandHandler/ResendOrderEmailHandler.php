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

namespace PrestaShop\PrestaShop\Adapter\Order\CommandHandler;

use Carrier;
use Configuration;
use OrderHistory;
use OrderState;
use PrestaShop\PrestaShop\Core\Domain\Order\Command\ResendOrderEmailCommand;
use PrestaShop\PrestaShop\Core\Domain\Order\CommandHandler\ResendOrderEmailHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Order\Exception\OrderEmailSendException;
use PrestaShop\PrestaShop\Core\Domain\Order\Exception\OrderException;
use Validate;

/**
 * @internal
 */
final class ResendOrderEmailHandler extends AbstractOrderCommandHandler implements ResendOrderEmailHandlerInterface
{
    /**
     * @param ResendOrderEmailCommand $command
     */
    public function handle(ResendOrderEmailCommand $command): void
    {
        $order = $this->getOrder($command->getOrderId());
        $orderState = new OrderState($command->getOrderStatusId());

        if (!Validate::isLoadedObject($orderState)) {
            throw new OrderException(sprintf('An error occurred while loading order status. Order status with "%s" was not found.', $command->getOrderId()->getValue()));
        }

        $history = new OrderHistory($command->getOrderHistoryId());

        $carrier = new Carrier($order->id_carrier, (int) $order->getAssociatedLanguage()->getId());
        $templateVars = [];

        if ($orderState->id == Configuration::get('PS_OS_SHIPPING') && $order->getShippingNumber()) {
            $templateVars = ['{followup}' => str_replace('@', $order->getShippingNumber(), $carrier->url)];
        }

        if (!$history->sendEmail($order, $templateVars)) {
            throw new OrderEmailSendException('Failed to resend order email.', OrderEmailSendException::FAILED_RESEND);
        }
    }
}
