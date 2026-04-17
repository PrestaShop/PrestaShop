<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Grid\Query;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Query\QueryBuilder;
use InvalidArgumentException;
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
            ->applySorting($searchCriteria, $qb);

        return $qb;
    }

    /**
     * {@inheritdoc}
     */
    public function getCountQueryBuilder(SearchCriteriaInterface $searchCriteria)
    {
        $qb = $this->getQueryBuilder($searchCriteria->getFilters())
            ->select('COUNT(DISTINCT pr.`id_specific_price_rule`)');

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
            );

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

        $exactMatchFilters = ['id_specific_price_rule', 'from_quantity', 'reduction_type'];

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

            if ($filterName === 'reduction') {
                $numberOfDecimals = $this->findNumberOfDecimals($value);
                // using TRUNCATE in order to have smart price searches
                // Smart price searches means:
                // searching for "10" will return both 10.0, 10.1 and 10.2
                // searching for "10.0" will only return 10.0
                // searching for "10.1" will only return 10.1
                $qb->andWhere('TRUNCATE(' . $allowedFiltersAliasMap[$filterName] . ',' . $numberOfDecimals . ') = :' . $filterName);
                $qb->setParameter($filterName, $value);
                continue;
            }

            $qb->andWhere($allowedFiltersAliasMap[$filterName] . ' LIKE :' . $filterName);
            $qb->setParameter($filterName, "%$value%");
        }
    }

    /**
     * @param string $value
     *
     * @return int
     */
    private function findNumberOfDecimals($value): int
    {
        if (!is_string($value)) {
            throw new InvalidArgumentException('Expected string');
        }

        $numberOfDecimals = 0;
        $explodedValue = explode('.', $value);

        if (isset($explodedValue[1])) {
            $numberOfDecimals = strlen($explodedValue[1]);
        }

        return $numberOfDecimals;
    }
}
