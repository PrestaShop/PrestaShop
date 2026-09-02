<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */
declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\OrderState\QueryHandler;

use OrderState;
use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsQueryHandler;
use PrestaShop\PrestaShop\Core\Domain\OrderState\Exception\OrderStateNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\OrderState\Query\GetOrderStateForEditing;
use PrestaShop\PrestaShop\Core\Domain\OrderState\QueryHandler\GetOrderStateForEditingHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\OrderState\QueryResult\EditableOrderState;
use SplFileInfo;

/**
 * Handles command that gets orderState for editing
 *
 * @internal
 */
#[AsQueryHandler]
final class GetOrderStateForEditingHandler implements GetOrderStateForEditingHandlerInterface
{
    /**
     * {@inheritdoc}
     */
    public function handle(GetOrderStateForEditing $query)
    {
        $orderStateId = $query->getOrderStateId();
        $orderState = new OrderState($orderStateId->getValue());

        if ($orderState->id !== $orderStateId->getValue()) {
            throw new OrderStateNotFoundException($orderStateId, sprintf('OrderState with id "%s" was not found', $orderStateId->getValue()));
        }

        $filePath = _PS_ORDER_STATE_IMG_DIR_ . $orderState->id . '.gif';
        $file = file_exists($filePath) ? new SplFileInfo($filePath) : null;

        return new EditableOrderState(
            $orderStateId,
            $orderState->name,
            $file,
            $orderState->color,
            (bool) $orderState->logable,
            (bool) $orderState->invoice,
            (bool) $orderState->hidden,
            (bool) $orderState->send_email,
            (bool) $orderState->pdf_invoice,
            (bool) $orderState->pdf_delivery,
            (bool) $orderState->shipped,
            (bool) $orderState->paid,
            (bool) $orderState->delivery,
            $orderState->template,
            (bool) $orderState->deleted
        );
    }
}
