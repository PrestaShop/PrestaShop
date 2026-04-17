<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Form\IdentifiableObject\DataHandler;

use PrestaShop\PrestaShop\Core\CommandBus\CommandBusInterface;
use PrestaShop\PrestaShop\Core\Domain\Product\Shop\Command\SetProductShopsCommand;

class ProductShopsFormDataHandler implements FormDataHandlerInterface
{
    /**
     * @var CommandBusInterface
     */
    private $bus;

    /**
     * @param CommandBusInterface $bus
     */
    public function __construct(
        CommandBusInterface $bus
    ) {
        $this->bus = $bus;
    }

    /**
     * {@inheritDoc}
     */
    public function create(array $data)
    {
        // The form is only used for update not creation
    }

    /**
     * {@inheritDoc}
     */
    public function update($id, array $data)
    {
        $this->bus->handle(new SetProductShopsCommand(
            (int) $id,
            (int) $data['source_shop_id'],
            $data['selected_shops']
        ));
    }
}
