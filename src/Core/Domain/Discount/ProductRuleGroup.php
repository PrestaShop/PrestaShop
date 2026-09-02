<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
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
    public static function fromArray(array $data): ProductRuleGroup
    {
        return new ProductRuleGroup(
            $data['quantity'],
            array_map(static function (array $productRule): ProductRule {
                return new ProductRule(
                    ProductRuleType::from($productRule['type']),
                    $productRule['itemIds'] ?? [],
                );
            }, $data['rules'] ?? []),
            isset($data['type']) ? ProductRuleGroupType::from($data['type']) : ProductRuleGroupType::AT_LEAST_ONE_PRODUCT_RULE,
        );
    }

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
