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

namespace PrestaShop\PrestaShop\Core\Domain\ImageSettings\QueryResult;

use PrestaShop\PrestaShop\Core\Domain\ImageSettings\ValueObject\ImageTypeId;

/**
 * Transfers image type data for editing
 */
class EditableImageType
{
    public function __construct(
        private readonly ImageTypeId $imageTypeId,
        private readonly string $name,
        private readonly int $width,
        private readonly int $height,
        private readonly bool $products,
        private readonly bool $categories,
        private readonly bool $manufacturers,
        private readonly bool $suppliers,
        private readonly bool $stores,
    ) {
    }

    public function getImageTypeId(): ImageTypeId
    {
        return $this->imageTypeId;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getWidth(): int
    {
        return $this->width;
    }

    public function getHeight(): int
    {
        return $this->height;
    }

    public function isProducts(): bool
    {
        return $this->products;
    }

    public function isCategories(): bool
    {
        return $this->categories;
    }

    public function isManufacturers(): bool
    {
        return $this->manufacturers;
    }

    public function isSuppliers(): bool
    {
        return $this->suppliers;
    }

    public function isStores(): bool
    {
        return $this->stores;
    }
}
