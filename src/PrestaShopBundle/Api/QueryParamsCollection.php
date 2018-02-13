<?php
/**
 * 2007-2018 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
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
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2018 PrestaShop SA
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShopBundle\Api;

use Doctrine\Common\Util\Inflector;
use PrestaShopBundle\Exception\InvalidPaginationParamsException;
use Symfony\Component\HttpFoundation\Request;

abstract class QueryParamsCollection
{
    const SQL_PARAM_FIRST_RESULT = 'first_result';

    const SQL_PARAM_MAX_RESULTS = 'max_results';

    const SQL_CLAUSE_WHERE = 'where';

    const SQL_CLAUSE_HAVING = 'having';

    /**
     * @var array
     */
    protected $queryParams = array();

    protected $defaultPageIndex = 1;

    protected $defaultPageSize = 100;

    /**
     * @return array
     */
    public function getQueryParams()
    {
        return $this->queryParams;
    }

    /**
     * @return int
     */
    public function getDefaultPageIndex()
    {
        return $this->defaultPageIndex;
    }

    /**
     * @return int
     */
    public function getDefaultPageSize()
    {
        return $this->defaultPageSize;
    }

    /**
     * @param $pageSize int
     * @return $this
     */
    public function setPageSize($pageSize)
    {
        $this->queryParams['page_size'] = (int) $pageSize;

        return $this;
    }

    /**
     * @param $pageIndex int
     * @return $this
     */
    public function setPageIndex($pageIndex)
    {
        $this->queryParams['page_index'] = (int) $pageIndex;

        return $this;
    }

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
    protected function excludeUnknownParams(array $queryParams)
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
    protected function parseFilterParams(array $queryParams, Request $request)
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
                    return is_int($value) || (is_string($value) && strlen(trim($value)) > 0);
                });
            }

            $filterParams[$filter] = $allParameters[$filter];
            if (is_array($filterParams[$filter]) && count($filterParams[$filter]) === 0) {
                unset($filterParams[$filter]);
            }
        });

        $queryParams['filter'] = $filterParams;

        return $queryParams;
    }

    /**
     * @return array
     */
    abstract protected function getValidFilterParams();

    /**
     * @param array $queryParams
     * @return array
     */
    protected function parsePaginationParams(array $queryParams)
    {

        if (!array_key_exists('page_index', $queryParams)) {
            $queryParams['page_index'] = $this->getDefaultPageIndex();
        }

        if (!array_key_exists('page_size', $queryParams)) {
            $queryParams['page_size'] = $this->getDefaultPageSize();
        }

        $queryParams['page_size'] = (int)$queryParams['page_size'];
        $queryParams['page_index'] = (int)$queryParams['page_index'];

        if (
            $queryParams['page_size'] > $this->getDefaultPageSize() ||
            $queryParams['page_size'] < 1
        ) {
            throw new InvalidPaginationParamsException(
                sprintf(
                    'A page size should be an integer greater than 1 and fewer than %s',
                    $this->getDefaultPageSize()
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
    protected function getValidPaginationParams() {
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
    protected function parseOrderParams(array $queryParams)
    {
        if (!array_key_exists('order', $queryParams)) {
            $queryParams = $this->setDefaultOrderParam($queryParams);
        }

        if (!is_array($queryParams['order'])) {
            $queryParams['order'] = (array) $queryParams['order'];
        }

        foreach ($queryParams['order'] as $key => &$order) {
            $order = strtolower($order);
            $filterColumn = $this->removeDirection($order);

            if (!in_array($filterColumn, $this->getValidOrderParams())) {
                unset($queryParams['order'][$key]);
            }
        }

        if (empty($queryParams['order'])) {
            $queryParams = $this->setDefaultOrderParam($queryParams);
        }

        return $queryParams;
    }

    /**
     * @return array
     */
    abstract protected function getValidOrderParams();

    /**
     * @param $queryParams
     * @return mixed
     */
    abstract protected function setDefaultOrderParam($queryParams);

    /**
     * @param $subject
     * @return mixed
     */
    protected function removeDirection($subject)
    {
        $subject = str_replace(' asc', '', $subject);
        return str_replace(' desc', '', $subject);
    }

    /**
     * @return string
     */
    public function getSqlOrder()
    {
        $implodableOrder = array();

        foreach ($this->queryParams['order'] as $order) {
            $descendingOrder = false !== strpos($order, 'desc');
            $filterColumn = $this->removeDirection($order);

            $orderFiltered = '{' . $filterColumn . '}';

            if ($descendingOrder) {
                $orderFiltered = $orderFiltered . ' DESC';
            }

            $implodableOrder[] = $orderFiltered;
        }

        return 'ORDER BY ' . implode(', ', $implodableOrder) . ' ';
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

    /**
     * @return bool
     */
    protected function hasSearchFilter()
    {
        return array_key_exists('keywords', $this->queryParams['filter']);
    }

    /**
     * @param $value
     * @param $column
     * @param array $filters
     * @return array
     */
    protected function appendSqlFilter($value, $column, array $filters)
    {
        $column = Inflector::tableize($column);

        if ('attributes' === $column) {
            return $this->appendSqlAttributesFilter($filters, $value);
        }

        if ('features' === $column) {
            return $this->appendSqlFeaturesFilter($filters, $value);
        }

        if ('keywords' === $column) {
            return $filters;
        }

        if ('category_id' === $column) {
            return $this->appendSqlCategoryFilter($filters);
        }

        if ('date_add' === $column) {
            return $this->appendSqlDateAddFilter($filters, $value);
        }

        if ('active' === $column) {
            return $this->appendSqlActiveFilter($filters, $value);
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
    protected function appendSqlFilterParams($column, $value, $sqlParams)
    {
        $column = Inflector::tableize($column);

        if ('attributes' === $column) {
            return $this->appendSqlAttributesFilterParam($value, $sqlParams);
        }

        if ('features' === $column) {
            return $this->appendSqlFeaturesFilterParam($value, $sqlParams);
        }

        if ('keywords' === $column) {
            return $this->appendSqlSearchFilterParam($value, $sqlParams);
        }

        if ('category_id' === $column) {
            return $this->appendSqlCategoryFilterParam($value, $sqlParams);
        }

        if ('date_add' === $column) {
            return $this->appendSqlDateAddFilterParam($value, $sqlParams);
        }

        if ('active' === $column) {
            return $this->appendSqlActiveFilterParam($value, $sqlParams);
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
     * @param array $filters
     * @return array
     */
    protected function appendSqlCategoryFilter(array $filters)
    {
        $filters[] = sprintf('AND FIND_IN_SET({%s}, %s)', 'category_id', ':categories_ids');

        return $filters;
    }

    /**
     * @param $value
     * @param $sqlParams
     * @return mixed
     */
    protected function appendSqlCategoryFilterParam($value, $sqlParams)
    {
        if (!is_array($value)) {
            $value = array($value);
        }

        $value = array_map('intval', $value);
        $sqlParams[':categories_ids'] = implode(',', $value);

        return $sqlParams;
    }


    /**
     * @param array $filters
     * @param dateAdd
     * @return array
     */
    protected function appendSqlDateAddFilter(array $filters, $dateAdd)
    {
        if (!is_array($dateAdd)) {
            $dateAdd = array($dateAdd);
        }

        if (array_key_exists('sup', $dateAdd)) {
            $search = ($this->isTimestamp($dateAdd['sup']) ? 'UNIX_TIMESTAMP(%s)' : '%s');
            $filters[] = sprintf('AND '.$search.' >= %s', '{date_add}', ':date_add_sup');
        }
        if (array_key_exists('inf', $dateAdd)) {
            $search = ($this->isTimestamp($dateAdd['inf']) ? 'UNIX_TIMESTAMP(%s)' : '%s');
            $filters[] = sprintf('AND '.$search.' <= %s', '{date_add}', ':date_add_inf');
        }

        return $filters;
    }

    /**
     * @param $value
     * @param $sqlParams
     * @return mixed
     */
    protected function appendSqlDateAddFilterParam($value, $sqlParams)
    {
        if (!is_array($value)) {
            $value = array($value);
        }

        if (array_key_exists('sup', $value)) {
            $sqlParams[':date_add_sup'] = $value['sup'];
        }
        if (array_key_exists('inf', $value)) {
            $sqlParams[':date_add_inf'] = $value['inf'];
        }

        return $sqlParams;
    }

    /**
     * @param array $filters
     * @param active
     * @return array
     */
    protected function appendSqlActiveFilter(array $filters, $active)
    {
        if (in_array($active, array('0', '1'))) {
            $filters[] = sprintf('AND %s = %s', '{active}', ':active');
        }

        return $filters;
    }

    /**
     * @param $value
     * @param $sqlParams
     * @return mixed
     */
    protected function appendSqlActiveFilterParam($value, $sqlParams)
    {
        if (in_array($value, array('0', '1'))) {
            $sqlParams[':active'] = $value;
        }

        return $sqlParams;
    }


    /**
     * @param array $filters
     * @param $attributes
     * @return array
     */
    protected function appendSqlAttributesFilter(array $filters, $attributes)
    {
        if (!is_array($attributes)) {
            $attributes = array($attributes);
        }

        $attributesKeys = array_keys($attributes);
        array_walk($attributesKeys, function ($key) use (&$filters) {
            $filters[] = sprintf(
                'AND FIND_IN_SET(:attribute_%d, {attributes})',
                $key
            );
        });

        return $filters;
    }

    /**
     * @param array $value
     * @param $sqlParams
     * @return array
     */
    protected function appendSqlAttributesFilterParam($value, $sqlParams)
    {
        if (!is_array($value)) {
            $value = array($value);
        }

        array_map(function ($index, $value) use (&$sqlParams) {
            $sqlParams['attribute_' . $index] = strval($value);
        }, range(0, count($value) - 1), $value);

        return $sqlParams;
    }


    /**
     * @param array $filters
     * @param $attributes
     * @return array
     */
    protected function appendSqlFeaturesFilter(array $filters, $attributes)
    {
        if (!is_array($attributes)) {
            $attributes = array($attributes);
        }

        $attributesKeys = array_keys($attributes);
        array_walk($attributesKeys, function ($key) use (&$filters) {
            $filters[] = sprintf(
                'AND FIND_IN_SET(:feature_%d, {features})',
                $key
            );
        });

        return $filters;
    }

    /**
     * @param array $value
     * @param $sqlParams
     * @return array
     */
    protected function appendSqlFeaturesFilterParam($value, $sqlParams)
    {
        if (!is_array($value)) {
            $value = array($value);
        }

        array_map(function ($index, $value) use (&$sqlParams) {
            $sqlParams['feature_' . $index] = strval($value);
        }, range(0, count($value) - 1), $value);

        return $sqlParams;
    }


    /**
     * @param $filters
     * @return mixed
     */
    protected function appendSqlSearchFilter($filters)
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

    protected function appendSqlSearchFilterParam($value, $sqlParams)
    {
        if (!is_array($value)) {
            $value = array($value);
        }

        array_map(function ($index, $value) use (&$sqlParams) {
            $sqlParams['keyword_' . $index] = strval('%' . $value . '%');
        }, range(0, count($value) - 1), $value);

        return $sqlParams;
    }

    protected function isTimestamp($timestamp)
    {
        $check = (is_int($timestamp) OR is_float($timestamp)) ? $timestamp : (string) (int) $timestamp;

        return  ($check === $timestamp)
            AND ( (int) $timestamp <=  PHP_INT_MAX)
            AND ( (int) $timestamp >= ~PHP_INT_MAX);
    }
}
