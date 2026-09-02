<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Form\IdentifiableObject\DataHandler;

use OrderReturn;
use PrestaShop\PrestaShop\Core\CommandBus\CommandBusInterface;
use PrestaShop\PrestaShop\Core\Domain\OrderReturn\Command\BulkDeleteProductsFromOrderReturnCommand;
use PrestaShop\PrestaShop\Core\Domain\OrderReturn\Command\UpdateOrderReturnStateCommand;
use PrestaShop\PrestaShop\Core\Exception\NotImplementedException;

/**
 * Saves or updates order return data submitted in form
 */
class OrderReturnFormDataHandler implements FormDataHandlerInterface
{
    /**
     * @var CommandBusInterface
     */
    private $commandBus;

    /**
     * @param CommandBusInterface $commandBus
     */
    public function __construct(CommandBusInterface $commandBus)
    {
        $this->commandBus = $commandBus;
    }

    /**
     * {@inheritdoc}
     */
    public function update($orderReturnId, array $data): void
    {
        // Issue #27628: per-row deletions are staged client-side and committed here in one bulk
        // command, so the user only ever submits the page once. Deletions run first so the state
        // update sees the final product list.
        $stagedRows = $this->parseStagedDeletions($data['staged_product_deletions'] ?? []);
        if ($stagedRows !== []) {
            $this->commandBus->handle(new BulkDeleteProductsFromOrderReturnCommand((int) $orderReturnId, $stagedRows));
        }

        $orderReturnStateId = (int) $data['order_return_state'];
        $this->commandBus->handle(new UpdateOrderReturnStateCommand((int) $orderReturnId, $orderReturnStateId));
    }

    /**
     * Order Return doesn't have a create option
     *
     * @param array $data
     *
     * @throws NotImplementedException
     */
    public function create(array $data): void
    {
        throw new NotImplementedException(OrderReturn::class . ' is not created by form, this method should never be called');
    }

    /**
     * The front-end writes a JSON string of the form
     *   [{"order_detail_id":42,"customization_id":0},{"order_detail_id":43,"customization_id":7}]
     * into the staged_product_deletions hidden field. We tolerate malformed or empty input.
     *
     * @param mixed $rawJson raw value coming from the hidden field
     *
     * @return array<int, array{order_detail_id: int, customization_id: int}>
     */
    private function parseStagedDeletions($rawJson): array
    {
        if (!is_string($rawJson) || $rawJson === '') {
            return [];
        }

        $decoded = json_decode($rawJson, true);
        if (!is_array($decoded)) {
            return [];
        }

        $parsed = [];
        foreach ($decoded as $row) {
            if (!is_array($row)) {
                continue;
            }
            $orderDetailId = (int) ($row['order_detail_id'] ?? 0);
            if ($orderDetailId <= 0) {
                continue;
            }
            $parsed[] = [
                'order_detail_id' => $orderDetailId,
                'customization_id' => (int) ($row['customization_id'] ?? 0),
            ];
        }

        return $parsed;
    }
}
