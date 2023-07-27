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

namespace PrestaShop\PrestaShop\Core\Grid\Query;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\ParameterType;
use Doctrine\DBAL\Query\QueryBuilder;
use InvalidArgumentException;
use PrestaShop\PrestaShop\Core\Exception\CoreException;
use PrestaShop\PrestaShop\Core\Grid\Search\SearchCriteriaInterface;
use PrestaShop\PrestaShop\Core\Search\Filters\ProductCombinationFilters;
use PrestaShopException;
use StockAvailable;

final class ProductCombinationQueryBuilder extends AbstractDoctrineQueryBuilder
{
    /**
     * @var DoctrineSearchCriteriaApplicatorInterface
     */
    private $searchCriteriaApplicator;

    /**
     * @param Connection $connection
     * @param string $dbPrefix
     * @param DoctrineSearchCriteriaApplicatorInterface $searchCriteriaApplicator
     */
    public function __construct(
        Connection $connection,
        string $dbPrefix,
        DoctrineSearchCriteriaApplicatorInterface $searchCriteriaApplicator
    ) {
        parent::__construct($connection, $dbPrefix);
        $this->searchCriteriaApplicator = $searchCriteriaApplicator;
    }

    /**
     * {@inheritdoc}
     */
    public function getSearchQueryBuilder(SearchCriteriaInterface $searchCriteria)
    {
        if (!$searchCriteria instanceof ProductCombinationFilters) {
            throw new InvalidArgumentException(
                sprintf(
                    'Expected %s, but got %s',
                    ProductCombinationFilters::class, $searchCriteria::class
                )
            );
        }

        $qb = $this->getCombinationsQueryBuilder($searchCriteria)
            ->addSelect('
                pa.reference, pa.supplier_reference, pa.ean13, pa.isbn, pa.upc, pa.mpn,
                pas.wholesale_price, pas.price, pas.ecotax, pas.weight, pas.unit_price_impact, pas.default_on,
                pas.minimal_quantity, pas.low_stock_threshold, pas.low_stock_alert, pas.available_date,
                pas.id_product_attribute, pas.id_product, pas.id_shop, sa.quantity AS quantity
            ');

        $this->searchCriteriaApplicator
            ->applyPagination($searchCriteria, $qb)
            ->applySorting($searchCriteria, $qb);

        // Always sort by id as the second sorting condition (it also acts as default sorting when no orderBy is provided)
        $qb->addOrderBy('pa.id_product_attribute', 'asc');

        return $qb;
    }

    /**
     * {@inheritdoc}
     */
    public function getCountQueryBuilder(SearchCriteriaInterface $searchCriteria)
    {
        if (!$searchCriteria instanceof ProductCombinationFilters) {
            throw new InvalidArgumentException(
                sprintf(
                    'Expected %s, but got %s',
                    ProductCombinationFilters::class, $searchCriteria::class
                )
            );
        }

        return $this->getCombinationsQueryBuilder($searchCriteria)
            ->select('COUNT(pa.id_product_attribute)')
        ;
    }

    /**
     * @param ProductCombinationFilters $productCombinationFilters
     *
     * @return QueryBuilder
     */
    private function getCombinationsQueryBuilder(ProductCombinationFilters $productCombinationFilters): QueryBuilder
    {
        $filters = $productCombinationFilters->getFilters();
        $productId = $productCombinationFilters->getProductId();
        $shopId = $productCombinationFilters->getShopId();

        $qb = $this->connection->createQueryBuilder();
        $qb->from($this->dbPrefix . 'product_attribute', 'pa')
            ->innerJoin(
                'pa',
                $this->dbPrefix . 'product_attribute_shop',
                'pas',
                'pa.id_product_attribute = pas.id_product_attribute'
            )
            ->leftJoin(
                'pa',
                $this->dbPrefix . 'stock_available',
                'sa',
                'pa.id_product_attribute = sa.id_product_attribute'
            )
            ->where('pas.id_product = :productId')
            ->andWhere('pas.id_shop = :shopId')
            ->setParameter('productId', $productId)
            ->setParameter('shopId', $shopId)
        ;

        $this->addShopCondition($qb, 'sa', $shopId);

        // filter by attributes
        if (isset($filters['attributes'])) {
            $combinationIds = $this->getCombinationIdsByAttributeIds($productId, (array) $filters['attributes']);
            $qb->andWhere($qb->expr()->in('pa.id_product_attribute', ':combinationIds'))
                ->setParameter('combinationIds', $combinationIds, Connection::PARAM_INT_ARRAY)
            ;
        }

        if (isset($filters['reference'])) {
            $qb->andWhere('pa.reference LIKE :reference')
                ->setParameter('reference', '%' . $filters['reference'] . '%')
            ;
        }

        if (isset($filters['default_on'])) {
            if ((bool) $filters['default_on']) {
                $qb->andWhere('pas.default_on = 1');
            } else {
                $qb->andWhere('pas.default_on IS NULL OR pa.default_on = 0');
            }
        }

        return $qb;
    }

    /**
     * Adds "where" condition for shop or shopGroup depending if the shop is in a shop group with shared stock
     * (reusing legacy logic from StockAvailable::addSqlShopParams)
     *
     * @param QueryBuilder $qb
     * @param string $stockAlias
     * @param int $shopId
     *
     * @return QueryBuilder
     */
    private function addShopCondition(QueryBuilder $qb, string $stockAlias, int $shopId): QueryBuilder
    {
        // Use legacy method, it checks if the shop belongs to a ShopGroup that shares stock, in which case the StockAvailable
        // must be assigned to the group not the shop
        $shopParams = [];
        try {
            StockAvailable::addSqlShopParams($shopParams, $shopId);
        } catch (PrestaShopException $e) {
            throw new CoreException('Error occurred when trying to add StockAvailable shop condition', 0, $e);
        }

        foreach ($shopParams as $key => $value) {
            if (!in_array($key, ['id_shop', 'id_shop_group'])) {
                continue;
            }

            $qb->andWhere(sprintf('%s.%s = :%s', $stockAlias, $key, $key))
                ->setParameter($key, $value, ParameterType::INTEGER)
            ;
        }

        return $qb;
    }

    /**
     * @param int $productId
     * @param array<int, int[]> $attributeGroups
     *
     * @return int[]
     */
    private function getCombinationIdsByAttributeIds(int $productId, array $attributeGroups): array
    {
        $qb = $this->connection->createQueryBuilder();

        $allAttributes = [];
        foreach ($attributeGroups as $attributeIds) {
            $allAttributes = array_merge($allAttributes, $attributeIds);
        }
        $qb->select('pac.id_product_attribute, pac.id_attribute')
            ->from($this->dbPrefix . 'product_attribute_combination', 'pac')
            ->leftJoin(
                'pac',
                $this->dbPrefix . 'product_attribute',
                'pa',
                'pac.id_product_attribute = pa.id_product_attribute'
            )
            ->where('pa.id_product = :productId')
            ->andWhere($qb->expr()->in('pac.id_attribute', ':attributes'))
            ->setParameter('attributes', $allAttributes, Connection::PARAM_INT_ARRAY)
            ->setParameter('productId', $productId)
        ;
        $results = $qb->executeQuery()->fetchAllAssociative();
        if (!$results) {
            return [];
        }

        $combinationAttributes = [];
        foreach ($results as $result) {
            $combinationAttributes[(int) $result['id_product_attribute']][] = (int) $result['id_attribute'];
        }

        foreach ($attributeGroups as $groupAttributes) {
            foreach ($combinationAttributes as $combinationId => $attributeIds) {
                if (empty(array_intersect($groupAttributes, $attributeIds))) {
                    unset($combinationAttributes[$combinationId]);
                }
            }
        }

        return empty($combinationAttributes) ? [] : array_keys($combinationAttributes);
    }
}
