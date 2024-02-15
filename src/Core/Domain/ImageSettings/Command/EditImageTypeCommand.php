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

namespace PrestaShop\PrestaShop\Core\Domain\ImageSettings\Command;

use PrestaShop\PrestaShop\Core\Domain\ImageSettings\ValueObject\ImageTypeId;

/**
 * Command that edits zone
 */
class EditImageTypeCommand
{
    private ImageTypeId $imageTypeId;
    private ?string $name = null;
    private ?int $width = null;
    private ?int $height = null;
    private ?bool $products = null;
    private ?bool $categories = null;
    private ?bool $manufacturers = null;
    private ?bool $suppliers = null;
    private ?bool $stores = null;

    public function __construct(int $imageTypeId)
    {
        $this->imageTypeId = new ImageTypeId($imageTypeId);
    }

    public function getImageTypeId(): ImageTypeId
    {
        return $this->imageTypeId;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getWidth(): ?int
    {
        return $this->width;
    }

    public function setWidth(int $width): self
    {
        $this->width = $width;

        return $this;
    }

    public function getHeight(): ?int
    {
        return $this->height;
    }

    public function setHeight(int $height): self
    {
        $this->height = $height;

        return $this;
    }

    public function isProducts(): ?bool
    {
        return $this->products;
    }

    public function setProducts(bool $products): self
    {
        $this->products = $products;

        return $this;
    }

    public function isCategories(): ?bool
    {
        return $this->categories;
    }

    public function setCategories(bool $categories): self
    {
        $this->categories = $categories;

        return $this;
    }

    public function isManufacturers(): ?bool
    {
        return $this->manufacturers;
    }

    public function setManufacturers(bool $manufacturers): self
    {
        $this->manufacturers = $manufacturers;

        return $this;
    }

    public function isSuppliers(): ?bool
    {
        return $this->suppliers;
    }

    public function setSuppliers(bool $suppliers): self
    {
        $this->suppliers = $suppliers;

        return $this;
    }

    public function isStores(): ?bool
    {
        return $this->stores;
    }

    public function setStores(bool $stores): self
    {
        $this->stores = $stores;

        return $this;
    }
}
