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

namespace PrestaShopBundle\Api;

use Doctrine\Common\Util\Inflector;
use PrestaShopBundle\Exception\InvalidPaginationParamsException;
use Symfony\Component\HttpFoundation\Request;

abstract class QueryParamsCollection
{
    public const SQL_PARAM_FIRST_RESULT = 'first_result';

    public const SQL_PARAM_MAX_RESULTS = 'max_results';

    public const SQL_CLAUSE_WHERE = 'where';

    public const SQL_CLAUSE_HAVING = 'having';

    /**
     * @var array
     */
    protected $queryParams = [];

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
     * @param int $pageSize
     *
     * @return $this
     */
    public function setPageSize($pageSize)
    {
        $this->queryParams['page_size'] = (int) $pageSize;

        return $this;
    }

    /**
     * @param int $pageIndex
     *
     * @return $this
     */
    public function setPageIndex($pageIndex)
    {
        $this->queryParams['page_index'] = (int) $pageIndex;

        return $this;
    }

    /**
     * @param Request $request
     *
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
     * @param array $queryParams
     * @param array $allParams
     *
     * @return $this
     */
    public function fromArray(array $queryParams, array $allParams = []): QueryParamsCollection
    {
        $queryParams = $this->excludeUnknownParams($queryParams);
        $queryParams = $this->parsePaginationParams($queryParams);
        $queryParams = $this->parseOrderParams($queryParams);

        if (empty($allParams)) {
            $allParams = $queryParams;
        }
        $this->queryParams = $this->parseFilterParamsArray($queryParams, $allParams);

        return $this;
    }

    /**
     * @param array $queryParams
     *
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
     *
     * @return array
     */
    protected function parseFilterParams(array $queryParams, Request $request)
    {
        $allParameters = array_merge(
            $request->attributes->all(),
            $request->query->all()
        );

        return $this->parseFilterParamsArray($queryParams, $allParameters);
    }

    /**
     * @param array $queryParams
     * @param array $allParameters
     *
     * @return array
     */
    protected function parseFilterParamsArray(array $queryParams, array $allParameters): array
    {
        $filters = array_filter(array_keys($allParameters), function ($filter) {
            return in_array($filter, $this->getValidFilterParams());
        });

        $filterParams = [];
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
     *
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

        $queryParams['page_size'] = (int) $queryParams['page_size'];
        $queryParams['page_index'] = (int) $queryParams['page_index'];

        if (
            $queryParams['page_size'] > $this->getDefaultPageSize() ||
            $queryParams['page_size'] < 1
        ) {
            throw new InvalidPaginationParamsException(sprintf('A page size should be an integer greater than 1 and fewer than %s', $this->getDefaultPageSize()));
        }

        if ($queryParams['page_index'] < 1) {
            throw new InvalidPaginationParamsException();
        }

        return $queryParams;
    }

    /**
     * @return array
     */
    protected function getValidPaginationParams()
    {
        return [
            'page_size',
            'page_index',
            'order',
        ];
    }

    /**
     * @param array $queryParams
     *
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
     * @param array $queryParams
     *
     * @return mixed
     */
    abstract protected function setDefaultOrderParam($queryParams);

    /**
     * @param string $subject
     *
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
        $implodableOrder = [];

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
        $whereFilters = [];

        foreach ($this->queryParams['filter'] as $column => $value) {
            $whereFilters = $this->appendSqlFilter($value, $column, $whereFilters);
        }

        $filters = [
            self::SQL_CLAUSE_WHERE => implode("\n", $whereFilters),
        ];

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
     * @param int|array<int> $value
     * @param string $column
     * @param array $filters
     *
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

        $filters[] = sprintf('AND {%s} IN (%s)', $column, implode(',', $placeholders));

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

        return [
            self::SQL_PARAM_MAX_RESULTS => (int) $maxResult,
            self::SQL_PARAM_FIRST_RESULT => (int) $firstResult,
        ];
    }

    /**
     * @return array
     */
    private function getSqlFiltersParams()
    {
        $sqlParams = [];

        if (count($this->queryParams['filter']) === 0) {
            return $sqlParams;
        }

        foreach ($this->queryParams['filter'] as $column => $value) {
            $sqlParams = $this->appendSqlFilterParams($column, $value, $sqlParams);
        }

        return $sqlParams;
    }

