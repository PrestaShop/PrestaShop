<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
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
