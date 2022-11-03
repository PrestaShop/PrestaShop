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
 * needs please refer to https://devdocs.prestashop-project.org/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

namespace PrestaShop\PrestaShop\Core\Domain\CartRule\ValueObject;

use PrestaShop\PrestaShop\Core\Domain\CartRule\Exception\CartRuleConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\ProductId;

/**
 * Gift product VO
 */
class GiftProduct
{
    /**
     * @var ProductId
     */
    private $productId;
    /**
     * @var int
     */
    private $productAttributeId;

    /**
     * @param int $productId
     * @param int|null $productAttributeId
     *
     * @throws CartRuleConstraintException
     */
    public function __construct(int $productId, int $productAttributeId = null)
    {
        $this->assertGiftProductIsValid($productId, $productAttributeId);

        $this->productId = new ProductId($productId);
        $this->productAttributeId = $productAttributeId;
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
     * @param int $productId
     * @param int|null $productAttributeId
     *
     * @throws CartRuleConstraintException
     */
    private function assertGiftProductIsValid(int $productId, ?int $productAttributeId): void
    {
        if (0 >= $productId) {
            throw new CartRuleConstraintException('Gift product ID must be a positive integer', CartRuleConstraintException::INVALID_GIFT_PRODUCT);
        }

        if (null !== $productAttributeId && 0 >= $productAttributeId) {
            throw new CartRuleConstraintException('Gift product attribute ID must be a positive integer', CartRuleConstraintException::INVALID_GIFT_PRODUCT_ATTRIBUTE);
        }
    }
}
