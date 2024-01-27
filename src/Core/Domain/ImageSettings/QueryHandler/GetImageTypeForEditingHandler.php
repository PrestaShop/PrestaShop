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

namespace PrestaShop\PrestaShop\Core\Domain\ImageSettings\QueryHandler;

use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsQueryHandler;
use PrestaShop\PrestaShop\Core\Domain\ImageSettings\Exception\ImageTypeNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\ImageSettings\Query\GetImageTypeForEditing;
use PrestaShop\PrestaShop\Core\Domain\ImageSettings\QueryResult\EditableImageType;
use PrestaShopBundle\Entity\Repository\ImageTypeRepository;

/**
 * Handles command that gets image type for editing
 *
 * @internal
 */
#[AsQueryHandler]
final class GetImageTypeForEditingHandler implements GetImageTypeForEditingHandlerInterface
{
    public function __construct(
        private readonly ImageTypeRepository $imageTypeRepository
    ) {
    }

    /**
     * {@inheritdoc}
     */
    public function handle(GetImageTypeForEditing $query): EditableImageType
    {
        $imageType = $this->imageTypeRepository->find($query->getImageTypeId()->getValue());

        if (null === $imageType) {
            throw new ImageTypeNotFoundException(sprintf('Image type with id "%d" not found', $query->getImageTypeId()->getValue()));
        }

        return new EditableImageType(
            $query->getImageTypeId(),
            (string) $imageType->getName(),
            (int) $imageType->getWidth(),
            (int) $imageType->getHeight(),
            (bool) $imageType->isProducts(),
            (bool) $imageType->isCategories(),
            (bool) $imageType->isManufacturers(),
            (bool) $imageType->isSuppliers(),
            (bool) $imageType->isStores()
        );
    }
}
