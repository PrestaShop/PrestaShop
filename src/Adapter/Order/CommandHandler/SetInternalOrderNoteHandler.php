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

namespace PrestaShop\PrestaShop\Adapter\Order\CommandHandler;

use Order;
use PrestaShop\PrestaShop\Adapter\Order\AbstractOrderHandler;
use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsCommandHandler;
use PrestaShop\PrestaShop\Core\Domain\Order\Command\SetInternalOrderNoteCommand;
use PrestaShop\PrestaShop\Core\Domain\Order\CommandHandler\SetInternalOrderNoteHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Order\Exception\OrderConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Order\Exception\OrderException;
use PrestaShop\PrestaShop\Core\Domain\Order\Exception\OrderNotFoundException;

/**
 * Handles command that saves internal order note.
 *
 * @internal
 */
#[AsCommandHandler]
final class SetInternalOrderNoteHandler extends AbstractOrderHandler implements SetInternalOrderNoteHandlerInterface
{
    /**
     * @param SetInternalOrderNoteCommand $command
     *
     * @throws OrderNotFoundException
     */
    public function handle(SetInternalOrderNoteCommand $command)
    {
        $order = $this->getOrder($command->getOrderId());

        $order->note = $command->getInternalNote();

        if (false === $order->validateFields(false)) {
            throw new OrderConstraintException(sprintf('Invalid note "%s" provided for order with id "%d".', $command->getInternalNote(), $command->getOrderId()->getValue()), OrderConstraintException::INVALID_INTERNAL_NOTE);
        }

        if (false === $order->update()) {
            throw new OrderException(sprintf('An error occurred when setting note for order with id "%d".', $command->getOrderId()->getValue()));
        }
    }
}
