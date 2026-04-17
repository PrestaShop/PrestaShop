<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Grid\Query;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Query\QueryBuilder;
use PrestaShop\PrestaShop\Adapter\Configuration;
use PrestaShop\PrestaShop\Core\Grid\Search\SearchCriteriaInterface;

/**
 * @todo: copied from default product lits. need to remove unused queries.
 */
final class ProductLightQueryBuilder extends AbstractDoctrineQueryBuilder
{
    /**
     * @var DoctrineSearchCriteriaApplicatorInterface
     */
    private $searchCriteriaApplicator;

    /**
     * @var Configuration
     */
    private $configuration;

    /**
     * @var ProductQueryBuilder
     */
    private $productQueryBuilder;

    public function __construct(
        Connection $connection,
        string $dbPrefix,
        DoctrineSearchCriteriaApplicatorInterface $searchCriteriaApplicator,
        Configuration $configuration,
        ProductQueryBuilder $productQueryBuilder
    ) {
        parent::__construct($connection, $dbPrefix);
        $this->searchCriteriaApplicator = $searchCriteriaApplicator;
        $this->configuration = $configuration;
        $this->productQueryBuilder = $productQueryBuilder;
    }

    /**
     * {@inheritdoc}
     */
    public function getSearchQueryBuilder(SearchCriteriaInterface $searchCriteria): QueryBuilder
    {
        $qb = $this->productQueryBuilder->getSearchQueryBuilder($searchCriteria);
        $qb
            ->select('p.id_product')
            ->addSelect('ps.`price` AS `price_tax_excluded`')
            ->addSelect('pl.`name`')
        ;

        if ($this->configuration->getBoolean('PS_STOCK_MANAGEMENT')) {
            $qb->addSelect('sa.`quantity`');
        }

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
        return $this->productQueryBuilder->getCountQueryBuilder($searchCriteria);
    }
}
