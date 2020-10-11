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

namespace PrestaShop\PrestaShop\Core\Domain\ImageType\QueryResult;

use PrestaShop\PrestaShop\Core\Domain\ImageType\ValueObject\ImageTypeId;

/**
 * Transfers editable image type data.
 */
class EditableImageType
{
    /**
     * @var ImageTypeId
     */
    private $imageTypeId;

    /**
     * @var string
     */
    private $name;

    /**
     * @var int
     */
    private $width;

    /**
     * @var int
     */
    private $height;

    /**
     * @var bool
     */
    private $productsEnabled;

    /**
     * @var bool
     */
    private $categoriesEnabled;

    /**
     * @var bool
     */
    private $manufacturersEnabled;

    /**
     * @var bool
     */
    private $suppliersEnabled;

    /**
     * @var bool
     */
    private $storesEnabled;

    /**
     * @param ImageTypeId $imageTypeId
     * @param string $name
     * @param int $width
     * @param int $height
     * @param bool $productsEnabled
     * @param bool $categoriesEnabled
     * @param bool $manufacturersEnabled
     * @param bool $suppliersEnabled
     * @param bool $storesEnabled
     */
    public function __construct(
        ImageTypeId $imageTypeId,
        string $name,
        int $width,
        int $height,
        bool $productsEnabled,
        bool $categoriesEnabled,
        bool $manufacturersEnabled,
        bool $suppliersEnabled,
        bool $storesEnabled
    ) {
        $this->imageTypeId = $imageTypeId;
        $this->name = $name;
        $this->width = $width;
        $this->height = $height;
        $this->productsEnabled = $productsEnabled;
        $this->categoriesEnabled = $categoriesEnabled;
        $this->manufacturersEnabled = $manufacturersEnabled;
        $this->suppliersEnabled = $suppliersEnabled;
        $this->storesEnabled = $storesEnabled;
    }

    /**
     * @return ImageTypeId
     */
    public function getImageTypeId(): ImageTypeId
    {
        return $this->imageTypeId;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return int
     */
    public function getWidth(): int
    {
        return $this->width;
    }

    /**
     * @return int
     */
    public function getHeight(): int
    {
        return $this->height;
    }

    /**
     * @return bool
     */
    public function isProductsEnabled(): bool
    {
        return $this->productsEnabled;
    }

    /**
     * @return bool
     */
    public function isCategoriesEnabled(): bool
    {
        return $this->categoriesEnabled;
    }

    /**
     * @return bool
     */
    public function isManufacturersEnabled(): bool
    {
        return $this->manufacturersEnabled;
    }

    /**
     * @return bool
     */
    public function isSuppliersEnabled(): bool
    {
        return $this->suppliersEnabled;
    }

    /**
     * @return bool
     */
    public function isStoresEnabled(): bool
    {
        return $this->storesEnabled;
    }
}
