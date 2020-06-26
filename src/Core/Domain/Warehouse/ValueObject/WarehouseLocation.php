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

namespace PrestaShop\PrestaShop\Core\Domain\Warehouse\ValueObject;

use PrestaShop\PrestaShop\Core\Domain\Product\Combination\ValueObject\CombinationId;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\ProductId;

class WarehouseLocation
{
    /**
     * @var WarehouseId
     */
    private $warehouseId;

    /**
     * @var ProductId
     */
    private $productId;

    /**
     * @var CombinationId|null
     */
    private $combinationId;

    /**
     * @var bool|null
     */
    private $isActive;

    /**
     * @var string|null
     */
    private $location;

    /**
     * @param int $warehouseId
     * @param int $productId
     */
    public function __construct(int $warehouseId, int $productId)
    {
        $this->warehouseId = new WarehouseId($warehouseId);
        $this->productId = new ProductId($productId);
    }

    /**
     * @return WarehouseId
     */
    public function getWarehouseId(): WarehouseId
    {
        return $this->warehouseId;
    }

    /**
     * @return ProductId
     */
    public function getProductId(): ProductId
    {
        return $this->productId;
    }

    /**
     * @return CombinationId|null
     */
    public function getCombinationId(): ?CombinationId
    {
        return $this->combinationId;
    }

    /**
     * @param int $combinationId
     *
     * @return WarehouseLocation
     */
    public function setCombinationId(int $combinationId): WarehouseLocation
    {
        $this->combinationId = new CombinationId($combinationId);

        return $this;
    }

    /**
     * @return bool|null
     */
    public function getIsActive(): ?bool
    {
        return $this->isActive;
    }

    /**
     * @param bool $isActive
     * @return WarehouseLocation
     */
    public function setIsActive(bool $isActive): WarehouseLocation
    {
        $this->isActive = $isActive;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getLocation(): ?string
    {
        return $this->location;
    }

    /**
     * @param string $location
     *
     * @return WarehouseLocation
     */
    public function setLocation(string $location): WarehouseLocation
    {
        $this->location = $location;

        return $this;
    }
}
