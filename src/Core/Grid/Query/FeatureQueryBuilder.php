<?php
/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.md.
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
 * needs please refer to https://devdocs.prestashop.com/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
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
            ->addSelect('COUNT(fv.id_feature_value) AS values_count')
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
                $qb->andWhere('position LIKE :' . $filterName)
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
