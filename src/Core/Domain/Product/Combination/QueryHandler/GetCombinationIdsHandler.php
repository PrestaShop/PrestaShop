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
 * needs please refer to https://devdocs.prestashop-project.org/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */
declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Product\Combination\QueryHandler;

use PrestaShop\PrestaShop\Core\Domain\Product\Combination\Query\GetCombinationIds;
use PrestaShop\PrestaShop\Core\Domain\Product\Combination\ValueObject\CombinationId;
use PrestaShop\PrestaShop\Core\Grid\Query\ProductCombinationQueryBuilder;
use PrestaShop\PrestaShop\Core\Search\Filters\ProductCombinationFilters;

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
            ->execute()
            ->fetchAllAssociative()
        ;

        return array_map(static function (array $result): CombinationId {
            return new CombinationId((int) $result['id_product_attribute']);
        }, $results);
    }
}
