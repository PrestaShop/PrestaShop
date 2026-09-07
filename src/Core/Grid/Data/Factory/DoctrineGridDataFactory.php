<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Grid\Data\Factory;

use Doctrine\DBAL\Query\QueryBuilder;
use Doctrine\SqlFormatter\NullHighlighter;
use Doctrine\SqlFormatter\SqlFormatter;
use PrestaShop\PrestaShop\Core\ExtraProperty\Grid\ExtraPropertiesGridQueryBuilderModifier;
use PrestaShop\PrestaShop\Core\Grid\Data\GridData;
use PrestaShop\PrestaShop\Core\Grid\Query\DoctrineQueryBuilderInterface;
use PrestaShop\PrestaShop\Core\Grid\Query\QueryParserInterface;
use PrestaShop\PrestaShop\Core\Grid\Record\RecordCollection;
use PrestaShop\PrestaShop\Core\Grid\Search\SearchCriteria;
use PrestaShop\PrestaShop\Core\Grid\Search\SearchCriteriaInterface;
use PrestaShop\PrestaShop\Core\Hook\HookDispatcherInterface;
use PrestaShop\PrestaShop\Core\Search\Filters;
use PrestaShop\PrestaShop\Core\Search\Pagination;
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
     * @param ExtraPropertiesGridQueryBuilderModifier|null $extraPropertiesGridQueryBuilderModifier
     */
    public function __construct(
        protected DoctrineQueryBuilderInterface $gridQueryBuilder,
        protected HookDispatcherInterface $hookDispatcher,
        protected QueryParserInterface $queryParser,
        protected string $gridId,
        protected ?ExtraPropertiesGridQueryBuilderModifier $extraPropertiesGridQueryBuilderModifier = null,
    ) {
    }

    /**
     * {@inheritdoc}
     */
    public function getData(SearchCriteriaInterface $searchCriteria)
    {
        $searchQueryBuilder = $this->gridQueryBuilder->getSearchQueryBuilder($searchCriteria);
        $countQueryBuilder = $this->gridQueryBuilder->getCountQueryBuilder($searchCriteria);

        if ($this->extraPropertiesGridQueryBuilderModifier) {
            $this->extraPropertiesGridQueryBuilderModifier->apply(
                $searchQueryBuilder,
                $countQueryBuilder,
                $searchCriteria,
                $this->gridId
            );
        }

        $this->hookDispatcher->dispatchWithParameters('action' . Container::camelize($this->gridId) . 'GridQueryBuilderModifier', [
            'search_query_builder' => $searchQueryBuilder,
            'count_query_builder' => $countQueryBuilder,
            'search_criteria' => $searchCriteria,
        ]);

        $recordsTotal = (int) $countQueryBuilder->executeQuery()->fetchOne();

        // The offset lives in the URL and in the employee's saved filters, so removing the last rows of
        // the page being viewed leaves it pointing past the end of the result set. Running the search with
        // it returns nothing and the grid says there is no record at all, while the earlier pages still
        // hold some. Fall back to the last page that does, and rebuild the data from there.
        if (Pagination::isOffsetOutOfRange($recordsTotal, (int) $searchCriteria->getOffset())) {
            return $this->getData($this->withOffset(
                $searchCriteria,
                Pagination::computeValidOffset($recordsTotal, (int) $searchCriteria->getLimit())
            ));
        }

        $records = $searchQueryBuilder->executeQuery()->fetchAllAssociative();

        if ($this->extraPropertiesGridQueryBuilderModifier) {
            $records = $this->extraPropertiesGridQueryBuilderModifier->castExtraProperties($records, $this->gridId);
        }

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

    /**
     * Returns the same search criteria carrying a different offset.
     *
     * Filters keep extra state the grid relies on, so those are cloned rather than rebuilt; anything else
     * only exposes the SearchCriteriaInterface getters, which is all a plain SearchCriteria needs.
     */
    private function withOffset(SearchCriteriaInterface $searchCriteria, int $offset): SearchCriteriaInterface
    {
        if ($searchCriteria instanceof Filters) {
            $newSearchCriteria = clone $searchCriteria;
            $newSearchCriteria->add(['offset' => $offset]);

            return $newSearchCriteria;
        }

        return new SearchCriteria(
            $searchCriteria->getFilters(),
            $searchCriteria->getOrderBy(),
            $searchCriteria->getOrderWay(),
            $offset,
            $searchCriteria->getLimit()
        );
    }
}
