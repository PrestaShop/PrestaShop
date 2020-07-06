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
use PrestaShop\PrestaShop\Core\Grid\Search\SearchCriteriaInterface;

/**
 * Class responsible for providing sql which is needed to render cms page list.
 */
final class CmsPageQueryBuilder extends AbstractDoctrineQueryBuilder
{
    /**
     * @var DoctrineSearchCriteriaApplicatorInterface
     */
    private $searchCriteriaApplicator;

    /**
     * @var array
     */
    private $contextShopIds;

    /**
     * @var int
     */
    private $contextIdLang;

    /**
     * @param Connection $connection
     * @param string $dbPrefix
     * @param DoctrineSearchCriteriaApplicatorInterface $searchCriteriaApplicator
     * @param array $contextShopIds
     * @param int $contextIdLang
     */
    public function __construct(
        Connection $connection,
        $dbPrefix,
        DoctrineSearchCriteriaApplicatorInterface $searchCriteriaApplicator,
        array $contextShopIds,
        $contextIdLang
    ) {
        parent::__construct($connection, $dbPrefix);

        $this->searchCriteriaApplicator = $searchCriteriaApplicator;
        $this->contextShopIds = $contextShopIds;
        $this->contextIdLang = $contextIdLang;
    }

    /**
     * {@inheritdoc}
     */
    public function getSearchQueryBuilder(SearchCriteriaInterface $searchCriteria)
    {
        $qb = $this->getQueryBuilder($searchCriteria->getFilters());

        $qb
            ->select('c.`id_cms`, cl.`link_rewrite`, c.`active`, c.`position`, cl.`meta_title`, cl.`head_seo_title`')
            ->addSelect('c.`id_cms_category`')
            ->groupBy('c.`id_cms`')
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
            ->select('COUNT(DISTINCT c.`id_cms`)')
        ;

        return $qb;
    }

    /**
     * Gets query builder with the common sql for cms page listing.
     *
     * @param array $filters
     *
     * @return \Doctrine\DBAL\Query\QueryBuilder
     */
    private function getQueryBuilder(array $filters)
    {
        $availableFilters = [
            'id_cms_category_parent',
            'id_cms',
            'link_rewrite',
            'meta_title',
            'head_seo_title',
            'position',
            'active',
        ];

        $qb = $this->connection
            ->createQueryBuilder()
            ->from($this->dbPrefix . 'cms', 'c')
            ->leftJoin(
                'c',
                $this->dbPrefix . 'cms_lang',
                'cl',
                'cl.`id_cms` = c.`id_cms`'
            )
            ->innerJoin(
                'c',
                $this->dbPrefix . 'cms_shop',
                'cs',
                'cs.`id_cms` = c.`id_cms`'
            )
        ;

        $qb->andWhere('cl.`id_lang` = :contextLangId');
        $qb->andWhere('cl.`id_shop` IN (:contextShopIds)');
        $qb->andWhere('cs.`id_shop` IN (:contextShopIds)');

        $qb->setParameter('contextLangId', $this->contextIdLang);
        $qb->setParameter('contextShopIds', $this->contextShopIds, Connection::PARAM_INT_ARRAY);

        foreach ($filters as $filterName => $value) {
            if (!in_array($filterName, $availableFilters, true)) {
                continue;
            }

            if ('id_cms_category_parent' === $filterName) {
                $qb->andWhere('c.`id_cms_category` = :id_cms_category_parent');
                $qb->setParameter('id_cms_category_parent', $value);

                continue;
            }

            if (in_array($filterName, ['id_cms', 'active'], true)) {
                $qb->andWhere('c.`' . $filterName . '` = :' . $filterName);
                $qb->setParameter($filterName, $value);

                continue;
            }

            if ('position' === $filterName) {
                $modifiedPositionFilter = $this->getModifiedPositionFilter($value);
                $qb->andWhere('c.`' . $filterName . '` = :' . $filterName);
                $qb->setParameter($filterName, $modifiedPositionFilter);
                continue;
            }

            $qb->andWhere('cl.`' . $filterName . '` LIKE :' . $filterName);
            $qb->setParameter($filterName, '%' . $value . '%');
        }

        return $qb;
    }

    /**
     * Gets modified position filter value. This is required due to in database position filter index starts from 0 and
     * for the customer which wants to filter results the value starts from 1 instead.
     *
     * @param string|int $positionFilterValue
     *
     * @return int|null - if null is returned then no results are found since position field does not hold null values
     */
    private function getModifiedPositionFilter($positionFilterValue)
    {
        if (!is_numeric($positionFilterValue)) {
            return null;
        }
        $reducedByOneFilterValue = $positionFilterValue - 1;
        if (0 > $reducedByOneFilterValue) {
            return null;
        }

        return $reducedByOneFilterValue;
    }
}
