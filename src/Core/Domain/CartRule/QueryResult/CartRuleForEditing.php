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

use DateTime;
use PrestaShop\PrestaShop\Core\Domain\CartRule\ValueObject\CartRuleId;
use PrestaShopBundle\ApiPlatform\Resources\CartRuleAction;
use PrestaShopBundle\ApiPlatform\Resources\CartRuleMinimumAmount;

/**
 * Provides data for editing CatalogPriceRule
 */
class CartRuleForEditing
{
    /**
     * @var CartRuleId
     */
    private $cartRuleId;

    /**
     * @var CartRuleInformationForEditing
     */
    private $information;

    /**
     * @var CartRuleConditionsForEditing
     */
    private $conditions;

    /**
     * @var CartRuleActionForEditing
     */
    private $actions;

    /**
     * @var DateTime|null
     */
    private $dateAdd;

    /**
     * @var DateTime|null
     */
    private $dateUpd;

    public function __construct(
        CartRuleId $cartRuleId,
        CartRuleInformationForEditing $information,
        CartRuleConditionsForEditing $conditions,
        CartRuleActionForEditing $actions,
        ?DateTime $dateAdd,
        ?DateTime $dateUpd
    ) {
        $this->cartRuleId = $cartRuleId;
        $this->information = $information;
        $this->conditions = $conditions;
        $this->actions = $actions;
        $this->dateAdd = $dateAdd;
        $this->dateUpd = $dateUpd;
    }

    /**
     * @return CartRuleId
     */
    public function getCartRuleId(): CartRuleId
    {
        return $this->cartRuleId;
    }

    /**
     * @return CartRuleInformationForEditing
     */
    public function getInformation(): CartRuleInformationForEditing
    {
        return $this->information;
    }

    /**
     * @return CartRuleConditionsForEditing
     */
    public function getConditions(): CartRuleConditionsForEditing
    {
        return $this->conditions;
    }

    /**
     * @return CartRuleActionForEditing
     */
    public function getActions(): CartRuleActionForEditing
    {
        return $this->actions;
    }

    /**
     * @return DateTime|null
     */
    public function getDateAdd(): ?DateTime
    {
        return $this->dateAdd;
    }

    /**
     * @return DateTime|null
     */
    public function getDateUpd(): ?DateTime
    {
        return $this->dateUpd;
    }

    /**
     * @return string
     */
    public function getDescription(): string
    {
        return $this->information->getDescription();
    }

    /**
     * @return string
     */
    public function getCode(): string
    {
        return $this->information->getCode();
    }

    /**
     * @return int
     */
    public function getPriority(): int
    {
        return $this->information->getPriority();
    }

    /**
     * @return bool
     */
    public function getAllowPartialUse(): bool
    {
        return $this->information->isPartialUse();
    }

    /**
     * @return bool
     */
    public function getActive(): bool
    {
        return $this->information->isEnabled();
    }

    /**
     * @return array
     */
    public function getLocalizedNames(): array
    {
        return $this->information->getLocalizedNames();
    }

    /**
     * @return bool
     */
    public function getHighlightInCart(): bool
    {
        return $this->information->isHighlight();
    }

    public function getMinimumAmount(): CartRuleMinimumAmount
    {
        return new CartRuleMinimumAmount(
            (float) (string) $this->conditions->getMinimum()->getAmount(),
            $this->conditions->getMinimum()->getCurrencyId(),
            $this->conditions->getMinimum()->isAmountTax(),
            $this->conditions->getMinimum()->isShipping(),
        );
    }

    /**
     * @return bool
     */
    public function getMinimumAmountShippingIncluded(): bool
    {
        return $this->conditions->getMinimum()->isShipping();
    }

    /**
     * @return int
     */
    public function getCustomerId(): int
    {
        return $this->conditions->getCustomerId()->getValue();
    }

    /**
     * @return array
     */
    public function getValidityDateRange(): array
    {
        return [
            $this->getConditions()->getDateFrom(),
            $this->getConditions()->getDateTo(),
        ];
    }

    /**
     * @return int
     */
    public function getTotalQuantity(): int
    {
        return $this->conditions->getQuantity();
    }

    /**
     * @return int
     */
    public function getQuantityPerUser(): int
    {
        return $this->conditions->getQuantityPerUser();
    }

    public function getCartRuleAction(): CartRuleAction
    {
        return new CartRuleAction(
            $this->getActions()->isFreeShipping(),
            $this->getActions()->getReduction(),
            $this->getActions()->getGiftProductId(),
            $this->getActions()->getGiftCombinationId(),
            $this->getActions()->getDiscountApplicationType(),
        );
    }
}
