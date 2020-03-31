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

namespace PrestaShop\PrestaShop\Core\Grid\Query;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Query\QueryBuilder;
use PDO;
use PrestaShop\PrestaShop\Core\Grid\Search\SearchCriteriaInterface;

/**
 * Builds SQL queries using Doctrine for retrieving data for orders grid
 */
final class OrderQueryBuilder implements DoctrineQueryBuilderInterface
{
    /**
     * @var Connection
     */
    protected $connection;

    /**
     * @var string
     */
    protected $dbPrefix;

    /**
     * @var int
     */
    private $contextLangId;

    /**
     * @var DoctrineSearchCriteriaApplicatorInterface
     */
    private $criteriaApplicator;
    /**
     * @var array
     */
    private $contextShopIds;

    /**
     * @param Connection $connection
     * @param string $dbPrefix
     * @param DoctrineSearchCriteriaApplicatorInterface $criteriaApplicator
     * @param int $contextLangId
     * @param int[] $contextShopIds
     */
    public function __construct(
        Connection $connection,
        $dbPrefix,
        DoctrineSearchCriteriaApplicatorInterface $criteriaApplicator,
        $contextLangId,
        array $contextShopIds
    ) {
        $this->connection = $connection;
        $this->dbPrefix = $dbPrefix;
        $this->contextLangId = $contextLangId;
        $this->criteriaApplicator = $criteriaApplicator;
        $this->contextShopIds = $contextShopIds;
    }

    /**
     * {@inheritdoc}
     */
    public function getSearchQueryBuilder(SearchCriteriaInterface $searchCriteria)
    {
        $qb = $this
            ->getBaseQueryBuilder($searchCriteria->getFilters())
            ->addSelect($this->getCustomerField() . ' AS `customer`')
            ->addSelect('o.id_order, o.reference, o.total_paid_tax_incl, os.paid, osl.name AS osname')
            ->addSelect('o.current_state, o.id_customer')
            ->addSelect('cu.`id_customer` IS NULL as `deleted_customer`')
            ->addSelect('os.color, o.payment, s.name AS shop_name')
            ->addSelect('o.date_add, cu.company, cl.name AS country_name, o.invoice_number, o.delivery_number')
        ;

        $this->addNewCustomerField($qb);

        $this->applySorting($qb, $searchCriteria);

        $qb = $this->applyNewCustomerFilter($qb, $searchCriteria->getFilters());

        $this->criteriaApplicator
            ->applyPagination($searchCriteria, $qb)
        ;

        return $qb;
    }

    /**
     * {@inheritdoc}
     */
    public function getCountQueryBuilder(SearchCriteriaInterface $searchCriteria)
    {
        $qb = $this->getBaseQueryBuilder($searchCriteria->getFilters());
        if (isset($searchCriteria->getFilters()['new'])) {
            $this->addNewCustomerField($qb->addSelect('o.id_order as o_id_order'));
            $qb = $this->applyNewCustomerFilter($qb, $searchCriteria->getFilters());
            $qb->select('count(o_id_order)');
        } else {
            $qb->select('count(o.id_order)');
        }

        return $qb;
    }

