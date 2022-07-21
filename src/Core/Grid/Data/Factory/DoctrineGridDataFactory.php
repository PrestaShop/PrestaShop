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

namespace PrestaShop\PrestaShop\Core\Grid\Data\Factory;

use Doctrine\DBAL\Query\QueryBuilder;
use PDO;
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
final class DoctrineGridDataFactory implements GridDataFactoryInterface
{
    /**
     * @var DoctrineQueryBuilderInterface
     */
    private $gridQueryBuilder;

    /**
     * @var HookDispatcherInterface
     */
    private $hookDispatcher;

    /**
     * @var QueryParserInterface
     */
    private $queryParser;

    /**
     * @var string
     */
    private $gridId;

    /**
     * @param DoctrineQueryBuilderInterface $gridQueryBuilder
     * @param HookDispatcherInterface $hookDispatcher
     * @param QueryParserInterface $queryParser
     * @param string $gridId
     */
    public function __construct(
        DoctrineQueryBuilderInterface $gridQueryBuilder,
        HookDispatcherInterface $hookDispatcher,
        QueryParserInterface $queryParser,
        $gridId
    ) {
        $this->gridQueryBuilder = $gridQueryBuilder;
        $this->hookDispatcher = $hookDispatcher;
        $this->queryParser = $queryParser;
        $this->gridId = $gridId;
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

        $recordsTotal = (int) $countQueryBuilder->execute()->fetch(PDO::FETCH_COLUMN);
        $searchOffset = (int) $searchCriteria->getOffset();

        // If offset is out of range, we compute and apply a valid offset.
        if (Pagination::isOffsetOutOfRange($recordsTotal, $searchOffset)) {
            // @TODO : those cases should be simplified with some refactoring cf. https://github.com/PrestaShop/PrestaShop/issues/29131

            $searchLimit = $searchCriteria->getLimit();
            $inRangeOffset = Pagination::computeValidOffset($recordsTotal, $searchOffset, $searchLimit);

            // @TODO : add INFO log to prevent that we overwrite offset from `$searchCriteria->getOffset()` to `$inRangeOffset`

            if ($searchCriteria instanceof Filters) {
                $newSearchCriteria = clone $searchCriteria;
                $newSearchCriteria->add(['offset' => $inRangeOffset]);
            } else {
                // @TODO : add INFO/WARN log to prevent that we replaced `get_class($searchCriteria)` by `SearchCriteria`

                $newSearchCriteria = new SearchCriteria(
                    $searchCriteria->getFilters(),
                    $searchCriteria->getOrderBy(),
                    $searchCriteria->getOrderWay(),
                    $inRangeOffset,
                    $searchLimit
                );
            }

            return $this->getData($newSearchCriteria);
        }

        $records = $searchQueryBuilder->execute()->fetchAll();
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
    private function getRawQuery(QueryBuilder $queryBuilder)
    {
        $query = $queryBuilder->getSQL();
        $parameters = $queryBuilder->getParameters();

        return $this->queryParser->parse($query, $parameters);
    }
}
