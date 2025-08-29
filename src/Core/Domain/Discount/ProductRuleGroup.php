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

namespace PrestaShop\PrestaShop\Core\Domain\Discount;

/**
 * Product rule groups have an AND/&& condition between them, meaning if multiple groups are set
 * on a discount they all must be satisfied for the discount to be valid.
 *
 * Each group is associated with a specific minimum quantity of products that must respect the specified
 * rules. However, the product rules have an OR/|| condition between them so the minimum quantity must
 * match one or several rules defined.
 */
class ProductRuleGroup
{
    /**
     * @param int $quantity
     * @param ProductRule[] $rules
     * @param ProductRuleGroupType $type
     */
    public function __construct(
        private readonly int $quantity,
        private readonly array $rules,
        private readonly ProductRuleGroupType $type = ProductRuleGroupType::AT_LEAST_ONE_PRODUCT_RULE,
    ) {
    }

    public function getQuantity(): int
    {
        return $this->quantity;
    }

    /**
     * @return ProductRule[]
     */
    public function getRules(): array
    {
        return $this->rules;
    }

    public function getType(): ProductRuleGroupType
    {
        return $this->type;
    }
}
