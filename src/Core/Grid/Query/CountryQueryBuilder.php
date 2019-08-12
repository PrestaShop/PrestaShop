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
 * Builds search & count queries for countries grid.
 */
final class CountryQueryBuilder extends AbstractDoctrineQueryBuilder
{
    /**
     * @var DoctrineSearchCriteriaApplicatorInterface
     */
    private $searchCriteriaApplicator;

    /**
     * @var int
     */
    private $employeeIdLang;

    /**
     * @param Connection $connection
     * @param string $dbPrefix
     * @param DoctrineSearchCriteriaApplicatorInterface $searchCriteriaApplicator
     * @param int $employeeIdLang
     */
    public function __construct(
        Connection $connection,
        string $dbPrefix,
        DoctrineSearchCriteriaApplicatorInterface $searchCriteriaApplicator,
        int $employeeIdLang
    ) {
        parent::__construct($connection, $dbPrefix);

        $this->searchCriteriaApplicator = $searchCriteriaApplicator;
        $this->employeeIdLang = $employeeIdLang;
    }

    /**
     * {@inheritdoc}
     */
    public function getSearchQueryBuilder(SearchCriteriaInterface $searchCriteria): QueryBuilder
    {
        $qb = $this->getQueryBuilder($searchCriteria->getFilters());
        $qb
            ->select('c.`id_country`, c.`iso_code`, c.`call_prefix`, c.`id_zone`, c.`active`')
            ->addSelect('cl.`name` as country_name, z.`name` as zone_name')
        ;

        $this->searchCriteriaApplicator
            ->applyPagination($searchCriteria, $qb)
            ->applySorting($searchCriteria, $qb)
        ;

        return $qb;
    }

    /**
     * {@inheritdoc}
     */
    public function getCountQueryBuilder(SearchCriteriaInterface $searchCriteria): QueryBuilder
    {
        $qb = $this->getQueryBuilder($searchCriteria->getFilters())
            ->select('COUNT(DISTINCT c.`id_country`)')
        ;

        return $qb;
    }

    /**
     * Gets query builder with the common sql used for displaying countries list and applying filter actions.
     *
     * @param array $filters
     *
     * @return QueryBuilder
     */
    private function getQueryBuilder(array $filters): QueryBuilder
    {
        $qb = $this->connection
            ->createQueryBuilder()
            ->from($this->dbPrefix . 'country', 'c');

        $qb->leftJoin(
            'c',
            $this->dbPrefix . 'zone',
            'z',
            'c.`id_zone` = z.`id_zone`'
        );

        $qb->leftJoin(
            'c',
            $this->dbPrefix . 'country_lang',
            'cl',
            'c.`id_country` = cl.`id_country` AND cl.`id_lang` = :idLang '
        );

        $qb->setParameter('idLang', $this->employeeIdLang);
        $this->applyFilters($qb, $filters);

        return $qb;
    }

    /**
     * Apply filters to state query builder.
     *
     * @param array $filters
     * @param QueryBuilder $qb
     */
    private function applyFilters(QueryBuilder $qb, array $filters)
    {
        $allowedFiltersMap = [
            'id_country' => 'c.id_country',
            'country_name' => 'cl.name',
            'iso_code' => 'c.iso_code',
            'id_zone' => 'c.id_zone',
            'call_prefix' => 'c.call_prefix',
            'active' => 's.active',
        ];

        foreach ($filters as $filterName => $value) {
            if (!array_key_exists($filterName, $allowedFiltersMap) || empty($value)) {
                continue;
            }

            if ('country_name' === $filterName || 'iso_code' === $filterName || 'call_prefix' === $filterName) {
                $qb->andWhere($allowedFiltersMap[$filterName] . ' LIKE :' . $filterName)
                    ->setParameter($filterName, '%' . $value . '%');

                continue;
            }

            $qb->andWhere($allowedFiltersMap[$filterName] . ' = :' . $filterName);
            $qb->setParameter($filterName, $value);
        }
    }
}
