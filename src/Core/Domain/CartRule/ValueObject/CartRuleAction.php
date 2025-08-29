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

namespace PrestaShop\PrestaShop\Core\Domain\CartRule\ValueObject;

use PrestaShop\PrestaShop\Core\Domain\CartRule\Exception\CartRuleConstraintException;

class CartRuleAction
{
    /**
     * @var bool
     */
    private $freeShipping;

    /**
     * @var GiftProduct|null
     */
    private $giftProduct;

    /**
     * @var Discount|null
     */
    private $discount;

    public function __construct(
        bool $freeShipping,
        ?GiftProduct $giftProduct = null,
        ?Discount $discount = null
    ) {
        if ($freeShipping || $giftProduct || $discount) {
            $this->freeShipping = $freeShipping;
            $this->giftProduct = $giftProduct;
            $this->discount = $discount;

            return;
        }

        throw new CartRuleConstraintException(
            'Cart rule must have at least one action',
            CartRuleConstraintException::MISSING_ACTION
        );
    }

    public function isFreeShipping(): bool
    {
        return $this->freeShipping;
    }

    public function getGiftProduct(): ?GiftProduct
    {
        return $this->giftProduct;
    }

    public function getDiscount(): ?Discount
    {
        return $this->discount;
    }
}
