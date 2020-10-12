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

use ImageType;
use PrestaShop\PrestaShop\Adapter\ImageType\AbstractImageTypeHandler;
use PrestaShop\PrestaShop\Core\Domain\ImageType\Command\AddImageTypeCommand;
use PrestaShop\PrestaShop\Core\Domain\ImageType\CommandHandler\AddImageTypeHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\ImageType\Exception\CannotAddImageTypeException;
use PrestaShop\PrestaShop\Core\Domain\ImageType\ValueObject\ImageTypeId;

/**
 * Handles command that creates new image type
 */
class AddImageTypeHandler extends AbstractImageTypeHandler implements AddImageTypeHandlerInterface
{
    /**
     * {@inheritdoc}
     */
    public function handle(AddImageTypeCommand $command): ImageTypeId
    {
        $imageType = new ImageType();

        $imageType->name = $command->getName();
        $imageType->width = $command->getWidth();
        $imageType->height = $command->getHeight();
        $imageType->products = $command->isProductsEnabled();
        $imageType->categories = $command->isCategoriesEnabled();
        $imageType->manufacturers = $command->isManufacturersEnabled();
        $imageType->suppliers = $command->isSuppliersEnabled();
        $imageType->stores = $command->isStoresEnabled();

        if (false === $imageType->validateFields(false)) {
            throw new CannotAddImageTypeException('Image type contains invalid field values');
        }

        if (false === $imageType->add()) {
            throw new CannotAddImageTypeException('Failed to add new image type');
        }

        return new ImageTypeId((int) $imageType->id);
    }
}
