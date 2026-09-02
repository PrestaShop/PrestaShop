<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Discount;

/**
 * Product rule group can have different type of handling their list of product rules:
 *  - AT_LEAST_ONE_PRODUCT_RULE: only one of the product rule needs to be valid for the product rule group to be valid
 *  - ALL_PRODUCT_RULES: all the product rules must be valid, or the product rule group is not valid
 */
enum ProductRuleGroupType: string
{
    case AT_LEAST_ONE_PRODUCT_RULE = 'at_least_one_product_rule';
    case ALL_PRODUCT_RULES = 'all_product_rules';
}
