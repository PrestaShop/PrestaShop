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
