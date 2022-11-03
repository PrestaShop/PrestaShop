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

namespace PrestaShop\PrestaShop\Core\Domain\CartRule\ValueObject\CartRuleAction;

use PrestaShop\PrestaShop\Core\Domain\CartRule\ValueObject\GiftProduct;
use PrestaShop\PrestaShop\Core\Domain\CartRule\ValueObject\MoneyAmountCondition;
use PrestaShop\PrestaShop\Core\Domain\CartRule\ValueObject\PercentageDiscount;

/**
 * Cart rule action that gives percentage discount.
 * Percentage discount must have a percentage set.
 * It can optionally have free shipping and gift product.
 * It cannot have amount discount.
 */
final class PercentageDiscountAction implements CartRuleActionInterface
{
    /**
     * @var PercentageDiscount
     */
    private $percentageDiscount;

    /**
     * @var bool
     */
    private $isFreeShipping;

    /**
     * @var GiftProduct|null
     */
    private $giftProduct;

    /**
     * @param PercentageDiscount $percentageDiscount
     * @param bool $isFreeShipping
     * @param GiftProduct|null $giftProduct
     */
    public function __construct(
        PercentageDiscount $percentageDiscount,
        bool $isFreeShipping,
        GiftProduct $giftProduct = null
    ) {
        $this->percentageDiscount = $percentageDiscount;
        $this->isFreeShipping = $isFreeShipping;
        $this->giftProduct = $giftProduct;
    }

    /**
     * {@inheritdoc}
     */
    public function isFreeShipping(): bool
    {
        return $this->isFreeShipping;
    }

    /**
     * {@inheritdoc}
     */
    public function getPercentageDiscount(): ?PercentageDiscount
    {
        return $this->percentageDiscount;
    }

    /**
     * {@inheritdoc}
     */
    public function getAmountDiscount(): ?MoneyAmountCondition
    {
        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function getGiftProduct(): ?GiftProduct
    {
        return $this->giftProduct;
    }
}
