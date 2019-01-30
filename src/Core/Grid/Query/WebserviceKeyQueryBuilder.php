<?php
/**
 * 2007-2019 PrestaShop and Contributors
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
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
 * needs please refer to https://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2019 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShop\PrestaShop\Core\Grid\Query;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Query\QueryBuilder;
use PrestaShop\PrestaShop\Core\Grid\Search\SearchCriteriaInterface;

/**
 * Class WebserviceKeyQueryBuilder is responsible for providing data for webservice accounts list.
 */
final class WebserviceKeyQueryBuilder extends AbstractDoctrineQueryBuilder
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
     * WebserviceKeyQueryBuilder constructor.
     *
     * @param Connection $connection
     * @param $dbPrefix
     * @param DoctrineSearchCriteriaApplicatorInterface $searchCriteriaApplicator
     * @param array $contextShopIds
     */
    public function __construct(
        Connection $connection,
        $dbPrefix,
        DoctrineSearchCriteriaApplicatorInterface $searchCriteriaApplicator,
        array $contextShopIds
    ) {
        parent::__construct($connection, $dbPrefix);
        $this->searchCriteriaApplicator = $searchCriteriaApplicator;
        $this->contextShopIds = $contextShopIds;
    }

    /**
     * {@inheritdoc}
     */
    public function getSearchQueryBuilder(SearchCriteriaInterface $searchCriteria)
    {
        $qb = $this->getQueryBuilder($searchCriteria->getFilters());
        $qb->select('wa.`id_webservice_account`, wa.`key`, wa.`description`, wa.`active`')
            ->orderBy(
                $this->getModifiedOrderBy($searchCriteria->getOrderBy()),
                $searchCriteria->getOrderWay()
            )
            ->groupBy('wa.`id_webservice_account`');

        $this->searchCriteriaApplicator->applyPagination($searchCriteria, $qb);

        return $qb;
    }

    /**
     * {@inheritdoc}
     */
    public function getCountQueryBuilder(SearchCriteriaInterface $searchCriteria)
    {
        $qb = $this->getQueryBuilder($searchCriteria->getFilters())
            ->select('COUNT(DISTINCT wa.`id_webservice_account`)');

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
            ->from($this->dbPrefix . 'webservice_account', 'wa')
            ->innerJoin(
                'wa',
                $this->dbPrefix . 'webservice_account_shop',
                'was',
                'was.`id_webservice_account` = wa.`id_webservice_account`'
            );

        $qb->andWhere('was.`id_shop` IN (:shops)');

        $qb->setParameter('shops', $this->contextShopIds, Connection::PARAM_INT_ARRAY);

        foreach ($filters as $filterName => $value) {
            if ('active' === $filterName && is_numeric($value)) {
                $qb->andWhere('wa.`active`=' . (int) $value);

                continue;
            }

            $qb->andWhere('wa.`' . $filterName . '` LIKE :' . $filterName);
            $qb->setParameter($filterName, '%' . $value . '%');
        }

        return $qb;
    }

    /**
     * Gets modified order by which includes an alias for reserved keyword.
     *
     * @param string $orderBy - original order by value
     *
     * @return string
     */
    private function getModifiedOrderBy($orderBy)
    {
        return 'wa.`' . $orderBy . '`';
    }
}
