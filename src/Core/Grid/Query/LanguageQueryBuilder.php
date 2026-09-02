<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Grid\Query;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Query\QueryBuilder;
use PrestaShop\PrestaShop\Core\Grid\Search\SearchCriteriaInterface;

/**
 * Class LanguageQueryBuilder provides query builders for languages grid.
 */
final class LanguageQueryBuilder extends AbstractDoctrineQueryBuilder
{
    /**
     * @var DoctrineSearchCriteriaApplicatorInterface
     */
    private $searchCriteriaApplicator;

    /**
     * @param Connection $connection
     * @param string $dbPrefix
     * @param DoctrineSearchCriteriaApplicatorInterface $searchCriteriaApplicator
     */
    public function __construct(
        Connection $connection,
        $dbPrefix,
        DoctrineSearchCriteriaApplicatorInterface $searchCriteriaApplicator
    ) {
        parent::__construct($connection, $dbPrefix);

        $this->searchCriteriaApplicator = $searchCriteriaApplicator;
    }

    /**
     * {@inheritdoc}
     */
    public function getSearchQueryBuilder(SearchCriteriaInterface $searchCriteria)
    {
        $builder = $this->getLanguageQueryBuilder($searchCriteria)
            ->select('l.*');

        $this->searchCriteriaApplicator
            ->applySorting($searchCriteria, $builder)
            ->applyPagination($searchCriteria, $builder);

        return $builder;
    }

    /**
     * {@inheritdoc}
     */
    public function getCountQueryBuilder(SearchCriteriaInterface $searchCriteria)
    {
        return $this->getLanguageQueryBuilder($searchCriteria)->select('COUNT(id_lang)');
    }

    /**
     * @param SearchCriteriaInterface $searchCriteria
     *
     * @return QueryBuilder
     */
    private function getLanguageQueryBuilder(SearchCriteriaInterface $searchCriteria)
    {
        $builder = $this->connection->createQueryBuilder()
            ->from($this->dbPrefix . 'lang', 'l');

        $this->applyFilters($builder, $searchCriteria);

        return $builder;
    }

    /**
     * @param QueryBuilder $builder
     * @param SearchCriteriaInterface $searchCriteria
     */
    private function applyFilters(QueryBuilder $builder, SearchCriteriaInterface $searchCriteria)
    {
        $allowedFilters = [
            'id_lang',
            'name',
            'iso_code',
            'language_code',
            'locale',
            'date_format_lite',
            'date_format_full',
            'active',
        ];

        foreach ($searchCriteria->getFilters() as $filterName => $filterValue) {
            if (!in_array($filterName, $allowedFilters)) {
                continue;
            }

            if (in_array($filterName, ['id_lang', 'active'])) {
                $builder->andWhere($filterName . ' = :' . $filterName);
                $builder->setParameter($filterName, $filterValue);

                continue;
            }

            $builder->andWhere($filterName . ' LIKE :' . $filterName);
            $builder->setParameter($filterName, '%' . $filterValue . '%');
        }
    }
}
