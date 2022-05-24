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

namespace PrestaShop\PrestaShop\Adapter\Product\QueryHandler;

use PDO;
use PrestaShop\Decimal\DecimalNumber;
use PrestaShop\PrestaShop\Core\Domain\Product\Query\GetLightProductList;
use PrestaShop\PrestaShop\Core\Domain\Product\QueryHandler\GetLightProductListHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Product\QueryResult\LightProductForListing;
use PrestaShop\PrestaShop\Core\Domain\Product\QueryResult\LightProductList;
use PrestaShop\PrestaShop\Core\Grid\Query\DoctrineQueryBuilderInterface;
use PrestaShop\PrestaShop\Core\Grid\Search\SearchCriteria;

class GetLightProductListHandler implements GetLightProductListHandlerInterface
{
    /**
     * @var DoctrineQueryBuilderInterface
     */
    private $lightProductsQueryBuilder;

    /**
     * @param DoctrineQueryBuilderInterface $lightProductsQueryBuilder
     */
    public function __construct(
        DoctrineQueryBuilderInterface $lightProductsQueryBuilder
    ) {
        $this->lightProductsQueryBuilder = $lightProductsQueryBuilder;
    }

    /**
     * @param GetLightProductList $query
     *
     * @return LightProductList
     */
    public function handle(GetLightProductList $query): LightProductList
    {
        $searchCriteria = new SearchCriteria(
            [],
            $query->getOrderBy(),
            $query->getOrderWay(),
            $query->getOffset(),
            $query->getLimit()
        );

        $products = $this->lightProductsQueryBuilder->getSearchQueryBuilder($searchCriteria)->execute()->fetchAll();
        $total = (int) $this->lightProductsQueryBuilder->getCountQueryBuilder($searchCriteria)->execute()->fetch(PDO::FETCH_COLUMN);

        return new LightProductList($this->formatLightProducts($products), $total);
    }

    /**
     * @param array<int, array<string, mixed>> $records
     *
     * @return LightProductForListing[]
     */
    private function formatLightProducts(array $records): array
    {
        $products = [];
        foreach ($records as $record) {
            $products[] = new LightProductForListing(
                (int) $record['id_product'],
                $record['name'],
                new DecimalNumber($record['price_tax_excluded']),
                (int) $record['quantity']
            );
        }

        return $products;
    }
}
