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
 * Builds search and count query builders for the quick access grid.
 * Grid is ordered alphabetically by name (ticket requirement).
 */
class QuickAccessQueryBuilder extends AbstractDoctrineQueryBuilder
{
    private DoctrineSearchCriteriaApplicatorInterface $searchCriteriaApplicator;

    public function __construct(
        Connection $connection,
        string $dbPrefix,
        DoctrineSearchCriteriaApplicatorInterface $searchCriteriaApplicator,
        private readonly LanguageContext $languageContext
    ) {
        parent::__construct($connection, $dbPrefix);
        $this->searchCriteriaApplicator = $searchCriteriaApplicator;
    }

    public function getSearchQueryBuilder(SearchCriteriaInterface $searchCriteria): QueryBuilder
    {
        $qb = $this->getBaseQueryBuilder($searchCriteria)
            ->select('q.id_quick_access, ql.name, q.link, q.new_window');

        $this->searchCriteriaApplicator
            ->applySorting($searchCriteria, $qb)
            ->applyPagination($searchCriteria, $qb);

        return $qb;
    }

    public function getCountQueryBuilder(SearchCriteriaInterface $searchCriteria): QueryBuilder
    {
        return $this->getBaseQueryBuilder($searchCriteria)
            ->select('COUNT(DISTINCT q.id_quick_access)');
    }

    private function getBaseQueryBuilder(SearchCriteriaInterface $searchCriteria): QueryBuilder
    {
        $qb = $this->connection->createQueryBuilder()
            ->from($this->dbPrefix . 'quick_access', 'q')
            ->innerJoin(
                'q',
                $this->dbPrefix . 'quick_access_lang',
                'ql',
                'q.id_quick_access = ql.id_quick_access AND ql.id_lang = :contextLangId'
            )
            ->setParameter('contextLangId', $this->languageContext->getId());

        $this->applyFilters($qb, $searchCriteria);

        return $qb;
    }

    private function applyFilters(QueryBuilder $qb, SearchCriteriaInterface $searchCriteria): void
    {
        $allowedFilters = ['id_quick_access', 'name', 'link', 'new_window'];

        foreach ($searchCriteria->getFilters() as $filterName => $filterValue) {
            if (!in_array($filterName, $allowedFilters)) {
                continue;
            }

            if (in_array($filterName, ['id_quick_access', 'new_window'])) {
                $qb->andWhere('q.' . $filterName . ' = :' . $filterName)
                    ->setParameter($filterName, $filterValue);
                continue;
            }

            if ($filterName === 'name') {
                $qb->andWhere('ql.name LIKE :name')
                    ->setParameter('name', '%' . $filterValue . '%');
                continue;
            }

            if ($filterName === 'link') {
                $qb->andWhere('q.link LIKE :link')
                    ->setParameter('link', '%' . $filterValue . '%');
            }
        }
    }
}
