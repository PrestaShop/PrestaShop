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