    /**
     * @param array $filters
     *
     * @return QueryBuilder
     */
    private function getBaseQueryBuilder(array $filters)
    {
        $qb = $this->connection
            ->createQueryBuilder()
            ->from($this->dbPrefix . 'orders', 'o')
            ->leftJoin('o', $this->dbPrefix . 'customer', 'cu', 'o.id_customer = cu.id_customer')
            ->innerJoin('o', $this->dbPrefix . 'address', 'a', 'o.id_address_delivery = a.id_address')
            ->innerJoin('a', $this->dbPrefix . 'country', 'c', 'a.id_country = c.id_country')
            ->innerJoin(
                'c',
                $this->dbPrefix . 'country_lang',
                'cl',
                'c.id_country = cl.id_country AND cl.id_lang = :context_lang_id'
            )
            ->leftJoin('o', $this->dbPrefix . 'order_state', 'os', 'o.current_state = os.id_order_state')
            ->leftJoin(
                'os',
                $this->dbPrefix . 'order_state_lang',
                'osl',
                'os.id_order_state = osl.id_order_state AND osl.id_lang = :context_lang_id'
            )
            ->leftJoin('o', $this->dbPrefix . 'shop', 's', 'o.id_shop = s.id_shop')
            ->andWhere('o.`id_shop` IN (:context_shop_ids)')
            ->setParameter('context_lang_id', $this->contextLangId, PDO::PARAM_INT)
            ->setParameter('context_shop_ids', $this->contextShopIds, Connection::PARAM_INT_ARRAY)
        ;

        $strictComparisonFilters = [
            'id_order' => 'o.id_order',
            'country_name' => 'c.id_country',
            'total_paid_tax_incl' => 'o.total_paid_tax_incl',
            'osname' => 'os.id_order_state',
        ];

        $likeComparisonFilters = [
            'reference' => 'o.`reference`',
            'company' => 'cu.`company`',
            'payment' => 'o.`payment`',
            'customer' => $this->getCustomerField(),
        ];

        $havingLikeComparisonFilters = [];

        $dateComparisonFilters = [
            'date_add' => 'o.`date_add`',
        ];

        foreach ($filters as $filterName => $filterValue) {
            if (isset($strictComparisonFilters[$filterName])) {
                $alias = $strictComparisonFilters[$filterName];

                $qb->andWhere("$alias = :$filterName");
                $qb->setParameter($filterName, $filterValue);

                continue;
            }

            if (isset($likeComparisonFilters[$filterName])) {
                $alias = $likeComparisonFilters[$filterName];

                $qb->andWhere("$alias LIKE :$filterName");
                $qb->setParameter($filterName, '%' . $filterValue . '%');

                continue;
            }

            if (isset($havingLikeComparisonFilters[$filterName])) {
                $alias = $havingLikeComparisonFilters[$filterName];

                $qb->andHaving("$alias LIKE :$filterName");
                $qb->setParameter($filterName, '%' . $filterValue . '%');

                continue;
            }

            if (isset($dateComparisonFilters[$filterName])) {
                $alias = $dateComparisonFilters[$filterName];

                if (isset($filterValue['from'])) {
                    $name = sprintf('%s_from', $filterName);

                    $qb->andWhere("$alias >= :$name");
                    $qb->setParameter($name, sprintf('%s %s', $filterValue['from'], '0:0:0'));
                }

                if (isset($filterValue['to'])) {
                    $name = sprintf('%s_to', $filterName);

                    $qb->andWhere("$alias <= :$name");
                    $qb->setParameter($name, sprintf('%s %s', $filterValue['to'], '23:59:59'));
                }

                continue;
            }
        }

        return $qb;
    }

    /**
     * @param QueryBuilder $qb
     */
    private function addNewCustomerField(QueryBuilder $qb)
    {
        $newCustomerSubSelect = $this->connection
            ->createQueryBuilder()
            ->select('so.id_order')
            ->from($this->dbPrefix . 'orders', 'so')
            ->where('so.id_customer = o.id_customer')
            ->andWhere('so.id_order < o.id_order')
            ->setMaxResults(1)
        ;

        $qb->addSelect('IF ((' . $newCustomerSubSelect->getSQL() . ') > 0, 0, 1) AS new');
    }

    /**
     * @return string
     */
    private function getCustomerField()
    {
        return 'CONCAT(LEFT(cu.`firstname`, 1), \'. \', cu.`lastname`)';
    }

    /**
     * @param QueryBuilder $qb
     * @param array $filters
     *
     * @return QueryBuilder
     */
    private function applyNewCustomerFilter(QueryBuilder $qb, array $filters)
    {
        if (!isset($filters['new'])) {
            return $qb;
        }

        $builder = $this->connection
            ->createQueryBuilder()
            ->select('*')
            ->from('(' . $qb->getSQL() . ') tmp_table')
            ->andWhere('new = :new')
            ->setParameter('new', $filters['new'])
        ;

        foreach ($qb->getParameters() as $name => $previousParam) {
            $builder->setParameter(
                $name,
                $previousParam,
                is_array($previousParam) ? Connection::PARAM_INT_ARRAY : null
            );
        }

        return $builder;
    }

    /**
     * @param QueryBuilder $qb
     * @param SearchCriteriaInterface $criteria
     */
    private function applySorting(QueryBuilder $qb, SearchCriteriaInterface $criteria)
    {
        $sortableFields = [
            'id_order' => 'o.id_order',
            'country_name' => 'c.id_country',
            'total_paid_tax_incl' => 'o.total_paid_tax_incl',
            'reference' => 'o.`reference`',
            'company' => 'cu.`company`',
            'payment' => 'o.`payment`',
            'customer' => 'customer',
            'osname' => 'osl.name',
            'date_add' => 'o.`date_add`',
        ];

        if (isset($sortableFields[$criteria->getOrderBy()])) {
            $qb->orderBy(
                $sortableFields[$criteria->getOrderBy()],
                $criteria->getOrderWay()
            );
        }
    }
}
