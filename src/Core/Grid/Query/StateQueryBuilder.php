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
 * Builds search & count queries for states grid.
 */
class StateQueryBuilder extends AbstractDoctrineQueryBuilder
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
        string $dbPrefix,
        DoctrineSearchCriteriaApplicatorInterface $searchCriteriaApplicator,
        private LanguageContext $languageContext
    ) {
        parent::__construct($connection, $dbPrefix);
        $this->searchCriteriaApplicator = $searchCriteriaApplicator;
    }

    /**
     * {@inheritdoc}
     */
    public function getSearchQueryBuilder(SearchCriteriaInterface $searchCriteria): QueryBuilder
    {
        $qb = $this->getQueryBuilder($searchCriteria->getFilters());
        $qb
            ->select(
                's.`id_state`',
                's.`name`',
                's.`iso_code`',
                's.`id_country`',
                's.`id_zone`',
                's.`active`',
                'cl.`name` as country_name',
                'z.`name` as zone_name'
            )
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
        return $this->getQueryBuilder($searchCriteria->getFilters())
            ->select('COUNT(DISTINCT s.`id_state`)');
    }

    /**
     * Gets query builder with the common sql used for displaying states list and applying filter actions.
     *
     * @param array $filters
     *
     * @return QueryBuilder
     */
    private function getQueryBuilder(array $filters): QueryBuilder
    {
        $qb = $this->connection
            ->createQueryBuilder()
            ->from($this->dbPrefix . 'state', 's');

        $qb->leftJoin(
            's',
            $this->dbPrefix . 'zone',
            'z',
            's.`id_zone` = z.`id_zone`'
        );

        $qb->leftJoin(
            's',
            $this->dbPrefix . 'country',
            'c',
            's.`id_country` = c.`id_country`'
        );

        $qb->leftJoin(
            's',
            $this->dbPrefix . 'country_lang',
            'cl',
            's.`id_country` = cl.`id_country` AND cl.`id_lang` = :idLang '
        );

        $qb->setParameter('idLang', $this->languageContext->getId());
        $this->applyFilters($qb, $filters);

        return $qb;
    }

    /**
     * Apply filters to state query builder.
     *
     * @param array $filters
     * @param QueryBuilder $qb
     */
    private function applyFilters(QueryBuilder $qb, array $filters): void
    {
        $allowedFiltersMap = [
            'id_state' => 's.id_state',
            'name' => 's.name',
            'iso_code' => 's.iso_code',
            'id_zone' => 's.id_zone',
            'id_country' => 's.id_country',
            'active' => 's.active',
        ];

        foreach ($filters as $filterName => $value) {
            if (!array_key_exists($filterName, $allowedFiltersMap)) {
                continue;
            }

            if (('name' === $filterName || 'iso_code' === $filterName) && !empty($value)) {
                $qb->andWhere($allowedFiltersMap[$filterName] . ' LIKE :' . $filterName)
                    ->setParameter($filterName, '%' . $value . '%');

                continue;
            }

            $qb->andWhere($allowedFiltersMap[$filterName] . ' = :' . $filterName);
            $qb->setParameter($filterName, $value);
        }
    }
}
