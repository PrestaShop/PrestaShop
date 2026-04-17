<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Grid\Query;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Query\QueryBuilder;
use PrestaShop\PrestaShop\Core\Exception\InvalidArgumentException;
use PrestaShop\PrestaShop\Core\Grid\Search\SearchCriteriaInterface;
use PrestaShop\PrestaShop\Core\Search\Filters\FeatureValueFilters;

class FeatureValueQueryBuilder extends AbstractDoctrineQueryBuilder
{
    public function __construct(
        Connection $connection,
        string $dbPrefix,
        protected readonly DoctrineSearchCriteriaApplicatorInterface $searchCriteriaApplicator
    ) {
        parent::__construct($connection, $dbPrefix);
    }

    /**
     * {@inheritdoc}
     */
    public function getSearchQueryBuilder(SearchCriteriaInterface $searchCriteria): QueryBuilder
    {
        $qb = $this->getQueryBuilder($searchCriteria)
            ->select('fv.id_feature, fv.id_feature_value, fvl.value, fv.position')
            ->groupBy('fv.id_feature_value')
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
        return $this->getQueryBuilder($searchCriteria)
            ->select('COUNT(DISTINCT fv.id_feature_value)')
        ;
    }

    /**
     * @param SearchCriteriaInterface $searchCriteria
     *
     * @return QueryBuilder
     *
     * @throws InvalidArgumentException
     */
    private function getQueryBuilder(SearchCriteriaInterface $searchCriteria): QueryBuilder
    {
        if (!$searchCriteria instanceof FeatureValueFilters) {
            throw new InvalidArgumentException(sprintf('Invalid search criteria type. Expected "%s"', FeatureValueFilters::class));
        }

        $filters = $searchCriteria->getFilters();
        $allowedFilters = ['id_feature_value', 'value', 'position'];

        $qb = $this->connection
            ->createQueryBuilder()
            ->from($this->dbPrefix . 'feature_value', 'fv')
            ->where('fv.id_feature = :featureId')
            ->setParameter('featureId', $searchCriteria->getFeatureId())
            ->leftJoin(
                'fv',
                $this->dbPrefix . 'feature_value_lang',
                'fvl',
                'fv.id_feature_value = fvl.id_feature_value AND fvl.id_lang = :langId'
            )
            ->setParameter('langId', $searchCriteria->getLanguageId())
        ;

        foreach ($filters as $filterName => $value) {
            if (!in_array($filterName, $allowedFilters, true)) {
                continue;
            }

            if ('value' === $filterName) {
                $qb->andWhere('fvl.value LIKE :' . $filterName)
                    ->setParameter($filterName, '%' . $value . '%');
                continue;
            }

            if ('position' === $filterName) {
                // Position in DB is 0-based, but in UI it is 1-based,
                // so we need to decrement the value
                if (is_numeric($value)) {
                    --$value;
                } else {
                    $value = null;
                }
                $qb->andWhere('fv.`position` = :' . $filterName)
                    ->setParameter($filterName, $value);
                continue;
            }

            $qb->andWhere('fv.`' . $filterName . '` = :' . $filterName)
                ->setParameter($filterName, $value);
        }

        return $qb;
    }
}
