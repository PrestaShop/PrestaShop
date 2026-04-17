<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Integration\Behaviour\Features\Context\Domain\Product\Combination;

use Behat\Gherkin\Node\TableNode;
use PrestaShop\PrestaShop\Core\Domain\Product\Combination\Query\GetCombinationForEditing;
use PrestaShop\PrestaShop\Core\Domain\Product\Combination\Query\GetCombinationIds;
use PrestaShop\PrestaShop\Core\Domain\Product\Combination\Query\GetEditableCombinationsList;
use PrestaShop\PrestaShop\Core\Domain\Product\Combination\QueryResult\CombinationForEditing;
use PrestaShop\PrestaShop\Core\Domain\Product\Combination\QueryResult\CombinationListForEditing;
use PrestaShop\PrestaShop\Core\Domain\Product\Combination\ValueObject\CombinationId;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopConstraint;
use PrestaShop\PrestaShop\Core\Search\Filters\ProductCombinationFilters;
use Tests\Integration\Behaviour\Features\Context\Domain\Product\AbstractProductFeatureContext;
use Tests\Integration\Behaviour\Features\Context\Util\PrimitiveUtils;

abstract class AbstractCombinationFeatureContext extends AbstractProductFeatureContext
{
    /**
     * @param string $productReference
     * @param int $shopId
     * @param ProductCombinationFilters|null $combinationFilters
     *
     * @return CombinationListForEditing
     */
    protected function getCombinationsList(string $productReference, int $shopId, ?ProductCombinationFilters $combinationFilters = null): CombinationListForEditing
    {
        return $this->getQueryBus()->handle(new GetEditableCombinationsList(
            $this->getSharedStorage()->get($productReference),
            $this->getDefaultLangId(),
            ShopConstraint::shop($shopId),
            $combinationFilters ? $combinationFilters->getLimit() : null,
            $combinationFilters ? $combinationFilters->getOffset() : null,
            $combinationFilters ? $combinationFilters->getOrderBy() : null,
            $combinationFilters ? $combinationFilters->getOrderWay() : null,
            $combinationFilters ? $combinationFilters->getFilters() : []
        ));
    }

    /**
     * @param string $productReference
     * @param int $shopId
     * @param ProductCombinationFilters|null $combinationFilters
     *
     * @return CombinationId[]
     */
    protected function getCombinationIds(string $productReference, int $shopId, ?ProductCombinationFilters $combinationFilters = null): array
    {
        return $this->getQueryBus()->handle(new GetCombinationIds(
            $this->getSharedStorage()->get($productReference),
            ShopConstraint::shop($shopId),
            $combinationFilters ? $combinationFilters->getLimit() : null,
            $combinationFilters ? $combinationFilters->getOffset() : null,
            $combinationFilters ? $combinationFilters->getOrderBy() : null,
            $combinationFilters ? $combinationFilters->getOrderWay() : null,
            $combinationFilters ? $combinationFilters->getFilters() : []
        ));
    }

    /**
     * @param string $combinationReference
     *
     * @return CombinationForEditing
     */
    protected function getCombinationForEditing(string $combinationReference, int $shopId): CombinationForEditing
    {
        return $this->getQueryBus()->handle(new GetCombinationForEditing(
            $this->getSharedStorage()->get($combinationReference),
            ShopConstraint::shop($shopId)
        ));
    }

    /**
     * @param int $productId
     * @param TableNode $tableNode
     *
     * @return ProductCombinationFilters
     */
    protected function buildProductCombinationFiltersForShop(int $productId, TableNode $tableNode, int $shopId): ProductCombinationFilters
    {
        $dataRows = $tableNode->getRowsHash();
        $defaults = ProductCombinationFilters::getDefaults();

        $limit = isset($dataRows['limit']) ? (int) $dataRows['limit'] : $defaults['limit'];
        $offset = isset($dataRows['page']) ? $this->countOffset((int) $dataRows['page'], $limit) : $defaults['offset'];
        $orderBy = isset($dataRows['order by']) ? $this->getDbField($dataRows['order by']) : $defaults['orderBy'];
        $orderWay = isset($dataRows['order way']) ? $this->getDbField($dataRows['order way']) : $defaults['sortOrder'];
        unset($dataRows['limit'], $dataRows['page'], $dataRows['order by'], $dataRows['order way'], $dataRows['criteria']);

        $filters = $defaults['filters'];
        $filters['product_id'] = $productId;
        $filters['shop_id'] = $this->getDefaultShopId();

        foreach ($dataRows as $criteriaField => $criteriaValue) {
            $attributeGroupMatch = preg_match('/attributes\[(.*?)\]/', $criteriaField, $matches) ? $matches[1] : null;
            if (null !== $attributeGroupMatch) {
                $attributeGroupId = $this->getSharedStorage()->get($attributeGroupMatch);
                $attributes = PrimitiveUtils::castStringArrayIntoArray($criteriaValue);
                foreach ($attributes as $attributeRef) {
                    $filters['attributes'][$attributeGroupId][] = $this->getSharedStorage()->get($attributeRef);
                }
            } elseif ('is default' === $criteriaField) {
                $filters[$this->getDbField('is default')] = PrimitiveUtils::castStringBooleanIntoBoolean($dataRows['is default']);
            } else {
                $filters[$this->getDbField($criteriaField)] = $criteriaValue;
            }
        }

        return new ProductCombinationFilters(
            ShopConstraint::shop($shopId),
            [
                'limit' => $limit,
                'offset' => $offset,
                'orderBy' => $orderBy,
                'sortOrder' => $orderWay,
                'filters' => $filters,
            ]
        );
    }

    /**
     * @param string $field
     *
     * @return string
     */
    private function getDbField(string $field): string
    {
        $fieldMap = [
            'impact on price' => 'price',
            'is default' => 'default_on',
        ];

        if (isset($fieldMap[$field])) {
            return $fieldMap[$field];
        }

        return $field;
    }

    /**
     * @param int $page
     * @param int $limit
     *
     * @return int
     */
    private function countOffset(int $page, int $limit): int
    {
        return ($page - 1) * $limit;
    }
}
