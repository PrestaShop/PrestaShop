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
                'tr.`id_tax_rules_group`',
                'tr.`description`',
                'tr.`id_country` AS country_id',
                'cl.`name` AS country_name',
                'tr.`id_state` AS state_id',
                'IFNULL(s.`name`, \'--\') AS state_name',
                'CASE '
                    . ' WHEN CONCAT_WS(\' - \', tr.`zipcode_from`, tr.`zipcode_to`) = \'0 - 0\''
                    . ' THEN \'--\' ELSE CONCAT_WS(\' - \', tr.`zipcode_from`, tr.`zipcode_to`)'
                . ' END AS zipcode',
                'tr.behavior',
                't.rate',
                'txl.`name` AS tax_name',
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
        $qb = $this->connection
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
            ->leftJoin(
                't',
                $this->dbPrefix . 'tax_lang',
                'txl',
                't.`id_tax` = txl.`id_tax` AND txl.`id_lang` = :idLang'
            )
            ->setParameter('idLang', $this->languageContext->getId());

        // The tax rules group filter is optional: without it the query lists tax rules across all groups
        $taxRulesGroupId = $filters['taxRulesGroupId'] ?? $filters['id_tax_rules_group'] ?? null;
        if (!empty($taxRulesGroupId)) {
            $qb
                ->andWhere('tr.`id_tax_rules_group` = :idTaxRulesGroup')
                ->setParameter('idTaxRulesGroup', (int) $taxRulesGroupId);
        }

        $this->applyFilters($qb, $filters);

        return $qb;
    }

    /**
     * @param QueryBuilder $qb
     * @param array $filters
     */
    private function applyFilters(QueryBuilder $qb, array $filters): void
    {
        // country_id, country_name, state_name and tax_name match the column aliases so the same field
        // names can be used for both filtering and sorting (needed by the Admin API listing)
        $allowedFilters = ['country', 'country_id', 'country_name', 'state', 'state_name', 'zipcode', 'behavior', 'rate', 'tax_name', 'description'];

        foreach ($filters as $filterName => $filterValue) {
            if (!in_array($filterName, $allowedFilters) || ($filterValue === '' || $filterValue === null)) {
                continue;
            }

            switch ($filterName) {
                case 'country':
                case 'country_name':
                    $qb->andWhere('cl.`name` LIKE :country');
                    $qb->setParameter('country', '%' . $filterValue . '%');
                    break;
                case 'country_id':
                    $qb->andWhere('tr.`id_country` = :countryId');
                    $qb->setParameter('countryId', (int) $filterValue);
                    break;
                case 'state':
                case 'state_name':
                    $qb->andWhere('s.`name` LIKE :state');
                    $qb->setParameter('state', '%' . $filterValue . '%');
                    break;
                case 'tax_name':
                    $qb->andWhere('txl.`name` LIKE :taxName');
                    $qb->setParameter('taxName', '%' . $filterValue . '%');
                    break;
                case 'zipcode':
                    $qb->andWhere('(tr.`zipcode_from` LIKE :zipcode OR tr.`zipcode_to` LIKE :zipcode)');
                    $qb->setParameter('zipcode', '%' . $filterValue . '%');
                    break;
                case 'behavior':
                    $qb->andWhere('tr.`behavior` = :behavior');
                    $qb->setParameter('behavior', (int) $filterValue);
                    break;
                case 'rate':
                    $qb->andWhere('t.`rate` LIKE :rate');
                    $qb->setParameter('rate', '%' . $filterValue . '%');
                    break;
                case 'description':
                    $qb->andWhere('tr.`description` LIKE :description');
                    $qb->setParameter('description', '%' . $filterValue . '%');
                    break;
            }
        }
    }
}
