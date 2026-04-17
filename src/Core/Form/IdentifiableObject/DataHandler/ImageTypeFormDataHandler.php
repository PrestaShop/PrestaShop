<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Form\IdentifiableObject\DataHandler;

use PrestaShop\PrestaShop\Core\CommandBus\CommandBusInterface;
use PrestaShop\PrestaShop\Core\Domain\ImageSettings\Command\AddImageTypeCommand;
use PrestaShop\PrestaShop\Core\Domain\ImageSettings\Command\EditImageTypeCommand;

class ImageTypeFormDataHandler implements FormDataHandlerInterface
{
    public function __construct(
        private CommandBusInterface $commandBus
    ) {
    }

    public function create(array $data)
    {
        $this->commandBus->handle(new AddImageTypeCommand(
            $data['name'],
            (int) $data['width'],
            (int) $data['height'],
            (bool) $data['products'],
            (bool) $data['categories'],
            (bool) $data['manufacturers'],
            (bool) $data['suppliers'],
            (bool) $data['stores'],
        ));
    }

    public function update($id, array $data)
    {
        $command = new EditImageTypeCommand((int) $id);
        $command
            ->setName($data['name'])
            ->setWidth((int) $data['width'])
            ->setHeight((int) $data['height'])
            ->setProducts((bool) $data['products'])
            ->setCategories((bool) $data['categories'])
            ->setManufacturers((bool) $data['manufacturers'])
            ->setSuppliers((bool) $data['suppliers'])
            ->setStores((bool) $data['stores'])
        ;

        $this->commandBus->handle($command);
    }
}
