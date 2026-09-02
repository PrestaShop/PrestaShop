<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */
declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Product\Combination\QueryHandler;

use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsQueryHandler;
use PrestaShop\PrestaShop\Core\Domain\Product\Combination\Query\GetCombinationIds;
use PrestaShop\PrestaShop\Core\Domain\Product\Combination\ValueObject\CombinationId;
use PrestaShop\PrestaShop\Core\Grid\Query\ProductCombinationQueryBuilder;
use PrestaShop\PrestaShop\Core\Search\Filters\ProductCombinationFilters;

#[AsQueryHandler]
class GetCombinationIdsHandler implements GetCombinationIdsHandlerInterface
{
    /**
     * @var ProductCombinationQueryBuilder
     */
    private $productCombinationQueryBuilder;

    /**
     * @param ProductCombinationQueryBuilder $productCombinationQueryBuilder
     */
    public function __construct(
        ProductCombinationQueryBuilder $productCombinationQueryBuilder
    ) {
        $this->productCombinationQueryBuilder = $productCombinationQueryBuilder;
    }

    /**
     * @param GetCombinationIds $query
     *
     * @return CombinationId[]
     */
    public function handle(GetCombinationIds $query): array
    {
        $filters = $query->getFilters();
        $filters['product_id'] = $query->getProductId()->getValue();
        $orderBy = $query->getOrderBy();

        if ('price' === $query->getOrderBy()) {
            // we need to specify alias for price to avoid price being ambiguous in the query
            $orderBy = 'pas.price';
        }

        $searchCriteria = new ProductCombinationFilters(
            $query->getShopConstraint(),
            [
                'limit' => $query->getLimit(),
                'offset' => $query->getOffset(),
                'orderBy' => $orderBy,
                'sortOrder' => $query->getOrderWay(),
                'filters' => $filters,
            ]
        );

        $results = $this->productCombinationQueryBuilder
            ->getSearchQueryBuilder($searchCriteria)
            ->select('pas.id_product_attribute')
            ->executeQuery()
            ->fetchAllAssociative()
        ;

        return array_map(static function (array $result): CombinationId {
            return new CombinationId((int) $result['id_product_attribute']);
        }, $results);
    }
}
