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
use PrestaShop\PrestaShop\Core\Grid\Search\SearchCriteriaInterface;

final class CombinationQueryBuilder extends AbstractDoctrineQueryBuilder
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
        $qb = $this->getCombinationsQueryBuilder($searchCriteria)->select('pa.*');

        $this->searchCriteriaApplicator
            ->applyPagination($searchCriteria, $qb)
            ->applySorting($searchCriteria, $qb)
        ;

        return $qb;
    }

    /**
     * {@inheritdoc}
     */
    public function getCountQueryBuilder(SearchCriteriaInterface $searchCriteria)
    {
        return $this->getCombinationsQueryBuilder($searchCriteria)
            ->select('COUNT(pa.id_product_attribute)')
        ;
    }

    /**
     * @param SearchCriteriaInterface $searchCriteria
     *
     * @return QueryBuilder
     */
    private function getCombinationsQueryBuilder(SearchCriteriaInterface $searchCriteria): QueryBuilder
    {
        $filters = $searchCriteria->getFilters();
        if (!isset($filters['product_id'])) {
            //@todo: better exception? or require filter in another layer - like builder for filters? (categories has same issue)
            throw new \RuntimeException('Product id is required for combinations grid');
        }

        $productId = (int) $filters['product_id'];

        $qb = $this->connection->createQueryBuilder();
        $qb->from($this->dbPrefix . 'product_attribute', 'pa')
            ->where('pa.id_product = :productId')
            ->setParameter('productId', $productId)
        ;

        // filter by attributes
        if (isset($filters['attribute_ids'])) {
            $combinationIds = $this->getCombinationIdsByAttributeIds($productId, (array) $filters['attribute_ids']);
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

        return $qb;
    }

    /**
     * @param int $productId
     * @param int[] $attributeIds
     *
     * @return int[]
     */
    private function getCombinationIdsByAttributeIds(int $productId, array $attributeIds): array
    {
        $qb = $this->connection->createQueryBuilder();
        $qb->select('pac.id_product_attribute')
            ->from($this->dbPrefix . 'product_attribute_combination', 'pac')
            ->leftJoin(
                'pac',
                $this->dbPrefix . 'product_attribute',
                'pa',
                'pac.id_product_attribute = pa.id_product_attribute'
            )
            ->where('pa.id_product = :productId')
            ->setParameter('productId', $productId)
            ->andWhere($qb->expr()->in('pac.id_attribute', ':attributeIds'))
            ->setParameter('attributeIds', $attributeIds, Connection::PARAM_INT_ARRAY)
            ->groupBy('pac.id_product_attribute')
        ;

        $results = $qb->execute()->fetchAll();

        if (!$results) {
            return [];
        }

        return array_map(function (array $result): int {
            return (int) $result['id_product_attribute'];
        }, $results);
    }
}
