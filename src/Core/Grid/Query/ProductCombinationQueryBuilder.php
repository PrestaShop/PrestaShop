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
use Doctrine\DBAL\Query\QueryBuilder;
use InvalidArgumentException;
use PrestaShop\PrestaShop\Core\Grid\Search\SearchCriteriaInterface;
use PrestaShop\PrestaShop\Core\Search\Filters\ProductCombinationFilters;

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
                    ProductCombinationFilters::class, get_class($searchCriteria)
                )
            );
        }

        $qb = $this->getCombinationsQueryBuilder($searchCriteria)->addSelect('pa.*');

        $this->searchCriteriaApplicator
            ->applyPagination($searchCriteria, $qb)
            ->applySorting($searchCriteria, $qb);

        // Sort by quantity has been added first, this is the second order condition
        if ('quantity' === $searchCriteria->getOrderBy()) {
            $qb->addOrderBy('pa.id_product_attribute', 'asc');
        }

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
                    ProductCombinationFilters::class, get_class($searchCriteria)
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

        $qb = $this->connection->createQueryBuilder();
        $qb->from($this->dbPrefix . 'product_attribute', 'pa')
            ->where('pa.id_product = :productId')
            ->setParameter('productId', $productId)
        ;

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
                $qb->andWhere('pa.default_on = 1');
            } else {
                $qb->andWhere('pa.default_on IS NULL OR pa.default_on = 0');
            }
        }

        if (null === $productCombinationFilters->getOrderBy()) {
            $qb->addOrderBy('id_product_attribute', 'asc');
        } elseif ('quantity' === $productCombinationFilters->getOrderBy()) {
            $qb
                ->addSelect('sa.quantity AS quantity')
                ->innerJoin(
                    'pa',
                    $this->dbPrefix . 'stock_available',
                    'sa',
                    'pa.id_product_attribute = sa.id_product_attribute'
                )
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
        $results = $qb->execute()->fetchAll();
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
