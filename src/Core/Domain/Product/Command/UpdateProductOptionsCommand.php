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

namespace PrestaShop\PrestaShop\Core\Domain\Product\Command;

use PrestaShop\PrestaShop\Core\Domain\Manufacturer\Exception\ManufacturerConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Manufacturer\ValueObject\ManufacturerId;
use PrestaShop\PrestaShop\Core\Domain\Manufacturer\ValueObject\ManufacturerIdInterface;
use PrestaShop\PrestaShop\Core\Domain\Manufacturer\ValueObject\NoManufacturerId;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\ProductCondition;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\ProductId;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\ProductVisibility;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopConstraint;

class UpdateProductOptionsCommand
{
    /**
     * @var ProductId
     */
    private $productId;

    /**
     * @var ProductVisibility|null
     */
    private $visibility;

    /**
     * @var bool|null
     */
    private $availableForOrder;

    /**
     * @var bool|null
     */
    private $onlineOnly;

    /**
     * @var bool|null
     */
    private $showPrice;

    /**
     * @var ProductCondition|null
     */
    private $condition;

    /**
     * @var bool|null
     */
    private $showCondition;

    /**
     * @var ManufacturerIdInterface|null
     */
    private $manufacturerId;

    /**
     * @var ShopConstraint
     */
    private $shopConstraint;

    /**
     * @param int $productId
     * @param ShopConstraint $shopConstraint
     */
    public function __construct(int $productId, ShopConstraint $shopConstraint)
    {
        $this->productId = new ProductId($productId);
        $this->shopConstraint = $shopConstraint;
    }

    /**
     * @return ProductId
     */
    public function getProductId(): ProductId
    {
        return $this->productId;
    }

    /**
     * @return ProductVisibility|null
     */
    public function getVisibility(): ?ProductVisibility
    {
        return $this->visibility;
    }

    /**
     * @return bool|null
     */
    public function isAvailableForOrder(): ?bool
    {
        return $this->availableForOrder;
    }

    /**
     * @param string $visibility
     *
     * @return UpdateProductOptionsCommand
     */
    public function setVisibility(string $visibility): UpdateProductOptionsCommand
    {
        $this->visibility = new ProductVisibility($visibility);

        return $this;
    }

    /**
     * @param bool $availableForOrder
     *
     * @return UpdateProductOptionsCommand
     */
    public function setAvailableForOrder(bool $availableForOrder): UpdateProductOptionsCommand
    {
        $this->availableForOrder = $availableForOrder;

        return $this;
    }

    /**
     * @return bool|null
     */
    public function isOnlineOnly(): ?bool
    {
        return $this->onlineOnly;
    }

    /**
     * @param bool $onlineOnly
     *
     * @return UpdateProductOptionsCommand
     */
    public function setOnlineOnly(bool $onlineOnly): UpdateProductOptionsCommand
    {
        $this->onlineOnly = $onlineOnly;

        return $this;
    }

    /**
     * @return bool|null
     */
    public function showPrice(): ?bool
    {
        return $this->showPrice;
    }

    /**
     * @param bool $showPrice
     *
     * @return UpdateProductOptionsCommand
     */
    public function setShowPrice(bool $showPrice): UpdateProductOptionsCommand
    {
        $this->showPrice = $showPrice;

        return $this;
    }

    /**
     * @return ProductCondition|null
     */
    public function getCondition(): ?ProductCondition
    {
        return $this->condition;
    }

    /**
     * @param string $condition
     *
     * @return UpdateProductOptionsCommand
     */
    public function setCondition(string $condition): UpdateProductOptionsCommand
    {
        $this->condition = new ProductCondition($condition);

        return $this;
    }

    /**
     * @param bool $showCondition
     *
     * @return $this
     */
    public function setShowCondition(bool $showCondition): UpdateProductOptionsCommand
    {
        $this->showCondition = $showCondition;

        return $this;
    }

    /**
     * @return bool|null
     */
    public function showCondition(): ?bool
    {
        return $this->showCondition;
    }

    /**
     * @return ManufacturerIdInterface|null
     */
    public function getManufacturerId(): ?ManufacturerIdInterface
    {
        return $this->manufacturerId;
    }

    /**
     * @param int $manufacturerId
     *
     * @throws ManufacturerConstraintException
     *
     * @return $this
     */
    public function setManufacturerId(int $manufacturerId): UpdateProductOptionsCommand
    {
        $this->manufacturerId = NoManufacturerId::NO_MANUFACTURER_ID === $manufacturerId ?
            new NoManufacturerId() :
            new ManufacturerId($manufacturerId)
        ;

        return $this;
    }

    /**
     * @return ShopConstraint
     */
    public function getShopConstraint(): ShopConstraint
    {
        return $this->shopConstraint;
    }
}
