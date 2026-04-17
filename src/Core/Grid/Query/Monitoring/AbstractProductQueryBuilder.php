<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Grid\Query\Monitoring;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Query\QueryBuilder;
use PrestaShop\PrestaShop\Core\Grid\Query\AbstractDoctrineQueryBuilder;
use PrestaShop\PrestaShop\Core\Grid\Query\DoctrineSearchCriteriaApplicator;
use PrestaShop\PrestaShop\Core\Grid\Search\SearchCriteriaInterface;
use PrestaShop\PrestaShop\Core\Multistore\MultistoreContextCheckerInterface;

/**
 * Provides reusable queries for lists of monitoring products
 */
abstract class AbstractProductQueryBuilder extends AbstractDoctrineQueryBuilder
{
    /**
     * @var int
     */
    protected $contextLangId;

    /**
     * @var int
     */
    protected $contextShopId;

    /**
     * @var DoctrineSearchCriteriaApplicator
     */
    protected $searchCriteriaApplicator;

    /**
     * @var MultistoreContextCheckerInterface
     */
    protected $multistoreContextChecker;

    /**
     * @param Connection $connection
     * @param string $dbPrefix
     * @param DoctrineSearchCriteriaApplicator $searchCriteriaApplicator
     * @param int $contextLangId
     * @param int $contextShopId
     * @param MultistoreContextCheckerInterface $multistoreContextChecker
     */
    public function __construct(
        Connection $connection,
        $dbPrefix,
        $contextLangId,
        $contextShopId,
        DoctrineSearchCriteriaApplicator $searchCriteriaApplicator,
        MultistoreContextCheckerInterface $multistoreContextChecker
    ) {
        parent::__construct($connection, $dbPrefix);
        $this->contextLangId = $contextLangId;
        $this->contextShopId = $contextShopId;
        $this->searchCriteriaApplicator = $searchCriteriaApplicator;
        $this->multistoreContextChecker = $multistoreContextChecker;
    }

    /**
     * Provides commonly reusable query for monitoring products lists
     *
     * @param SearchCriteriaInterface $searchCriteria
     *
     * @return QueryBuilder
     */
    protected function getProductsCommonQueryBuilder(SearchCriteriaInterface $searchCriteria)
    {
        $isSingleShopContext = $this->multistoreContextChecker->isSingleShopContext();

        $qb = $this->connection
            ->createQueryBuilder()
            ->select(['p.id_product', 'p.reference', 'p.active', 'pl.name'])
            ->from($this->dbPrefix . 'product', 'p')
            ->setParameter('context_lang_id', $this->contextLangId)
            ->setParameter('context_shop_id', $this->contextShopId);

        $qb->leftJoin(
            'p',
            $this->dbPrefix . 'product_lang',
            'pl',
            $isSingleShopContext ?
                'p.id_product = pl.id_product AND pl.id_lang = :context_lang_id AND pl.id_shop = :context_shop_id' :
                'p.id_product = pl.id_product AND pl.id_lang = :context_lang_id AND pl.id_shop = p.id_shop_default'
        );

        $qb->leftJoin(
            'p',
            $this->dbPrefix . 'product_shop',
            'ps',
            $isSingleShopContext ?
                'p.id_product = ps.id_product AND ps.id_shop = :context_shop_id' :
                'p.id_product = ps.id_product AND ps.id_shop = p.id_shop_default'
        );

        if ($isSingleShopContext) {
            $qb->andWhere('ps.id_shop = :context_shop_id');
        }

        $this->applyFilters($qb, $searchCriteria->getFilters());

        return $qb;
    }

    /**
     * @param QueryBuilder $qb
     * @param array $filters
     */
    private function applyFilters(QueryBuilder $qb, array $filters)
    {
        $allowedFilters = ['id_product', 'reference', 'name', 'active'];

        foreach ($filters as $filterName => $filterValue) {
            if (!in_array($filterName, $allowedFilters, true)) {
                continue;
            }

            if ('id_product' === $filterName) {
                $qb->andWhere("p.id_product = :$filterName");
                $qb->setParameter($filterName, $filterValue);

                continue;
            }

            if ('reference' === $filterName) {
                $qb->andWhere("p.reference LIKE :$filterName");
                $qb->setParameter($filterName, '%' . $filterValue . '%');

                continue;
            }

            if ('name' === $filterName) {
                $qb->andWhere("pl.name LIKE :$filterName");
                $qb->setParameter($filterName, '%' . $filterValue . '%');

                continue;
            }

            if ('active' === $filterName) {
                $qb->andWhere("p.active = :$filterName");
                $qb->setParameter($filterName, $filterValue);
            }
        }
    }
}