    /**
     * @param string $column
     * @param array $value
     * @param int|array<int> $sqlParams
     *
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
            $sqlParams[$column] = (int) $value;

            return $sqlParams;
        }

        array_map(function ($index, $value) use (&$sqlParams, $column) {
            $sqlParams[$column . '_' . $index] = (int) $value;
        }, array_keys($value), $value);

        return $sqlParams;
    }

    /**
     * @param array $filters
     *
     * @return array
     */
    protected function appendSqlCategoryFilter(array $filters)
    {
        $filters[] = 'AND EXISTS(SELECT 1 FROM {table_prefix}category_product cp
        WHERE cp.id_product=p.id_product AND FIND_IN_SET(cp.id_category, :categories_ids))';

        return $filters;
    }

    /**
     * @param int|array<int> $value
     * @param array $sqlParams
     *
     * @return mixed
     */
    protected function appendSqlCategoryFilterParam($value, $sqlParams)
    {
        if (!is_array($value)) {
            $value = [$value];
        }

        $value = array_map('intval', $value);
        $sqlParams[':categories_ids'] = implode(',', $value);

        return $sqlParams;
    }

    /**
     * @param array $filters
     * @param int|array<int> $dateAdd
     *
     * @return array
     */
    protected function appendSqlDateAddFilter(array $filters, $dateAdd)
    {
        if (!is_array($dateAdd)) {
            $dateAdd = [$dateAdd];
        }

        if (array_key_exists('sup', $dateAdd)) {
            $search = ($this->isTimestamp($dateAdd['sup']) ? 'UNIX_TIMESTAMP(%s)' : '%s');
            $filters[] = sprintf('AND ' . $search . ' >= %s', '{date_add}', ':date_add_sup');
        }
        if (array_key_exists('inf', $dateAdd)) {
            $search = ($this->isTimestamp($dateAdd['inf']) ? 'UNIX_TIMESTAMP(%s)' : '%s');
            $filters[] = sprintf('AND ' . $search . ' <= %s', '{date_add}', ':date_add_inf');
        }

        return $filters;
    }

    /**
     * @param int|array<int> $value
     * @param array $sqlParams
     *
     * @return mixed
     */
    protected function appendSqlDateAddFilterParam($value, $sqlParams)
    {
        if (!is_array($value)) {
            $value = [$value];
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
     * @param string|int $active
     *
     * @return array
     */
    protected function appendSqlActiveFilter(array $filters, $active)
    {
        if (in_array($active, ['0', '1'])) {
            $filters[] = sprintf('AND %s = %s', '{active}', ':active');
        }

        return $filters;
    }

    /**
     * @param int|string $value
     * @param array $sqlParams
     *
     * @return mixed
     */
    protected function appendSqlActiveFilterParam($value, $sqlParams)
    {
        if (in_array($value, ['0', '1'])) {
            $sqlParams[':active'] = $value;
        }

        return $sqlParams;
    }

    /**
     * @param array $filters
     * @param int|array<int> $attributes
     *
     * @return array
     */
    protected function appendSqlAttributesFilter(array $filters, $attributes)
    {
        if (!is_array($attributes)) {
            $attributes = [$attributes];
        }

        $attributesKeys = array_keys($attributes);
        array_walk($attributesKeys, function ($key) use (&$filters) {
            $filters[] = sprintf('AND EXISTS(SELECT 1
                    FROM {table_prefix}product_attribute_combination pac
                        LEFT JOIN {table_prefix}attribute a ON (
                            pac.id_attribute = a.id_attribute
                        )
                    WHERE pac.id_product_attribute=pa.id_product_attribute
                    AND a.id_attribute=:attribute_id_%d
                    AND a.id_attribute_group=:attribute_group_id_%d)', $key, $key);
        });

        return $filters;
    }

    /**
     * @param string|array<string> $value
     * @param array $sqlParams
     *
     * @return array
     */
    protected function appendSqlAttributesFilterParam($value, $sqlParams)
    {
        if (!is_array($value)) {
            $value = [$value];
        }

        array_map(function ($index, $value) use (&$sqlParams) {
            list($idAttributeGroup, $idAttribute) = explode(':', $value);
            $sqlParams['attribute_id_' . $index] = (string) $idAttribute;
            $sqlParams['attribute_group_id_' . $index] = (string) $idAttributeGroup;
        }, range(0, count($value) - 1), $value);

        return $sqlParams;
    }

    /**
     * @param array $filters
     * @param int|array<int>$attributes
     *
     * @return array
     */
    protected function appendSqlFeaturesFilter(array $filters, $attributes)
    {
        if (!is_array($attributes)) {
            $attributes = [$attributes];
        }

        $attributesKeys = array_keys($attributes);
        array_walk($attributesKeys, function ($key) use (&$filters) {
            $filters[] = sprintf('AND EXISTS(SELECT 1
                    FROM {table_prefix}feature_product fp
                        LEFT JOIN  {table_prefix}feature f ON (
                            fp.id_feature = f.id_feature
                        )
                        LEFT JOIN {table_prefix}feature_shop fs ON (
                            fs.id_shop = :shop_id AND
                            fs.id_feature = f.id_feature
                        )
                        LEFT JOIN {table_prefix}feature_value fv ON (
                            f.id_feature = fv.id_feature AND
                            fp.id_feature_value = fv.id_feature_value
                        )
                    WHERE fv.custom = 0 AND fp.id_product=p.id_product
                    AND fp.id_feature=:feature_id_%d
                    AND fp.id_feature_value=:feature_value_id_%d)', $key, $key);
        });

        return $filters;
    }

    /**
     * @param string|array<string> $value
     * @param array $sqlParams
     *
     * @return array
     */
    protected function appendSqlFeaturesFilterParam($value, $sqlParams)
    {
        if (!is_array($value)) {
            $value = [$value];
        }

        array_map(function ($index, $value) use (&$sqlParams) {
            list($idFeature, $idFeatureValue) = explode(':', $value);
            $sqlParams['feature_id_' . $index] = (string) $idFeature;
            $sqlParams['feature_value_id_' . $index] = (string) $idFeatureValue;
        }, range(0, count($value) - 1), $value);

        return $sqlParams;
    }

    /**
     * @param array$filters
     *
     * @return mixed
     */
    protected function appendSqlSearchFilter($filters)
    {
        if (!$this->hasSearchFilter()) {
            return $filters;
        }

        if (!is_array($this->queryParams['filter']['keywords'])) {
            $this->queryParams['filter']['keywords'] = (array) $this->queryParams['filter']['keywords'];
        }

        $parts = array_map(function ($index) {
            return sprintf(
                'AND (' .
                '{supplier_name} LIKE :keyword_%d OR ' .
                '{product_reference} LIKE :keyword_%d OR ' .
                '{product_name} LIKE :keyword_%d OR ' .
                '{combination_name} LIKE :keyword_%d' .
                ')',
                $index,
                $index,
                $index,
                $index
            );
        }, range(0, count($this->queryParams['filter']['keywords']) - 1));

        $filters[self::SQL_CLAUSE_HAVING] = implode("\n", $parts);

        return $filters;
    }

    protected function appendSqlSearchFilterParam($value, $sqlParams)
    {
        if (!is_array($value)) {
            $value = [$value];
        }

        array_map(function ($index, $value) use (&$sqlParams) {
            $sqlParams['keyword_' . $index] = (string) ('%' . $value . '%');
        }, range(0, count($value) - 1), $value);

        return $sqlParams;
    }

    protected function isTimestamp($timestamp)
    {
        $check = (is_int($timestamp) || is_float($timestamp)) ? $timestamp : (string) (int) $timestamp;

        return ($check === $timestamp)
            && ((int) $timestamp <= PHP_INT_MAX)
            && ((int) $timestamp >= ~PHP_INT_MAX);
    }
}
