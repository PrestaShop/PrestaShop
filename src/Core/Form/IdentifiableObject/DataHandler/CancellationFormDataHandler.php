<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Form\IdentifiableObject\DataHandler;

use PrestaShop\PrestaShop\Core\CommandBus\CommandBusInterface;
use PrestaShop\PrestaShop\Core\Domain\Order\Command\CancelOrderProductCommand;

/**
 * Class CancellationFormDataHandler
 */
final class CancellationFormDataHandler implements FormDataHandlerInterface
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
    public function create(array $data)
    {
        return;
    }

    /**
     * {@inheritdoc}
     */
    public function update($id, array $data)
    {
        $cancelledProducts = [];
        foreach ($data['products'] as $product) {
            if (isset($data['selected_' . $product->getOrderDetailId()]) && $data['selected_' . $product->getOrderDetailId()]) {
                $cancelledProducts[$product->getOrderDetailId()] = $data['quantity_' . $product->getOrderDetailId()] ?? 0;
            }
        }

        $command = new CancelOrderProductCommand(
            $cancelledProducts,
            $id
        );

        $this->commandBus->handle($command);
    }
}
