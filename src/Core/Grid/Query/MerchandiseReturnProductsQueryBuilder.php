<?php
/**
 * 2007-2020 PrestaShop SA and Contributors
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
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
 * needs please refer to https://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2020 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Grid\Query;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Query\QueryBuilder;
use PrestaShop\PrestaShop\Core\Grid\Search\SearchCriteriaInterface;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * Class MerchandiseReturnQueryBuilder builds queries for merchandise returns grid data.
 */
final class MerchandiseReturnProductsQueryBuilder extends AbstractDoctrineQueryBuilder
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
     * @var RequestStack
     */
    private $requestStack;

    /**
     * @param Connection $connection
     * @param $dbPrefix
     * @param DoctrineSearchCriteriaApplicatorInterface $searchCriteriaApplicator
     * @param int $contextLanguageId
     */
    public function __construct(
        Connection $connection,
        $dbPrefix,
        DoctrineSearchCriteriaApplicatorInterface $searchCriteriaApplicator,
        int $contextLanguageId,
        RequestStack $requestStack
    ) {
        parent::__construct($connection, $dbPrefix);
        $this->searchCriteriaApplicator = $searchCriteriaApplicator;
        $this->contextLanguageId = $contextLanguageId;
        $this->requestStack = $requestStack;
    }

    /**
     * {@inheritdoc}
     */
    public function getSearchQueryBuilder(SearchCriteriaInterface $searchCriteria)
    {
        $qb = $this->getMerchandiseReturnProductsQueryBuilder($searchCriteria);
        $qb
            ->select('
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
     * {@inheritdoc}
     */
    public function getCountQueryBuilder(SearchCriteriaInterface $searchCriteria)
    {
        return $this->getMerchandiseReturnProductsQueryBuilder($searchCriteria)
            ->select('COUNT(ord.id_order_detail)');
    }

    /**
     * @param SearchCriteriaInterface $searchCriteria
     *
     * @return QueryBuilder
     */
    private function getMerchandiseReturnProductsQueryBuilder(SearchCriteriaInterface $searchCriteria)
    {
        if (null !== ($request = $this->requestStack->getCurrentRequest())
            && $request->attributes->has('merchandiseReturnId')
        ) {
            $merchandiseReturnId = $request->attributes->get('merchandiseReturnId');
        }

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
            ->where('ord.id_order_return = :order_return_id')
            ->setParameter('context_language_id', $this->contextLanguageId)
            ->setParameter('order_return_id', $merchandiseReturnId);

        $this->applyFilters($searchCriteria->getFilters(), $queryBuilder);

        return $queryBuilder;
    }

    /**
     * Apply filters to merchandise returns query builder.
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
            'merchandiseReturnId'
        ];

        foreach ($filters as $filterName => $filterValue) {
            if (!in_array($filterName, $allowedFilters)) {
                continue;
            }

            if ($filterName === 'merchandiseReturnId') {
                $qb->andWhere('ord.`id_order_return` LIKE :' . $filterName);
                $qb->setParameter($filterName, '%' . $filterValue . '%');
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

            $qb->andWhere('od.`' . $filterName . '` LIKE :' . $filterName);
            $qb->setParameter($filterName, '%' . $filterValue . '%');
        }
    }
}
