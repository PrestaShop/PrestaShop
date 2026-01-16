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

namespace PrestaShop\PrestaShop\Adapter\Discount\Trait;

use PrestaShop\PrestaShop\Core\Domain\Discount\ProductRuleGroup;

/**
 * We use a trait to check this structure, it avoids adding an extra getter on the CQRS structure that would
 * then be normalized by the API, while still factorizing the code.
 */
trait ProductConditionsTrait
{
    /**
     * @param ProductRuleGroup[] $productConditions
     *
     * @return bool
     */
    public function isSegmentTargeted(array $productConditions): bool
    {
        foreach ($productConditions as $productCondition) {
            if ($productCondition->getQuantity() <= 0) {
                break;
            }

            if (empty($productCondition->getRules())) {
                break;
            }

            foreach ($productCondition->getRules() as $rule) {
                // If at least one product rules target some elements then it is not empty
                // and a segment is targeted
                if (!empty($rule->getItemIds())) {
                    return true;
                }
            }
        }

        return false;
    }
}
