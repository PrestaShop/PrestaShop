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

namespace PrestaShop\PrestaShop\Core\Domain\ImageType\Command;

use PrestaShop\PrestaShop\Core\Domain\ImageType\Exception\ImageTypeConstraintException;
use PrestaShop\PrestaShop\Core\Domain\ImageType\ValueObject\ImageTypeId;

/**
 * Edits given image type with provided data
 */
class EditImageTypeCommand
{
    /**
     * @var ImageTypeId
     */
    private $imageTypeId;

    /**
     * @var string|null
     */
    private $name;

    /**
     * @var int|null
     */
    private $width;

    /**
     * @var int|null
     */
    private $height;

    /**
     * @var bool|null
     */
    private $productsEnabled;

    /**
     * @var bool|null
     */
    private $categoriesEnabled;

    /**
     * @var bool|null
     */
    private $manufacturersEnabled;

    /**
     * @var bool|null
     */
    private $suppliersEnabled;

    /**
     * @var bool|null
     */
    private $storesEnabled;

    /**
     * @param int $imageTypeId
     *
     * @throws ImageTypeConstraintException
     */
    public function __construct(int $imageTypeId)
    {
        $this->imageTypeId = new ImageTypeId($imageTypeId);
    }

    /**
     * @return ImageTypeId
     */
    public function getImageTypeId(): ImageTypeId
    {
        return $this->imageTypeId;
    }

    /**
     * @param ImageTypeId $imageTypeId
     *
     * @return self
     */
    public function setImageTypeId(ImageTypeId $imageTypeId): self
    {
        $this->imageTypeId = $imageTypeId;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * @param string|null $name
     *
     * @return self
     */
    public function setName(?string $name): self
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getWidth(): ?int
    {
        return $this->width;
    }

    /**
     * @param int|null $width
     *
     * @return self
     */
    public function setWidth(?int $width): self
    {
        $this->width = $width;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getHeight(): ?int
    {
        return $this->height;
    }

    /**
     * @param int|null $height
     *
     * @return self
     */
    public function setHeight(?int $height): self
    {
        $this->height = $height;

        return $this;
    }

    /**
     * @return bool|null
     */
    public function getProductsEnabled(): ?bool
    {
        return $this->productsEnabled;
    }

    /**
     * @param bool|null $productsEnabled
     *
     * @return self
     */
    public function setProductsEnabled(?bool $productsEnabled): self
    {
        $this->productsEnabled = $productsEnabled;

        return $this;
    }

    /**
     * @return bool|null
     */
    public function getCategoriesEnabled(): ?bool
    {
        return $this->categoriesEnabled;
    }

    /**
     * @param bool|null $categoriesEnabled
     *
     * @return self
     */
    public function setCategoriesEnabled(?bool $categoriesEnabled): self
    {
        $this->categoriesEnabled = $categoriesEnabled;

        return $this;
    }

    /**
     * @return bool|null
     */
    public function getManufacturersEnabled(): ?bool
    {
        return $this->manufacturersEnabled;
    }

    /**
     * @param bool|null $manufacturersEnabled
     *
     * @return self
     */
    public function setManufacturersEnabled(?bool $manufacturersEnabled): self
    {
        $this->manufacturersEnabled = $manufacturersEnabled;

        return $this;
    }

    /**
     * @return bool|null
     */
    public function getSuppliersEnabled(): ?bool
    {
        return $this->suppliersEnabled;
    }

    /**
     * @param bool|null $storesEnabled
     *
     * @return self
     */
    public function setStoresEnabled(?bool $storesEnabled): self
    {
        $this->storesEnabled = $storesEnabled;

        return $this;
    }

    /**
     * @return bool|null
     */
    public function getStoresEnabled(): ?bool
    {
        return $this->storesEnabled;
    }

    /**
     * @param bool|null $suppliersEnabled
     *
     * @return self
     */
    public function setSuppliersEnabled(?bool $suppliersEnabled): self
    {
        $this->suppliersEnabled = $suppliersEnabled;

        return $this;
    }
}
