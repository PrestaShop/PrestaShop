<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Grid\Query;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Query\QueryBuilder;
use PrestaShop\PrestaShop\Adapter\Feature\Repository\FeatureRepository;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopId;
use PrestaShop\PrestaShop\Core\Exception\InvalidArgumentException;
use PrestaShop\PrestaShop\Core\Grid\Search\SearchCriteriaInterface;
use PrestaShop\PrestaShop\Core\Grid\Search\ShopSearchCriteriaInterface;

class FeatureQueryBuilder extends AbstractDoctrineQueryBuilder
{
    /**
     * @var DoctrineSearchCriteriaApplicatorInterface
     */
    private $searchCriteriaApplicator;

    /**
     * @var int
     */
    private $languageId;

    /**
     * @var FeatureRepository
     */
    private $featureRepository;

    /**
     * @param Connection $connection
     * @param string $dbPrefix
     * @param DoctrineSearchCriteriaApplicatorInterface $searchCriteriaApplicator
     */
    public function __construct(
        Connection $connection,
        $dbPrefix,
        DoctrineSearchCriteriaApplicatorInterface $searchCriteriaApplicator,
        int $contextLangId,
        FeatureRepository $featureRepository
    ) {
        parent::__construct($connection, $dbPrefix);

        $this->searchCriteriaApplicator = $searchCriteriaApplicator;
        $this->languageId = $contextLangId;
        $this->featureRepository = $featureRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function getSearchQueryBuilder(SearchCriteriaInterface $searchCriteria)
    {
        $qb = $this->getQueryBuilder($searchCriteria);
        $qb
            ->select('f.id_feature, fl.name, f.position')
            ->addSelect('COUNT(DISTINCT(fv.id_feature_value)) AS values_count')
            ->leftJoin(
                'f',
                $this->dbPrefix . 'feature_value',
                'fv',
                'f.id_feature = fv.id_feature'
            )
            ->groupBy('f.id_feature')
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
        return $this->getQueryBuilder($searchCriteria)
            ->select('COUNT(DISTINCT f.id_feature)')
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
        if (!$searchCriteria instanceof ShopSearchCriteriaInterface) {
            throw new InvalidArgumentException('Invalid search criteria type');
        }

        $filters = $searchCriteria->getFilters();
        $shopIds = array_map(static function (ShopId $shopId): int {
            return $shopId->getValue();
        }, $this->featureRepository->getShopIdsByConstraint($searchCriteria->getShopConstraint()));

        $allowedFilters = ['id_feature', 'name', 'position'];

        $qb = $this->connection
            ->createQueryBuilder()
            ->from($this->dbPrefix . 'feature', 'f')
            ->innerJoin(
                'f',
                $this->dbPrefix . 'feature_shop',
                'fs',
                'fs.id_feature = f.id_feature AND fs.id_shop IN (:shopIds)'
            )
            ->setParameter('shopIds', $shopIds, Connection::PARAM_INT_ARRAY)
            ->leftJoin(
                'f',
                $this->dbPrefix . 'feature_lang',
                'fl',
                'f.id_feature = fl.id_feature AND fl.id_lang = :langId'
            )
            ->setParameter('langId', $this->languageId)
        ;

        foreach ($filters as $filterName => $value) {
            if (!in_array($filterName, $allowedFilters, true)) {
                continue;
            }

            if ('name' === $filterName) {
                $qb->andWhere('fl.name LIKE :' . $filterName)
                    ->setParameter($filterName, '%' . $value . '%');
                continue;
            }

            if ('position' === $filterName) {
                $qb->andWhere('f.position LIKE :' . $filterName)
                    // position value starts from 0 in database, but in list view they are incremented by +1,
                    // so if it is a position filter, then we decrement the value to return expected results
                    ->setParameter($filterName, '%' . ((int) $value - 1) . '%');
                continue;
            }

            $qb->andWhere('f.`' . $filterName . '` = :' . $filterName)
                ->setParameter($filterName, $value);
        }

        return $qb;
    }
}
