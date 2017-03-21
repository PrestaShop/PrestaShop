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
use Doctrine\DBAL\Driver\Statement;
use PDO;
use PrestaShopBundle\Exception\InvalidPaginationParamsException;
use Symfony\Component\HttpFoundation\Request;

class QueryParamsCollection
{
    const DEFAULT_PAGE_INDEX = 1;

    const DEFAULT_PAGE_SIZE = 100;

    const SQL_PARAM_FIRST_RESULT = 'first_result';

    const SQL_PARAM_MAX_RESULT = 'max_result';

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

        $queryParams = $this->parsePaginationParams($queryParams);
        $queryParams = $this->parseOrderParams($queryParams);
        $this->queryParams = $this->parseFilterParams($queryParams, $request);

        return $this;
    }

    /**
     * @param array $queryParams
     * @param Request $request
     * @return array
     */
    private function parseFilterParams(array $queryParams, Request $request)
    {
        $attributes = $request->attributes->all();
        $filterParams = array_filter($attributes, function ($attribute) {
            return in_array($attribute, $this->getValidFilterParams());
        }, ARRAY_FILTER_USE_KEY);

        $queryParams['filter'] = $filterParams;

        return $queryParams;
    }

    /**
     * @return array
     */
    public function getValidFilterParams()
    {
        return array('productId');
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
    public function getSqlFilter()
    {
        $sqlFilter = '';

        if (count($this->queryParams['filter']) > 0) {
            foreach ($this->queryParams['filter'] as $column => $value) {
                $column = Inflector::tableize($column);
                $sqlFilter = 'AND {' . $column . '} = :' . $column;
            }
        }

        return $sqlFilter;
    }

    /**
     * @param Statement $statement
     */
    public function bindValuesInStatement(Statement $statement)
    {
        $sqlParams = $this->getSqlParams();

        foreach ($sqlParams as $name => $value) {
            $statement->bindValue($name, $value, PDO::PARAM_INT);
        }
    }

    /**
     * @return array
     */
    public function getSqlParams()
    {
        return array_merge(
            $this->getSqlPaginationParams(),
            $this->getSqlFilterParams()
        );
    }

    /**
     * @return array
     */
    private function getSqlPaginationParams()
    {
        $maxResult = $this->queryParams['page_size'];
        $pageIndex = $this->queryParams['page_index'];
        $firstResult = ($pageIndex - 1) * $maxResult;

        return array(
            'max_result' => $maxResult,
            'first_result' => $firstResult
        );
    }

    /**
     * @return array
     */
    private function getSqlFilterParams()
    {
        $sqlParams = array();

        if (count($this->queryParams['filter']) > 0) {
            foreach ($this->queryParams['filter'] as $column => $value) {
                $column = Inflector::tableize($column);
                $sqlParams[$column] = $value;
            }
        }

        return $sqlParams;
    }
}
