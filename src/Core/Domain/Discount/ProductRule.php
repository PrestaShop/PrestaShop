<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Discount;

/**
 * The product rules have an OR/|| condition between them so the minimum quantity of products (defined on the
 * parent group) must match one or several rules defined.
 *
 * A product rule is defined by the type of entity it matches (products, categories, ...) and a list of item IDs
 * for this specific entity.
 */
class ProductRule
{
    public function __construct(
        private readonly ProductRuleType $type,
        private readonly array $itemIds,
    ) {
    }

    public function getType(): ProductRuleType
    {
        return $this->type;
    }

    public function getItemIds(): array
    {
        return $this->itemIds;
    }
}
