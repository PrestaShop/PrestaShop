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

namespace PrestaShop\PrestaShop\Core\Domain\CartRule\ValueObject;

use PrestaShop\PrestaShop\Core\Domain\CartRule\Exception\CartRuleConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Product\Exception\ProductConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\ProductId;

/**
 * Discount application type indicates what the discount should be applied to.
 * E.g. to whole order, to a specific product, to cheapest product.
 */
class DiscountApplicationType
{
    /**
     * Discount will be applied to order without shipping
     */
    public const ORDER_WITHOUT_SHIPPING = 'order_without_shipping';

    /**
     * Discount will be applied to specifically selected product
     */
    public const SPECIFIC_PRODUCT = 'specific_product';

    /**
     * Discount will be applied to cheapest product of the cart
     */
    public const CHEAPEST_PRODUCT = 'cheapest_product';

    /**
     * Discount will be applied to products selection from cart rule's conditions.
     */
    public const SELECTED_PRODUCTS = 'selected_products';

    private const AVAILABLE_TYPES = [
        self::ORDER_WITHOUT_SHIPPING,
        self::SPECIFIC_PRODUCT,
        self::CHEAPEST_PRODUCT,
        self::SELECTED_PRODUCTS,
    ];

    /**
     * @var string
     */
    private $type;

    /**
     * @var ProductId|null
     */
    private $productId;

    /**
     * @param string $type
     * @param int|null $productId product id is required when application type is "specific_product"
     *
     * @throws CartRuleConstraintException
     * @throws ProductConstraintException
     */
    public function __construct(string $type, ?int $productId = null)
    {
        if (!in_array($type, self::AVAILABLE_TYPES)) {
            throw new CartRuleConstraintException(sprintf('Invalid cart rule discount application type %s. Available types are: %s', var_export($type, true), implode(', ', self::AVAILABLE_TYPES)), CartRuleConstraintException::INVALID_DISCOUNT_APPLICATION_TYPE);
        }

        if ($type === self::SPECIFIC_PRODUCT) {
            if (!$productId) {
                throw new CartRuleConstraintException(
                    'Provided Cart rule discount application type requires specific product',
                    CartRuleConstraintException::MISSING_DISCOUNT_APPLICATION_PRODUCT
                );
            }

            $this->productId = new ProductId($productId);
        }

        $this->type = $type;
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @return ProductId|null
     */
    public function getProductId(): ?ProductId
    {
        return $this->productId;
    }
}
