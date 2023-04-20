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

namespace PrestaShop\PrestaShop\Core\Domain\CartRule\QueryResult;

class CartRuleActionForEditing
{
    /**
     * @var bool
     */
    private $freeShipping;

    /**
     * @var CartRuleReductionForEditing
     */
    private $reduction;

    /**
     * @var int|null
     */
    private $giftProductId;

    /**
     * @var int|null
     */
    private $giftCombinationId;

    /**
     * @var string
     */
    private $discountApplicationType;

    public function __construct(
        bool $freeShipping,
        CartRuleReductionForEditing $reduction,
        string $discountApplicationType,
        ?int $giftProductId,
        ?int $giftCombinationId
    ) {
        $this->freeShipping = $freeShipping;
        $this->reduction = $reduction;
        $this->discountApplicationType = $discountApplicationType;
        $this->giftProductId = $giftProductId;
        $this->giftCombinationId = $giftCombinationId;
    }

    /**
     * @return bool
     */
    public function isFreeShipping(): bool
    {
        return $this->freeShipping;
    }

    /**
     * @return CartRuleReductionForEditing
     */
    public function getReduction(): CartRuleReductionForEditing
    {
        return $this->reduction;
    }

    /**
     * @return int|null
     */
    public function getGiftProductId(): ?int
    {
        return $this->giftProductId;
    }

    /**
     * @return int|null
     */
    public function getGiftCombinationId(): ?int
    {
        return $this->giftCombinationId;
    }

    /**
     * @return string
     */
    public function getDiscountApplicationType(): string
    {
        return $this->discountApplicationType;
    }
}
