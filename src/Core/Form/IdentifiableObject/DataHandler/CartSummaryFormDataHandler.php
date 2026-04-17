<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Form\IdentifiableObject\DataHandler;

use PrestaShop\PrestaShop\Core\CommandBus\CommandBusInterface;
use PrestaShop\PrestaShop\Core\Domain\Order\Command\AddOrderFromBackOfficeCommand;

/**
 * Handles cart summary data
 */
class CartSummaryFormDataHandler implements FormDataHandlerInterface
{
    /**
     * @var CommandBusInterface
     */
    private $commandBus;

    /**
     * @var int
     */
    private $contextEmployeeId;

    /**
     * @param CommandBusInterface $commandBus
     * @param int $contextEmployeeId
     */
    public function __construct(
        CommandBusInterface $commandBus,
        int $contextEmployeeId
    ) {
        $this->commandBus = $commandBus;
        $this->contextEmployeeId = $contextEmployeeId;
    }

    /**
     * Creates new Order from cart summary
     *
     * {@inheritdoc}
     */
    public function create(array $data)
    {
        return $this->commandBus->handle(new AddOrderFromBackOfficeCommand(
            (int) $data['cart_id'],
            (int) $this->contextEmployeeId,
            $data['order_message'],
            $data['payment_module'],
            (int) $data['order_state']
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function update($id, array $data)
    {
        // not used for edition, only creation
    }
}
