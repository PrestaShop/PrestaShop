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

namespace PrestaShop\PrestaShop\Core\Domain\SpecificPrice\Command;

use PrestaShop\PrestaShop\Core\Domain\Cart\ValueObject\CartId;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\ProductId;

@trigger_error(
    sprintf(
        '%s is deprecated since version 8.0.0 and will be removed in the next major version.',
        DeleteSpecificPriceByCartProductCommand::class
    ),
    E_USER_DEPRECATED
);

/**
 * @deprecated since 8.0.0 and will be removed in the next major version.
 * @see UpdateProductPriceInCartCommand to change a product price in a cart
 * or @see DeleteProductSpecificPriceCommand if you wish to set special price rules on a product
 */
class DeleteSpecificPriceByCartProductCommand
{
    /**
     * @var CartId
     */
    private $cartId;

    /**
     * @var ProductId
     */
    private $productId;

    /**
     * @var int|null
     */
    private $productAttributeId;

    public function __construct(
        int $cartId,
        int $productId
    ) {
        $this->cartId = new CartId($cartId);
        $this->productId = new ProductId($productId);
    }

    /**
     * @return CartId
     */
    public function getCartId(): CartId
    {
        return $this->cartId;
    }

    /**
     * @return ProductId
     */
    public function getProductId(): ProductId
    {
        return $this->productId;
    }

    /**
     * @return int|null
     */
    public function getProductAttributeId(): ?int
    {
        return $this->productAttributeId;
    }

    /**
     * @param int $productAttributeId
     *
     * @return DeleteSpecificPriceByCartProductCommand
     */
    public function setProductAttributeId(int $productAttributeId): self
    {
        $this->productAttributeId = $productAttributeId;

        return $this;
    }
}
