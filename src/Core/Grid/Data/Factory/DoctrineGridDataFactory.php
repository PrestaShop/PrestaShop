<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Grid\Data\Factory;

use Doctrine\DBAL\Query\QueryBuilder;
use Doctrine\SqlFormatter\NullHighlighter;
use Doctrine\SqlFormatter\SqlFormatter;
use PrestaShop\PrestaShop\Core\Grid\Data\GridData;
use PrestaShop\PrestaShop\Core\Grid\Query\DoctrineQueryBuilderInterface;
use PrestaShop\PrestaShop\Core\Grid\Query\QueryParserInterface;
use PrestaShop\PrestaShop\Core\Grid\Record\RecordCollection;
use PrestaShop\PrestaShop\Core\Grid\Search\SearchCriteriaInterface;
use PrestaShop\PrestaShop\Core\Hook\HookDispatcherInterface;
use Symfony\Component\DependencyInjection\Container;

/**
 * Class DoctrineGridDataFactory is responsible for returning grid data using Doctrine query builders.
 */
class DoctrineGridDataFactory implements GridDataFactoryInterface
{
    /**
     * @param DoctrineQueryBuilderInterface $gridQueryBuilder
     * @param HookDispatcherInterface $hookDispatcher
     * @param QueryParserInterface $queryParser
     * @param string $gridId
     */
    public function __construct(
        protected DoctrineQueryBuilderInterface $gridQueryBuilder,
        protected HookDispatcherInterface $hookDispatcher,
        protected QueryParserInterface $queryParser,
        protected string $gridId
    ) {
    }

    /**
     * {@inheritdoc}
     */
    public function getData(SearchCriteriaInterface $searchCriteria)
    {
        $searchQueryBuilder = $this->gridQueryBuilder->getSearchQueryBuilder($searchCriteria);
        $countQueryBuilder = $this->gridQueryBuilder->getCountQueryBuilder($searchCriteria);

        $this->hookDispatcher->dispatchWithParameters('action' . Container::camelize($this->gridId) . 'GridQueryBuilderModifier', [
            'search_query_builder' => $searchQueryBuilder,
            'count_query_builder' => $countQueryBuilder,
            'search_criteria' => $searchCriteria,
        ]);

        $records = $searchQueryBuilder->executeQuery()->fetchAllAssociative();
        $recordsTotal = (int) $countQueryBuilder->executeQuery()->fetchOne();

        $records = new RecordCollection($records);

        return new GridData(
            $records,
            $recordsTotal,
            $this->getRawQuery($searchQueryBuilder)
        );
    }

    /**
     * @param QueryBuilder $queryBuilder
     *
     * @return string
     */
    private function getRawQuery(QueryBuilder $queryBuilder): string
    {
        $query = $queryBuilder->getSQL();
        $parameters = $queryBuilder->getParameters();

        $parsedQuery = $this->queryParser->parse($query, $parameters);

        return $this->formatSQL($parsedQuery);
    }

    protected function formatSQL(string $query): string
    {
        $sqlFormatter = new SqlFormatter(new NullHighlighter());

        return $sqlFormatter->format($query);
    }
}
