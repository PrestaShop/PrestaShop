<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
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
