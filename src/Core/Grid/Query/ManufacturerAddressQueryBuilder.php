<?php
/**
 * 2007-2019 PrestaShop and Contributors
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
 * @copyright 2007-2019 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShop\PrestaShop\Core\Grid\Query;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Query\QueryBuilder;
use PrestaShop\PrestaShop\Core\Grid\Search\SearchCriteriaInterface;

/**
 * Class ManufacturerAddressQueryBuilder is responsible for building queries for manufacturers addresses grid data.
 */
final class ManufacturerAddressQueryBuilder extends AbstractDoctrineQueryBuilder
{
    /**
     * @var DoctrineSearchCriteriaApplicatorInterface
     */
    private $searchCriteriaApplicator;

    /**
     * @var int
     */
    private $contextLangId;

    /**
     * @param Connection $connection
     * @param string $dbPrefix
     * @param DoctrineSearchCriteriaApplicatorInterface $searchCriteriaApplicator
     * @param int $contextLangId
     */
    public function __construct(
        Connection $connection,
        $dbPrefix,
        DoctrineSearchCriteriaApplicatorInterface $searchCriteriaApplicator,
        $contextLangId
    ) {
        parent::__construct($connection, $dbPrefix);

        $this->searchCriteriaApplicator = $searchCriteriaApplicator;
        $this->contextLangId = $contextLangId;
    }

    /**
     * {@inheritdoc}
     */
    public function getSearchQueryBuilder(SearchCriteriaInterface $searchCriteria)
    {
        $qb = $this->getQueryBuilderByFilters($searchCriteria->getFilters());
        $qb->select('a.id_address, m.name, a.firstname, a.lastname, a.postcode, a.city, cl.name as country');

        $this->searchCriteriaApplicator
            ->applySorting($searchCriteria, $qb)
            ->applyPagination($searchCriteria, $qb)
        ;

        return $qb;
    }

    /**
     * {@inheritdoc}
     */
    public function getCountQueryBuilder(SearchCriteriaInterface $searchCriteria)
    {
        $qb = $this->getQueryBuilderByFilters($searchCriteria->getFilters());
        $qb->select('COUNT(DISTINCT a.`id_address`)');

        return $qb;
    }

    /**
     * Gets query builder with common sql needed for manufacturer addresses grid.
     *
     * @param array $filters
     *
     * @return QueryBuilder
     */
    private function getQueryBuilderByFilters(array $filters)
    {
        $qb = $this->connection
            ->createQueryBuilder()
            ->from($this->dbPrefix . 'address', 'a')
            ->leftJoin(
                'a',
                $this->dbPrefix . 'country_lang',
                'cl',
                'cl.id_country = a.id_country AND cl.id_lang = :lang'
            )
            ->setParameter('lang', $this->contextLangId)
            ->leftJoin(
                'a',
                $this->dbPrefix . 'manufacturer',
                'm', 'm.id_manufacturer = a.id_manufacturer'
            )
            ->andWhere('a.id_customer = 0')
            ->andWhere('a.id_supplier = 0')
            ->andWhere('a.id_warehouse = 0')
            ->andWhere('a.deleted = 0')
        ;
        $this->applyFilters($qb, $filters);

        return $qb;
    }

    /**
     * @param QueryBuilder $qb
     * @param array $filters
     */
    private function applyFilters(QueryBuilder $qb, array $filters)
    {
        $allowedFiltersMap = [
            'id_address' => 'a.id_address',
            'name' => 'm.name',
            'firstname' => 'a.firstname',
            'lastname' => 'a.lastname',
            'postcode' => 'a.postcode',
            'city' => 'a.city',
            'country' => 'a.id_country',
        ];
        $exactMatchingFilters = ['id_address', 'country'];

        foreach ($filters as $filterName => $value) {
            if (!array_key_exists($filterName, $allowedFiltersMap)) {
                continue;
            }

            if (in_array($filterName, $exactMatchingFilters, true)) {
                if (empty($value)) {
                    continue;
                }

                $qb->andWhere($allowedFiltersMap[$filterName] . " = :$filterName")
                    ->setParameter($filterName, $value);

                continue;
            }

            $qb->andWhere($allowedFiltersMap[$filterName] . " LIKE :$filterName")
                ->setParameter($filterName, '%' . $value . '%');
        }
    }
}
