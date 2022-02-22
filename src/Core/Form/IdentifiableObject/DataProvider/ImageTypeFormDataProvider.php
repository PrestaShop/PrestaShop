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

namespace PrestaShop\PrestaShop\Core\Form\IdentifiableObject\DataProvider;

use PrestaShop\PrestaShop\Core\CommandBus\CommandBusInterface;
use PrestaShop\PrestaShop\Core\Domain\ImageType\Query\GetImageTypeForEditing;
use PrestaShop\PrestaShop\Core\Domain\ImageType\QueryResult\EditableImageType;

/**
 * Provides data for image type add/edit
 */
final class ImageTypeFormDataProvider implements FormDataProviderInterface
{
    /**
     * @var CommandBusInterface
     */
    private $queryBus;

    /**
     * @param CommandBusInterface $queryBus
     */
    public function __construct(CommandBusInterface $queryBus)
    {
        $this->queryBus = $queryBus;
    }

    /**
     * {@inheritdoc}
     */
    public function getData($imageTypeId): array
    {
        /** @var EditableImageType $editableImageType */
        $editableImageType = $this->queryBus->handle(new GetImageTypeForEditing($imageTypeId));

        return [
            'name' => $editableImageType->getName(),
            'width' => $editableImageType->getWidth(),
            'height' => $editableImageType->getHeight(),
            'products_enabled' => $editableImageType->isProductsEnabled(),
            'categories_enabled' => $editableImageType->isCategoriesEnabled(),
            'manufacturers_enabled' => $editableImageType->isManufacturersEnabled(),
            'suppliers_enabled' => $editableImageType->isSuppliersEnabled(),
            'stores_enabled' => $editableImageType->isStoresEnabled(),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getDefaultData(): array
    {
        return [
            'products_enabled' => false,
            'categories_enabled' => false,
            'manufacturers_enabled' => false,
            'suppliers_enabled' => false,
            'stores_enabled' => false,
        ];
    }
}
