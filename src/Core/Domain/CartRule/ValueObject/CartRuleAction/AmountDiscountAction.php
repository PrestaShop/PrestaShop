<?php
/**
 * 2007-2019 PrestaShop and Contributors
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
 * @copyright 2007-2019 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShop\PrestaShop\Core\Domain\CartRule\ValueObject\CartRuleAction;

use PrestaShop\PrestaShop\Core\Domain\CartRule\ValueObject\GiftProduct;
use PrestaShop\PrestaShop\Core\Domain\CartRule\ValueObject\PercentageDiscount;
use PrestaShop\PrestaShop\Core\Domain\ValueObject\MoneyAmount;

/**
 * Cart rule action that gives amount discount.
 * Amount discount must have a money amount and currency.
 * It can optionally have free shipping and gift product.
 * It cannot have percentage discount.
 */
final class AmountDiscountAction implements CartRuleActionInterface
{
    /**
     * @var MoneyAmount
     */
    private $amount;

    /**
     * @var bool
     */
    private $isFreeShipping;

    /**
     * @var GiftProduct|null
     */
    private $giftProduct;

    /**
     * @param MoneyAmount $amount
     * @param bool $isFreeShipping
     * @param GiftProduct|null $giftProduct
     */
    public function __construct(
        MoneyAmount $amount,
        bool $isFreeShipping,
        GiftProduct $giftProduct = null
    ) {
        $this->amount = $amount;
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
        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function getAmountDiscount(): ?MoneyAmount
    {
        return $this->amount;
    }

    /**
     * {@inheritdoc}
     */
    public function getGiftProduct(): ?GiftProduct
    {
        return $this->giftProduct;
    }
}
