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

namespace PrestaShop\PrestaShop\Adapter\ImageType\CommandHandler;

use PrestaShop\PrestaShop\Adapter\ImageType\AbstractImageTypeHandler;
use PrestaShop\PrestaShop\Core\Domain\ImageType\Command\EditImageTypeCommand;
use PrestaShop\PrestaShop\Core\Domain\ImageType\CommandHandler\EditImageTypeHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\ImageType\Exception\ImageTypeException;

/**
 * Handles command that edits image type
 */
class EditImageTypeHandler extends AbstractImageTypeHandler implements EditImageTypeHandlerInterface
{
    /**
     * {@inheritdoc}
     */
    public function handle(EditImageTypeCommand $command)
    {
        $imageType = $this->getImageType($command->getImageTypeId());

        if (null !== $command->getName()) {
            $imageType->name = $command->getName();
        }

        if (null !== $command->getWidth()) {
            $imageType->width = $command->getWidth();
        }

        if (null !== $command->getHeight()) {
            $imageType->height = $command->getHeight();
        }

        if (null !== $command->isAppliedOnProducts()) {
            $imageType->products = $command->isAppliedOnProducts();
        }

        if (null !== $command->isAppliedOnCategories()) {
            $imageType->categories = $command->isAppliedOnCategories();
        }

        if (null !== $command->isAppliedOnManufacturers()) {
            $imageType->manufacturers = $command->isAppliedOnManufacturers();
        }

        if (null !== $command->isAppliedOnSuppliers()) {
            $imageType->suppliers = $command->isAppliedOnSuppliers();
        }

        if (null !== $command->isAppliedOnStores()) {
            $imageType->stores = $command->isAppliedOnStores();
        }

        if (false === $imageType->validateFields(false)) {
            throw new ImageTypeException('Image type contains invalid values.');
        }

        if (false === $imageType->update()) {
            throw new ImageTypeException(sprintf('Cannot update image type with id "%d"', $command->getImageTypeId()->getValue()));
        }
    }
}
