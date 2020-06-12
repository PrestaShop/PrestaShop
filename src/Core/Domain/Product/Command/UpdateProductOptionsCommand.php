<?php
/**
 * 2007-2020 PrestaShop SA and Contributors
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
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
 * needs please refer to https://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2020 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Product\Command;

use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\ProductCondition;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\ProductId;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\ProductVisibility;

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
     * @var string[]|null
     */
    private $tags;

    /**
     * @var ProductCondition|null
     */
    private $condition;

    /**
     * @var string|null
     */
    private $isbn;

    /**
     * @var string|null
     */
    private $upc;

    /**
     * @var string|null
     */
    private $ean13;

    /**
     * @var string|null
     */
    private $mpn;

    /**
     * @param ProductId $productId
     */
    public function __construct(int $productId)
    {
        $this->productId = new ProductId($productId);
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
     * @param ProductVisibility $visibility
     *
     * @return UpdateProductOptionsCommand
     */
    public function setVisibility(string $visibility): UpdateProductOptionsCommand
    {
        $this->visibility = new ProductVisibility($visibility);

        return $this;
    }

    /**
     * @return bool|null
     */
    public function getAvailableForOrder(): ?bool
    {
        return $this->availableForOrder;
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
    public function getOnlineOnly(): ?bool
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
    public function getShowPrice(): ?bool
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
     * @return string[]|null
     */
    public function getTags(): ?array
    {
        return $this->tags;
    }

    /**
     * @param string[] $tags
     *
     * @return UpdateProductOptionsCommand
     */
    public function setTags(array $tags): UpdateProductOptionsCommand
    {
        $this->tags = $tags;

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
     * @return string|null
     */
    public function getIsbn(): ?string
    {
        return $this->isbn;
    }

    /**
     * @param string $isbn
     *
     * @return UpdateProductOptionsCommand
     */
    public function setIsbn(string $isbn): UpdateProductOptionsCommand
    {
        $this->isbn = $isbn;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getUpc(): ?string
    {
        return $this->upc;
    }

    /**
     * @param string $upc
     *
     * @return UpdateProductOptionsCommand
     */
    public function setUpc(string $upc): UpdateProductOptionsCommand
    {
        $this->upc = $upc;

        return $this;
    }

    /**
     * @return string
     */
    public function getEan13(): string
    {
        return $this->ean13;
    }

    /**
     * @param string $ean13
     *
     * @return UpdateProductOptionsCommand
     */
    public function setEan13(string $ean13): UpdateProductOptionsCommand
    {
        $this->ean13 = $ean13;

        return $this;
    }

    /**
     * @return string
     */
    public function getMpn(): string
    {
        return $this->mpn;
    }

    /**
     * @param string $mpn
     *
     * @return UpdateProductOptionsCommand
     */
    public function setMpn(string $mpn): UpdateProductOptionsCommand
    {
        $this->mpn = $mpn;

        return $this;
    }
}
