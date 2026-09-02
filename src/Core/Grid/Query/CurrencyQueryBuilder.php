<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Grid\Query;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Query\QueryBuilder;
use PDO;
use PrestaShop\PrestaShop\Core\Grid\Search\SearchCriteriaInterface;

/**
 * Class CurrencyQueryBuilder builds search & count queries for currencies grid.
 */
final class CurrencyQueryBuilder extends AbstractDoctrineQueryBuilder
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
    private $contextLangId;

    /**
     * @param Connection $connection
     * @param string $dbPrefix
     * @param DoctrineSearchCriteriaApplicatorInterface $searchCriteriaApplicator
     * @param array $contextShopIds
     */
    public function __construct(
        Connection $connection,
        $dbPrefix,
        DoctrineSearchCriteriaApplicatorInterface $searchCriteriaApplicator,
        array $contextShopIds,
        $contextLangId
    ) {
        parent::__construct($connection, $dbPrefix);

        $this->searchCriteriaApplicator = $searchCriteriaApplicator;
        $this->contextShopIds = $contextShopIds;
        $this->contextLangId = $contextLangId;
    }

    /**
     * {@inheritdoc}
     */
    public function getSearchQueryBuilder(SearchCriteriaInterface $searchCriteria)
    {
        $qb = $this->getQueryBuilder($searchCriteria->getFilters());

        $qb
            ->select('c.`id_currency`, c.`iso_code`, cs.`conversion_rate`, c.`active`, c.`modified`, c.`unofficial`, cl.`name`, cl.`symbol`')
            ->groupBy('c.`id_currency`')
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
            ->select('COUNT(DISTINCT c.`id_currency`)')
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
        $allowedFilters = [
            'id_currency',
            'name',
            'symbol',
            'iso_code',
            'active',
        ];

        $qb = $this->connection
            ->createQueryBuilder()
            ->from($this->dbPrefix . 'currency', 'c')
            ->innerJoin(
                'c',
                $this->dbPrefix . 'currency_shop',
                'cs',
                'c.`id_currency` = cs.`id_currency`'
            )
            ->innerJoin(
                'c',
                $this->dbPrefix . 'currency_lang',
                'cl',
                'c.`id_currency` = cl.`id_currency`'
            )
        ;
        $qb->andWhere('cs.`id_shop` IN (:shops)');
        $qb->andWhere('cl.`id_lang` = :lang');
        $qb->andWhere('c.`deleted` = 0');

        $qb->setParameter('shops', $this->contextShopIds, Connection::PARAM_INT_ARRAY);
        $qb->setParameter('lang', $this->contextLangId, PDO::PARAM_INT);

        foreach ($filters as $filterName => $value) {
            if (!in_array($filterName, $allowedFilters, true)) {
                continue;
            }

            if ('active' === $filterName) {
                $qb->andWhere('c.`active` = :active');
                $qb->setParameter('active', $value);

                continue;
            }

            if ('name' === $filterName || 'symbol' === $filterName) {
                $qb->andWhere('cl.`' . $filterName . '` LIKE :' . $filterName);
                $qb->setParameter($filterName, '%' . $value . '%');

                continue;
            }

            $qb->andWhere('c.`' . $filterName . '` LIKE :' . $filterName);
            $qb->setParameter($filterName, '%' . $value . '%');
        }

        return $qb;
    }
}
