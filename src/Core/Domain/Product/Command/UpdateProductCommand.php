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

use PrestaShop\PrestaShop\Core\Domain\Product\Command\UpdateProductInput\BasicInformationInput;
use PrestaShop\PrestaShop\Core\Domain\Product\Command\UpdateProductInput\ProductOptionsInput;
use PrestaShop\PrestaShop\Core\Domain\Product\Command\UpdateProductInput\ProductPricesInput;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\ProductId;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopConstraint;

class UpdateProductCommand
{
    /**
     * @var ProductId
     */
    private $productId;

    /**
     * @var ShopConstraint
     */
    private $shopConstraint;

    /**
     * @var BasicInformationInput|null
     */
    private $basicInformation;

    /**
     * @var ProductOptionsInput|null
     */
    private $options;

    /**
     * @var ProductPricesInput|null
     */
    private $prices;

    /**
     * @param int $productId
     * @param ShopConstraint $shopConstraint
     */
    public function __construct(
        int $productId,
        ShopConstraint $shopConstraint
    ) {
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
     * @return ShopConstraint
     */
    public function getShopConstraint(): ShopConstraint
    {
        return $this->shopConstraint;
    }

    /**
     * @return BasicInformationInput|null
     */
    public function getBasicInformation(): ?BasicInformationInput
    {
        return $this->basicInformation;
    }

    /**
     * @param BasicInformationInput|null $basicInformation
     *
     * @return UpdateProductCommand
     */
    public function setBasicInformation(?BasicInformationInput $basicInformation): UpdateProductCommand
    {
        $this->basicInformation = $basicInformation;

        return $this;
    }

    /**
     * @return ProductOptionsInput|null
     */
    public function getOptions(): ?ProductOptionsInput
    {
        return $this->options;
    }

    /**
     * @param ProductOptionsInput|null $options
     *
     * @return UpdateProductCommand
     */
    public function setOptions(?ProductOptionsInput $options): UpdateProductCommand
    {
        $this->options = $options;

        return $this;
    }

    /**
     * @return ProductPricesInput|null
     */
    public function getPrices(): ?ProductPricesInput
    {
        return $this->prices;
    }

    /**
     * @param ProductPricesInput|null $prices
     *
     * @return UpdateProductCommand
     */
    public function setPrices(?ProductPricesInput $prices): UpdateProductCommand
    {
        $this->prices = $prices;

        return $this;
    }
}
