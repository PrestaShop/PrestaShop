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
 * Builds search & count queries for tax rule grid for tax rules group edit page.
 */
final class TaxRuleQueryBuilder extends AbstractDoctrineQueryBuilder
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
     * @param $dbPrefix
     * @param DoctrineSearchCriteriaApplicatorInterface $searchCriteriaApplicator
     * @param $employeeIdLang
     */
    public function __construct(
        Connection $connection,
        $dbPrefix,
        DoctrineSearchCriteriaApplicatorInterface $searchCriteriaApplicator,
        $employeeIdLang
    ) {
        parent::__construct($connection, $dbPrefix);

        $this->searchCriteriaApplicator = $searchCriteriaApplicator;
        $this->employeeIdLang = $employeeIdLang;
    }

    /**
     * {@inheritdoc}
     */
    public function getSearchQueryBuilder(SearchCriteriaInterface $searchCriteria)
    {
        $qb = $this->getQueryBuilder($searchCriteria->getFilters());

        $qb
            ->select('cl.`name` as country_name, s.`name` as state_name')
            ->addSelect('tr.`id_tax_rule`, tr.`zipcode_from`, tr.`zipcode_to`')
            ->addSelect('CONCAT(t.`rate`, "%") as rate, tr.`behavior`, tr.`description`')
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
    public function getCountQueryBuilder(SearchCriteriaInterface $searchCriteria)
    {
        $qb = $this->getQueryBuilder($searchCriteria->getFilters())
            ->select('COUNT(DISTINCT tr.`id_tax_rule`)')
        ;

        return $qb;
    }

    /**
     * Gets query builder with the common sql used for displaying webservice list and applying filter actions.
     *
     * @param array $filters
     *
     * @return QueryBuilder
     */
    private function getQueryBuilder(array $filters): QueryBuilder
    {
        $qb = $this->connection
            ->createQueryBuilder()
            ->from($this->dbPrefix . 'tax_rule', 'tr')
            ->leftJoin(
                'tr',
                $this->dbPrefix . 'country_lang',
                'cl',
                'tr.`id_country` = cl.`id_country` AND cl.`id_lang` = :employeeIdLang'
            )
            ->leftJoin(
                'tr',
                $this->dbPrefix . 'state',
                's',
                'tr.`id_state` = s.`id_state`'
            )
            ->leftJoin(
                'tr',
                $this->dbPrefix . 'tax',
                't',
                'tr.`id_tax` = t.`id_tax`'
            );

        $qb->setParameter('employeeIdLang', $this->employeeIdLang);
        $this->applyFilters($qb, $filters);

        return $qb;
    }

    /**
     * @param QueryBuilder $qb
     * @param array $filters
     */
    private function applyFilters(QueryBuilder $qb, array $filters): void
    {
        $allowedFiltersMap = [
            'tax_rules_group_id' => 'id_tax_rules_group',
        ];

        foreach ($filters as $filterName => $value) {
            if (!array_key_exists($filterName, $allowedFiltersMap)) {
                continue;
            }

            $qb->andWhere($allowedFiltersMap[$filterName] . ' = :' . $filterName);
            $qb->setParameter($filterName, $value);
        }
    }
}
