<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Grid\Query;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Query\QueryBuilder;
use PrestaShop\PrestaShop\Core\Context\LanguageContext;
use PrestaShop\PrestaShop\Core\Grid\Search\SearchCriteriaInterface;

/**
 * Query builder builds search & count queries for tax rule grid.
 */
class TaxRuleQueryBuilder extends AbstractDoctrineQueryBuilder
{
    /**
     * @var DoctrineSearchCriteriaApplicatorInterface
     */
    private $searchCriteriaApplicator;

    /**
     * @param Connection $connection
     * @param string $dbPrefix
     * @param DoctrineSearchCriteriaApplicatorInterface $searchCriteriaApplicator
     * @param LanguageContext $languageContext
     */
    public function __construct(
        Connection $connection,
        $dbPrefix,
        DoctrineSearchCriteriaApplicatorInterface $searchCriteriaApplicator,
        private LanguageContext $languageContext
    ) {
        parent::__construct($connection, $dbPrefix);

        $this->searchCriteriaApplicator = $searchCriteriaApplicator;
    }

    /**
     * {@inheritdoc}
     */
    public function getSearchQueryBuilder(SearchCriteriaInterface $searchCriteria)
    {
        $qb = $this->getQueryBuilder($searchCriteria->getFilters());

        $qb
            ->select([
                'tr.`id_tax_rule`',
                'tr.`description`',
                'cl.`name` AS country_name',
                'IFNULL(s.`name`, \'--\') AS state_name',
                'CASE '
                    . ' WHEN CONCAT_WS(\' - \', tr.`zipcode_from`, tr.`zipcode_to`) = \'0 - 0\''
                    . ' THEN \'--\' ELSE CONCAT_WS(\' - \', tr.`zipcode_from`, tr.`zipcode_to`)'
                . ' END AS zipcode',
                'tr.behavior',
                't.rate',
            ])
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
        return $this
            ->getQueryBuilder($searchCriteria->getFilters())
            ->select('COUNT(DISTINCT tr.`id_tax_rule`)');
    }

    /**
     * Gets query builder with the common sql used for displaying tax rule groups list and applying filter actions.
     *
     * @param array $filters
     *
     * @return QueryBuilder
     */
    private function getQueryBuilder(array $filters): QueryBuilder
    {
        return $this->connection
            ->createQueryBuilder()
            ->from($this->dbPrefix . 'tax_rule', 'tr')
            ->leftJoin(
                'tr',
                $this->dbPrefix . 'country',
                'c',
                'tr.`id_country` = c.`id_country`'
            )
            ->leftJoin(
                'tr',
                $this->dbPrefix . 'country_lang',
                'cl',
                'tr.`id_country` = cl.`id_country` AND cl.`id_lang` = :idLang '
            )
            ->leftJoin(
                'tr',
                $this->dbPrefix . 'state',
                's',
                'tr.`id_country` = s.`id_country` AND tr.`id_state` = s.`id_state`'
            )
            ->leftJoin(
                'tr',
                $this->dbPrefix . 'tax',
                't',
                'tr.`id_tax` = t.`id_tax`'
            )
            ->andWhere('tr.`id_tax_rules_group` = :idTaxRulesGroup')
            ->setParameter('idLang', $this->languageContext->getId())
            ->setParameter('idTaxRulesGroup', $filters['taxRulesGroupId']);
    }
}
