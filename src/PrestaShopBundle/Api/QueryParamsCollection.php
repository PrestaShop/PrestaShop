<?php
/**
 * 2007-2017 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2016 PrestaShop SA
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShopBundle\Api;

use Doctrine\Common\Util\Inflector;
use PrestaShopBundle\Exception\InvalidPaginationParamsException;
use Symfony\Component\HttpFoundation\Request;

class QueryParamsCollection
{
    const DEFAULT_PAGE_INDEX = 1;

    const DEFAULT_PAGE_SIZE = 100;

    const SQL_PARAM_FIRST_RESULT = 'first_result';

    const SQL_PARAM_MAX_RESULTS = 'max_results';

    const SQL_CLAUSE_WHERE = 'where';

    const SQL_CLAUSE_HAVING = 'having';

    /**
     * @var array
     */
    private $queryParams = array();

    /**
     * @param Request $request
     * @return $this
     */
    public function fromRequest(Request $request)
    {
        $queryParams = $request->query->all();

        $queryParams = $this->excludeUnknownParams($queryParams);
        $queryParams = $this->parsePaginationParams($queryParams);
        $queryParams = $this->parseOrderParams($queryParams);
        $this->queryParams = $this->parseFilterParams($queryParams, $request);

        return $this;
    }

    /**
     * @param $queryParams
     * @return mixed
     */
    private function excludeUnknownParams(array $queryParams)
    {
        $queryParamsNames = array_keys($queryParams);
        array_walk($queryParamsNames, function ($name) use (&$queryParams) {
            $validParams = array_merge(
                $this->getValidPaginationParams(),
                $this->getValidOrderParams(),
                $this->getValidFilterParams()
            );

            if (!in_array($name, $validParams)) {
                unset($queryParams[$name]);
            }
        });

        return $queryParams;
    }

    /**
     * @param array $queryParams
     * @param Request $request
     * @return array
     */
    private function parseFilterParams(array $queryParams, Request $request)
    {
        $allParameters = array_merge(
            $request->attributes->all(),
            $request->query->all()
        );

        $filters = array_filter(array_keys($allParameters), function ($filter) {
            return in_array($filter, $this->getValidFilterParams());
        });

        $filterParams = array();
        array_walk($filters, function ($filter) use ($allParameters, &$filterParams) {
            if (is_array($allParameters[$filter])) {
                $allParameters[$filter] = array_filter($allParameters[$filter], function ($value) {
                    return strlen(trim($value)) > 0;
                });
            }

            $filterParams[$filter] = $allParameters[$filter];
        });


        $queryParams['filter'] = $filterParams;



        return $queryParams;
    }

    /**
     * @return array
     */
    private function getValidFilterParams()
    {
        return array('productId', 'supplier_id', 'category_id', 'keywords');
    }

    /**
     * @param array $queryParams
     * @return array
     */
    private function parsePaginationParams(array $queryParams)
    {
        if (!array_key_exists('page_index', $queryParams)) {
            $queryParams['page_index'] = self::DEFAULT_PAGE_INDEX;
        }

        if (!array_key_exists('page_size', $queryParams)) {
            $queryParams['page_size'] = self::DEFAULT_PAGE_SIZE;
        }

        $queryParams['page_size'] = (int)$queryParams['page_size'];
        $queryParams['page_index'] = (int)$queryParams['page_index'];

        if (
            $queryParams['page_size'] > self::DEFAULT_PAGE_SIZE ||
            $queryParams['page_size'] < 1
        ) {
            throw new InvalidPaginationParamsException(
                sprintf(
                    'A page size should be an integer greater than 1 and fewer than %s',
                    self::DEFAULT_PAGE_SIZE
                )
            );
        }

        if ($queryParams['page_index'] < 1) {
            throw new InvalidPaginationParamsException();
        }

        return $queryParams;
    }

    /**
     * @return array
     */
    private function getValidPaginationParams() {
        return array(
            'page_size',
            'page_index',
            'order'
        );
    }

    /**
     * @param array $queryParams
     * @return array|mixed
     */
    private function parseOrderParams(array $queryParams)
    {
        if (!array_key_exists('order', $queryParams)) {
            $queryParams = $this->setDefaultOrderParam($queryParams);
        }

        $queryParams['order'] = strtolower($queryParams['order']);

        $filterColumn = $this->removeDirection($queryParams['order']);
        if (!in_array($filterColumn, $this->getValidOrderParams())) {
            $queryParams = $this->setDefaultOrderParam($queryParams);
        }

        return $queryParams;
    }

    /**
     * @return array
     */
    private function getValidOrderParams()
    {
        return array(
            'product',
            'reference',
            'supplier',
            'available_quantity',
            'physical_quantity',
        );
    }

    /**
     * @param $queryParams
     * @return mixed
     */
    private function setDefaultOrderParam($queryParams)
    {
        $queryParams['order'] = 'product DESC';

        return $queryParams;
    }

    /**
     * @param $subject
     * @return mixed
     */
    private function removeDirection($subject)
    {
        return str_replace(' desc', '', $subject);
    }

    /**
     * @return string
     */
    public function getSqlOrder()
    {
        $descendingOrder = false !== strpos($this->queryParams['order'], 'desc');
        $filterColumn = $this->removeDirection($this->queryParams['order']);

        $orderByClause = 'ORDER BY {' . $filterColumn . '}';

        if ($descendingOrder) {
            $orderByClause = $orderByClause . ' DESC';
        }

        return $orderByClause . ' ';
    }

    /**
     * @return array
     */
    public function getSqlFilters()
    {
        $whereFilters = array();

        foreach ($this->queryParams['filter'] as $column => $value) {
            $whereFilters = $this->appendSqlFilter($value, $column, $whereFilters);
        }

        $filters = array(
            self::SQL_CLAUSE_WHERE => implode("\n", $whereFilters)
        );

        $filters = $this->appendSqlSearchFilter($filters);

        return $filters;
    }

    private function hasSearchFilter()
    {
        return array_key_exists('keywords', $this->queryParams['filter']);
    }

    /**
     * @param $filters
     * @return mixed
     */
    private function appendSqlSearchFilter($filters)
    {
        if (!$this->hasSearchFilter()) {
            return $filters;
        }

        $parts = array_map(function ($index) {
            return sprintf(
                'AND (' .
                '{supplier_name} LIKE :keyword_%d OR '.
                '{product_reference} LIKE :keyword_%d OR ' .
                '{product_name} LIKE :keyword_%d OR ' .
                '{combination_name} LIKE :keyword_%d' .
                ')',
                $index, $index, $index, $index);
        }, range(0, count($this->queryParams['filter']['keywords']) - 1));

        $filters[self::SQL_CLAUSE_HAVING] = implode("\n", $parts);

        return $filters;
    }

    /**
     * @param $value
     * @param $column
     * @param array $filters
     * @return array
     */
    private function appendSqlFilter($value, $column, array $filters)
    {
        $column = Inflector::tableize($column);

        if ($column === 'keywords') {
            return $filters;
        }

        if ($column === 'category_id') {
            return $this->appendSqlCategoryFilter($column, $filters);
        }

        if (!is_array($value)) {
            $filters[] = sprintf('AND {%s} = :%s', $column, $column);

            return $filters;
        }

        $placeholders = array_map(function ($index) use ($column) {
            return ':' . $column . '_' . $index;
        }, array_keys($value));

        $filters[] = sprintf('AND {%s} IN (%s)', $column,  implode(',', $placeholders));

        return $filters;
    }

    /**
     * @return array
     */
    public function getSqlParams()
    {
        return array_merge(
            $this->getSqlPaginationParams(),
            $this->getSqlFiltersParams()
        );
    }

    /**
     * @return array
     */
    public function getSqlPaginationParams()
    {
        $maxResult = $this->queryParams['page_size'];
        $pageIndex = $this->queryParams['page_index'];
        $firstResult = ($pageIndex - 1) * $maxResult;

        return array(
            self::SQL_PARAM_MAX_RESULTS => (int)$maxResult,
            self::SQL_PARAM_FIRST_RESULT => (int)$firstResult
        );
    }

    /**
     * @return array
     */
    private function getSqlFiltersParams()
    {
        $sqlParams = array();

        if (count($this->queryParams['filter']) === 0) {
            return $sqlParams;
        }

        foreach ($this->queryParams['filter'] as $column => $value) {
            $sqlParams = $this->appendSqlFilterParams($column, $value, $sqlParams);
        }

        return $sqlParams;
    }

    /**
     * @param $column
     * @param $value
     * @param $sqlParams
     * @return mixed
     */
    private function appendSqlFilterParams($column, $value, $sqlParams)
    {
        $column = Inflector::tableize($column);

        if ($column === 'keywords') {
            return $this->appendSqlSearchFilterParam($value, $sqlParams);
        }

        if ($column === 'category_id') {
            return $this->appendSqlCategoryFilterParam($value, $sqlParams);
        }

        if (!is_array($value)) {
            $sqlParams[$column] = (int)$value;

            return $sqlParams;
        }

        array_map(function ($index, $value) use (&$sqlParams, $column) {
            $sqlParams[$column . '_' . $index] = (int)$value;
        }, array_keys($value), $value);

        return $sqlParams;
    }

    /**
     * @param $column
     * @param array $filters
     * @return array
     */
    private function appendSqlCategoryFilter($column, array $filters)
    {
        $filters[] = sprintf('AND FIND_IN_SET({%s}, %s)', $column, ':categories_ids');

        return $filters;
    }

    /**
     * @param $value
     * @param $sqlParams
     * @return mixed
     */
    private function appendSqlCategoryFilterParam($value, $sqlParams)
    {
        if (!is_array($value)) {
            $value = array($value);
        }

        $value = array_map('intval', $value);
        $sqlParams[':categories_ids'] = implode(',', $value);

        return $sqlParams;
    }

    private function appendSqlSearchFilterParam($value, $sqlParams)
    {
        if (!is_array($value)) {
            $value = array($value);
        }

        array_map(function ($index, $value) use (&$sqlParams) {
            $sqlParams['keyword_' . $index] = strval('%' . $value . '%');
        }, range(0, count($value) - 1), $value);

        return $sqlParams;
    }
}
