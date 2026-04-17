<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Grid\Query;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Query\QueryBuilder;
use PrestaShop\PrestaShop\Core\Grid\Search\SearchCriteriaInterface;

/**
 * Builds search & count queries for customer's viewed products grid.
 */
final class CustomerViewedProductQueryBuilder extends AbstractDoctrineQueryBuilder
{
    /**
     * @var DoctrineSearchCriteriaApplicatorInterface
     */
    private $searchCriteriaApplicator;

    /**
     * @var int
     */
    private $contextLanguageId;

    /**
     * @param Connection $connection
     * @param string $dbPrefix
     * @param DoctrineSearchCriteriaApplicatorInterface $searchCriteriaApplicator
     * @param int $contextLanguageId
     */
    public function __construct(
        Connection $connection,
        string $dbPrefix,
        DoctrineSearchCriteriaApplicatorInterface $searchCriteriaApplicator,
        int $contextLanguageId
    ) {
        parent::__construct($connection, $dbPrefix);
        $this->searchCriteriaApplicator = $searchCriteriaApplicator;
        $this->contextLanguageId = $contextLanguageId;
    }

    /**
     * {@inheritdoc}
     */
    public function getSearchQueryBuilder(SearchCriteriaInterface $searchCriteria): QueryBuilder
    {
        $qb = $this->getQueryBuilder($searchCriteria->getFilters());
        $qb->select('
            cp.`date_add`,
            cp.`id_product`,
            cp.`id_cart`,
            cp.`id_shop`,
            pl.`name` as product_name
       ');

        $this->searchCriteriaApplicator
            ->applyPagination($searchCriteria, $qb)
            ->applySorting($searchCriteria, $qb);

        return $qb;
    }

    /**
     * {@inheritdoc}
     */
    public function getCountQueryBuilder(SearchCriteriaInterface $searchCriteria): QueryBuilder
    {
        return $this->getQueryBuilder($searchCriteria->getFilters())
            ->select('COUNT(cp.`id_product`)');
    }

    /**
     * Gets query builder with the common sql used for displaying viewed products list and applying filter actions.
     *
     * @param array $filters
     *
     * @return QueryBuilder
     */
    private function getQueryBuilder(array $filters): QueryBuilder
    {
        $qb = $this->connection
            ->createQueryBuilder()
            ->from($this->dbPrefix . 'cart_product', 'cp')
            ->where('c.`id_customer` != 0')
            ->where('cp.`id_cart` NOT IN (SELECT `id_cart` FROM ' . $this->dbPrefix . 'orders)')
        ;

        $qb->innerJoin(
            'cp',
            $this->dbPrefix . 'cart',
            'c',
            'cp.`id_cart` = c.`id_cart`'
        );
        $qb->innerJoin(
            'cp',
            $this->dbPrefix . 'product_lang',
            'pl',
            'cp.`id_product` = pl.`id_product` AND pl.`id_lang` = :langId'
        )->setParameter('langId', $this->contextLanguageId);

        $this->applyFilters($qb, $filters);

        return $qb;
    }

    /**
     * Apply filters to viewed products query builder.
     *
     * @param array $filters
     * @param QueryBuilder $qb
     */
    private function applyFilters(QueryBuilder $qb, array $filters)
    {
        $allowedFiltersMap = [
            'id_customer' => 'c.id_customer',
        ];

        foreach ($filters as $filterName => $value) {
            if (!array_key_exists($filterName, $allowedFiltersMap) || empty($value)) {
                continue;
            }

            if ('id_customer' === $filterName) {
                $qb->andWhere('c.`id_customer` = :' . $filterName);
                $qb->setParameter($filterName, $value);
            }
        }
    }
}
