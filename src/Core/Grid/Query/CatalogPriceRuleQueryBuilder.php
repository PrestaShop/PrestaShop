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
use PrestaShop\PrestaShop\Core\Grid\Search\SearchCriteriaInterface;

/**
 * Builds query for catalog price rule list
 */
final class CatalogPriceRuleQueryBuilder extends AbstractDoctrineQueryBuilder
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
     * @param $dbPrefix
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

        $qb->select(
            'pr.id_specific_price_rule,
            pr.name,
            pr.from_quantity,
            pr.reduction,
            pr.reduction_type,
            pr.from date_from,
            pr.to date_to,
            pr_shop.name shop,
            pr_currency.name currency,
            pr_country.name country,
            pr_group.name group_name'
        );
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
            ->select('COUNT(DISTINCT pr.`id_specific_price_rule`)')
        ;

        return $qb;
    }

    /**
     * Gets query builder with the common sql for catalog price rule listing.
     *
     * @param array $filters
     *
     * @return QueryBuilder
     */
    private function getQueryBuilder(array $filters)
    {
        $qb = $this->connection
            ->createQueryBuilder()
            ->from($this->dbPrefix . 'specific_price_rule', 'pr')
            ->leftJoin(
                'pr',
                $this->dbPrefix . 'shop',
                'pr_shop',
                'pr_shop.`id_shop` = pr.`id_shop` AND pr.`id_shop` IN (:contextShopIds)'
            )
            ->leftJoin(
                'pr',
                $this->dbPrefix . 'currency_lang',
                'pr_currency',
                'pr_currency.`id_currency` = pr.`id_currency` AND pr_currency.`id_lang` = :contextLangId'
            )
            ->leftJoin(
                'pr',
                $this->dbPrefix . 'country_lang',
                'pr_country',
                'pr_country.`id_country` = pr.`id_country` AND pr_country.`id_lang` = :contextLangId'
            )
            ->leftJoin(
                'pr',
                $this->dbPrefix . 'group_lang',
                'pr_group',
                'pr_group.`id_group` = pr.`id_group` AND pr_group.`id_lang` = :contextLangId'
            )
        ;

        $this->applyFilters($qb, $filters);
        $qb->setParameter('contextLangId', $this->contextIdLang);
        $qb->setParameter('contextShopIds', $this->contextShopIds, Connection::PARAM_INT_ARRAY);

        return $qb;
    }

    /**
     * @param QueryBuilder $qb
     * @param array $filters
     */
    private function applyFilters(QueryBuilder $qb, array $filters)
    {
        $allowedFiltersAliasMap = [
            'id_specific_price_rule' => 'pr.id_specific_price_rule',
            'name' => 'pr.name',
            'from_quantity' => 'pr.from_quantity',
            'reduction' => 'pr.reduction',
            'reduction_type' => 'pr.reduction_type',
            'date_from' => 'pr.from',
            'date_to' => 'pr.to',
            'shop' => 'pr_shop.name',
            'currency' => 'pr_currency.name',
            'country' => 'pr_country.name',
            'group_name' => 'pr_group.name',
        ];

        $exactMatchFilters = ['id_specific_price_rule', 'from_quantity', 'reduction', 'reduction_type'];

        foreach ($filters as $filterName => $value) {
            if (!array_key_exists($filterName, $allowedFiltersAliasMap)) {
                return;
            }

            if (in_array($filterName, $exactMatchFilters, true)) {
                $qb->andWhere($allowedFiltersAliasMap[$filterName] . ' = :' . $filterName);
                $qb->setParameter($filterName, $value);

                continue;
            }

            if ('date_from' === $filterName || 'date_to' === $filterName) {
                if (isset($value['from'])) {
                    $qb->andWhere($allowedFiltersAliasMap[$filterName] . ' >= :' . $filterName . '_from');
                    $qb->setParameter($filterName . '_from', $value['from']);
                }
                if (isset($value['to'])) {
                    $qb->andWhere($allowedFiltersAliasMap[$filterName] . ' <= :' . $filterName . '_to');
                    $qb->setParameter($filterName . '_to', $value['to']);
                }

                continue;
            }

            $qb->andWhere($allowedFiltersAliasMap[$filterName] . ' LIKE :' . $filterName);
            $qb->setParameter($filterName, "%$value%");
        }
    }
}
