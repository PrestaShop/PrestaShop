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

namespace PrestaShop\PrestaShop\Core\Domain\Cart\Command;

use PrestaShop\PrestaShop\Core\Domain\Cart\Exception\CartException;
use PrestaShop\PrestaShop\Core\Domain\Cart\ValueObject\CartId;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\ProductId;

/**
 * Updates cart product price
 */
class UpdateProductPriceInCartCommand
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
     * @var int
     */
    private $combinationId;

    /**
     * @var float
     */
    private $price;

    /**
     * @param int $cartId
     * @param int $productId
     * @param int $combinationId
     * @param float $price
     */
    public function __construct($cartId, $productId, $combinationId, $price)
    {
        $this->assertPriceIsPositiveFloat($price);

        $this->cartId = new CartId($cartId);
        $this->productId = new ProductId($productId);
        $this->combinationId = $combinationId;
        $this->price = $price;
    }

    /**
     * @return CartId
     */
    public function getCartId()
    {
        return $this->cartId;
    }

    /**
     * @return ProductId
     */
    public function getProductId()
    {
        return $this->productId;
    }

    /**
     * @return int
     */
    public function getCombinationId()
    {
        return $this->combinationId;
    }

    /**
     * @return float
     */
    public function getPrice()
    {
        return $this->price;
    }

    /**
     * @param float $price
     */
    private function assertPriceIsPositiveFloat($price)
    {
        if (!is_float($price) || 0 > $price) {
            throw new CartException(sprintf('Price %s is invalid. Price must be float greater than zero.', var_export($price, true)));
        }
    }
}
