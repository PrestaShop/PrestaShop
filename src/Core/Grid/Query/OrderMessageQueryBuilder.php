<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Grid\Query;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Query\QueryBuilder;
use PrestaShop\PrestaShop\Core\Grid\Query\Filter\DoctrineFilterApplicatorInterface;
use PrestaShop\PrestaShop\Core\Grid\Query\Filter\SqlFilters;
use PrestaShop\PrestaShop\Core\Grid\Search\SearchCriteriaInterface;

/**
 * Builds search & count queries for Order message grid
 */
final class OrderMessageQueryBuilder implements DoctrineQueryBuilderInterface
{
    /**
     * @var Connection
     */
    private $connection;

    /**
     * @var string
     */
    private $dbPrefix;

    /**
     * @var int
     */
    private $contextLanguageId;

    /**
     * @var DoctrineFilterApplicatorInterface
     */
    private $doctrineFilterApplicator;

    /**
     * @var DoctrineSearchCriteriaApplicatorInterface
     */
    private $doctrineSearchCriteriaApplicator;

    /**
     * @param Connection $connection
     * @param string $dbPrefix
     * @param int $contextLanguageId
     * @param DoctrineFilterApplicatorInterface $doctrineFilterApplicator
     * @param DoctrineSearchCriteriaApplicatorInterface $doctrineSearchCriteriaApplicator
     */
    public function __construct(
        Connection $connection,
        string $dbPrefix,
        int $contextLanguageId,
        DoctrineFilterApplicatorInterface $doctrineFilterApplicator,
        DoctrineSearchCriteriaApplicatorInterface $doctrineSearchCriteriaApplicator
    ) {
        $this->connection = $connection;
        $this->dbPrefix = $dbPrefix;
        $this->contextLanguageId = $contextLanguageId;
        $this->doctrineFilterApplicator = $doctrineFilterApplicator;
        $this->doctrineSearchCriteriaApplicator = $doctrineSearchCriteriaApplicator;
    }

    /**
     * {@inheritdoc}
     */
    public function getSearchQueryBuilder(SearchCriteriaInterface $searchCriteria)
    {
        $qb = $this->buildBaseQuery($searchCriteria);
        $qb->select('om.id_order_message, oml.name, oml.message');

        $this->doctrineSearchCriteriaApplicator
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
        $qb = $this->buildBaseQuery($searchCriteria);
        $qb->select('COUNT(om.id_order_message)');

        return $qb;
    }

    /**
     * @param SearchCriteriaInterface $criteria
     *
     * @return QueryBuilder
     */
    private function buildBaseQuery(SearchCriteriaInterface $criteria): QueryBuilder
    {
        $qb = $this->connection->createQueryBuilder();

        $qb
            ->from($this->dbPrefix . 'order_message', 'om')
            ->leftJoin(
                'om',
                $this->dbPrefix . 'order_message_lang',
                'oml',
                'oml.id_order_message = om.id_order_message AND oml.id_lang = :context_lang_id'
            )
            ->setParameter('context_lang_id', $this->contextLanguageId)
        ;

        $sqlFilters = (new SqlFilters())
            ->addFilter('id_order_message', 'om.id_order_message', SqlFilters::WHERE_LIKE)
            ->addFilter('name', 'oml.name', SqlFilters::WHERE_LIKE)
            ->addFilter('message', 'oml.message', SqlFilters::WHERE_LIKE)
        ;

        $this->doctrineFilterApplicator->apply($qb, $sqlFilters, $criteria->getFilters());

        return $qb;
    }
}
