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

namespace PrestaShop\PrestaShop\Adapter\Manufacturer\CommandHandler;

use Manufacturer;
use PrestaShop\PrestaShop\Adapter\Manufacturer\AbstractManufacturerHandler;
use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsCommandHandler;
use PrestaShop\PrestaShop\Core\Domain\Manufacturer\Command\EditManufacturerCommand;
use PrestaShop\PrestaShop\Core\Domain\Manufacturer\CommandHandler\EditManufacturerHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Manufacturer\Exception\ManufacturerException;
use PrestaShopException;

/**
 * Handles command which edits manufacturer using legacy object model
 */
#[AsCommandHandler]
final class EditManufacturerHandler extends AbstractManufacturerHandler implements EditManufacturerHandlerInterface
{
    /**
     * {@inheritdoc}
     *
     * @throws ManufacturerException
     */
    public function handle(EditManufacturerCommand $command)
    {
        $manufacturerId = $command->getManufacturerId();
        $manufacturer = $this->getManufacturer($manufacturerId);
        $this->populateManufacturerWithData($manufacturer, $command);

        try {
            if (false === $manufacturer->validateFields(false)) {
                throw new ManufacturerException('Manufacturer contains invalid field values');
            }

            if (!$manufacturer->update()) {
                throw new ManufacturerException(sprintf('Cannot update manufacturer with id "%s"', $manufacturer->id));
            }

            if (null !== $command->getAssociatedShops()) {
                $this->associateWithShops($manufacturer, $command->getAssociatedShops());
            }
        } catch (PrestaShopException $e) {
            throw new ManufacturerException(sprintf('Cannot update manufacturer with id "%s"', $manufacturer->id));
        }
    }

    /**
     * Populates Manufacturer object with given data
     *
     * @param Manufacturer $manufacturer
     * @param EditManufacturerCommand $command
     */
    private function populateManufacturerWithData(Manufacturer $manufacturer, EditManufacturerCommand $command)
    {
        if (null !== $command->getName()) {
            $manufacturer->name = $command->getName();
        }
        if (null !== $command->getLocalizedShortDescriptions()) {
            $manufacturer->short_description = $command->getLocalizedShortDescriptions();
        }
        if (null !== $command->getLocalizedDescriptions()) {
            $manufacturer->description = $command->getLocalizedDescriptions();
        }
        if (null !== $command->getLocalizedMetaDescriptions()) {
            $manufacturer->meta_description = $command->getLocalizedMetaDescriptions();
        }
        if (null !== $command->getLocalizedMetaKeywords()) {
            $manufacturer->meta_keywords = $command->getLocalizedMetaKeywords();
        }
        if (null !== $command->getLocalizedMetaTitles()) {
            $manufacturer->meta_title = $command->getLocalizedMetaTitles();
        }
        if (null !== $command->isEnabled()) {
            $manufacturer->active = $command->isEnabled();
        }
    }
}
