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

/**
 * Class OrderReturnQueryBuilder builds queries for order returns grid data.
 */
final class OrderReturnProductsQueryBuilder extends AbstractDoctrineQueryBuilder
{
    /**
     * @var int
     */
    private $contextLanguageId;

    /**
     * @var DoctrineSearchCriteriaApplicatorInterface
     */
    private $searchCriteriaApplicator;

    /**
     * @param Connection $connection
     * @param string $dbPrefix
     * @param DoctrineSearchCriteriaApplicatorInterface $searchCriteriaApplicator
     * @param int $contextLanguageId
     */
    public function __construct(
        Connection $connection,
        string $dbPrefix,
        DoctrineSearchCriteriaApplicatorInterface $searchCriteriaApplicator,
        int $contextLanguageId
    ) {
        parent::__construct($connection, $dbPrefix);
        $this->searchCriteriaApplicator = $searchCriteriaApplicator;
        $this->contextLanguageId = $contextLanguageId;
    }

    /**
     * {@inheritdoc}
     */
    public function getSearchQueryBuilder(SearchCriteriaInterface $searchCriteria)
    {
        $qb = $this->getOrderReturnProductsQueryBuilder($searchCriteria);
        $qb->select('
            ord.id_order_return,
            ord.id_order_detail,
            ord.product_quantity,
            od.product_name,
            od.product_reference,
            od.id_customization,
            o.id_cart'
        )
            ->groupBy('ord.id_order_detail');

        $this->searchCriteriaApplicator
            ->applySorting($searchCriteria, $qb)
            ->applyPagination($searchCriteria, $qb);

        return $qb;
    }

    /**
     * @param SearchCriteriaInterface $searchCriteria
     *
     * @return QueryBuilder
     */
    private function getOrderReturnProductsQueryBuilder(SearchCriteriaInterface $searchCriteria)
    {
        $queryBuilder = $this->connection->createQueryBuilder()
            ->from($this->dbPrefix . 'order_return_detail', 'ord')
            ->leftJoin(
                'ord',
                $this->dbPrefix . 'order_detail',
                'od',
                'ord.id_order_detail = od.id_order_detail'
            )
            ->leftJoin(
                'od',
                $this->dbPrefix . 'orders',
                'o',
                'od.id_order = o.id_order'
            )
            ->setParameter('context_language_id', $this->contextLanguageId);

        $this->applyFilters($searchCriteria->getFilters(), $queryBuilder);

        return $queryBuilder;
    }

    /**
     * Apply filters to order returns query builder.
     *
     * @param array $filters
     * @param QueryBuilder $qb
     */
    private function applyFilters(array $filters, QueryBuilder $qb)
    {
        $allowedFilters = [
            'product_reference',
            'product_name',
            'quantity',
            'customization_name',
            'customization_value',
            'order_return_id',
        ];

        foreach ($filters as $filterName => $filterValue) {
            if (!in_array($filterName, $allowedFilters)) {
                continue;
            }

            if ($filterName === 'order_return_id') {
                $qb->andWhere('ord.`id_order_return`  = :' . $filterName);
                $qb->setParameter($filterName, $filterValue);
                continue;
            }

            if ($filterName === 'customization_name') {
                $qb->andWhere('cfl.`name` LIKE :' . $filterName);
                $qb->setParameter($filterName, '%' . $filterValue . '%');
                continue;
            }

            if ($filterName === 'customization_value') {
                $qb->andWhere('cd.`value` LIKE :' . $filterName);
                $qb->setParameter($filterName, '%' . $filterValue . '%');
                continue;
            }

            if ($filterName === 'product_quantity') {
                $qb->andWhere('od.`product_quantity`  = :' . $filterName);
                $qb->setParameter($filterName, $filterValue);
                continue;
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getCountQueryBuilder(SearchCriteriaInterface $searchCriteria)
    {
        return $this->getOrderReturnProductsQueryBuilder($searchCriteria)
            ->select('COUNT(ord.id_order_detail)');
    }
}
