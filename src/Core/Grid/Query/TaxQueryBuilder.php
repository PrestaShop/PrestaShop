<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Grid\Query;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Query\QueryBuilder;
use PrestaShop\PrestaShop\Core\Context\LanguageContext;
use PrestaShop\PrestaShop\Core\Grid\Search\SearchCriteriaInterface;

/**
 * Class TaxQueryBuilder builds search & count queries for taxes grid.
 */
final class TaxQueryBuilder extends AbstractDoctrineQueryBuilder
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
            ->select('t.`id_tax`, tl.`name`, t.`rate`, t.`active`')
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
            ->select('COUNT(DISTINCT t.`id_tax`)')
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
    private function getQueryBuilder(array $filters)
    {
        $qb = $this->connection
            ->createQueryBuilder()
            ->from($this->dbPrefix . 'tax', 't')
            ->leftJoin(
                't',
                $this->dbPrefix . 'tax_lang',
                'tl',
                't.`id_tax` = tl.`id_tax`'
            );
        $qb->andWhere('tl.`id_lang` = :employee_id_lang');
        $qb->andWhere('t.`deleted` = 0');

        $qb->setParameter('employee_id_lang', $this->languageContext->getId());
        $this->applyFilters($qb, $filters);

        return $qb;
    }

    private function applyFilters(QueryBuilder $qb, array $filters)
    {
        $allowedFiltersMap = [
            'id_tax' => 't.id_tax',
            'name' => 'tl.name',
            'rate' => 't.rate',
            'active' => 't.active',
        ];

        foreach ($filters as $filterName => $value) {
            if (!array_key_exists($filterName, $allowedFiltersMap)) {
                continue;
            }

            if ('active' === $filterName || 'id_tax' === $filterName) {
                $qb->andWhere($allowedFiltersMap[$filterName] . ' = :' . $filterName);
                $qb->setParameter($filterName, $value);

                continue;
            }

            $qb->andWhere($allowedFiltersMap[$filterName] . ' LIKE :' . $filterName)
                ->setParameter($filterName, '%' . $value . '%');
        }
    }
}
