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

namespace PrestaShop\PrestaShop\Core\Domain\Product\Command\UpdateProductInput;

use PrestaShop\PrestaShop\Core\Domain\Manufacturer\Exception\ManufacturerConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Manufacturer\ValueObject\ManufacturerId;
use PrestaShop\PrestaShop\Core\Domain\Manufacturer\ValueObject\ManufacturerIdInterface;
use PrestaShop\PrestaShop\Core\Domain\Manufacturer\ValueObject\NoManufacturerId;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\ProductCondition;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\ProductVisibility;

class ProductOptionsInput
{
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
     * @return ProductOptionsInput
     */
    public function setVisibility(string $visibility): ProductOptionsInput
    {
        $this->visibility = new ProductVisibility($visibility);

        return $this;
    }

    /**
     * @param bool $availableForOrder
     *
     * @return ProductOptionsInput
     */
    public function setAvailableForOrder(bool $availableForOrder): ProductOptionsInput
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
     * @return ProductOptionsInput
     */
    public function setOnlineOnly(bool $onlineOnly): ProductOptionsInput
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
     * @return ProductOptionsInput
     */
    public function setShowPrice(bool $showPrice): ProductOptionsInput
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
     * @return ProductOptionsInput
     */
    public function setCondition(string $condition): ProductOptionsInput
    {
        $this->condition = new ProductCondition($condition);

        return $this;
    }

    /**
     * @param bool $showCondition
     *
     * @return ProductOptionsInput
     */
    public function setShowCondition(bool $showCondition): ProductOptionsInput
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
     * @return ProductOptionsInput
     */
    public function setManufacturerId(int $manufacturerId): ProductOptionsInput
    {
        $this->manufacturerId = NoManufacturerId::NO_MANUFACTURER_ID === $manufacturerId ?
            new NoManufacturerId() :
            new ManufacturerId($manufacturerId)
        ;

        return $this;
    }
}
