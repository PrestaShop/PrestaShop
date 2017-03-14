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

use Symfony\Component\HttpFoundation\Request;
use RangeException;

class QueryParamsCollection
{
    const DEFAULT_PAGE_INDEX = '1';

    const DEFAULT_PAGE_SIZE = '100';

    const SQL_CLAUSE_ORDER = 'order';

    const SQL_CLAUSE_LIMIT = 'limit';

    const SQL_CLAUSE_LIMIT_PARAMS = 'limit_params';

    private $queryParams;

    /**
     * @param Request $request
     * @return $this
     */
    public function fromRequest(Request $request)
    {
        $queryParams = $request->query->all();

        if (!array_key_exists('page_index', $queryParams)) {
            $queryParams['page_index'] = self::DEFAULT_PAGE_INDEX;
        }

        if (!array_key_exists('page_size', $queryParams)) {
            $queryParams['page_size'] = self::DEFAULT_PAGE_SIZE;
        }

        $queryParams['page_size'] = (int) $queryParams['page_size'];
        $queryParams['page_index'] = (int) $queryParams['page_index'];

        if (
            $queryParams['page_size'] > self::DEFAULT_PAGE_SIZE ||
            $queryParams['page_size'] < 1
        ) {
            throw new RangeException(sprintf(
                'The page size should be greater than 1 and fewer than %s',
                self::DEFAULT_PAGE_SIZE
            ));
        }

        if (!array_key_exists('filter', $queryParams)) {
            $queryParams = $this->setDefaultFilter($queryParams);
        }

        $queryParams['filter'] = strtolower($queryParams['filter']);

        $filterColumn = $this->removeDirection($queryParams['filter']);
        if (!in_array($filterColumn, $this->getValidFilters())) {
            $queryParams = $this->setDefaultFilter($queryParams);
        }

        $this->queryParams = $queryParams;

        return $this;
    }

    public function toSqlClauses()
    {
        $descendingOrder = false !== strpos($this->queryParams['filter'], 'desc');
        $filterColumn = $this->removeDirection($this->queryParams['filter']);

        $orderClause = 'ORDER BY {' . $filterColumn . '}';

        if ($descendingOrder) {
            $orderClause = $orderClause . ' DESC';
        }

        $limitClause = 'LIMIT :first_result,:max_result';

        $maxResult = $this->queryParams['page_size'];
        $pageIndex = $this->queryParams['page_index'];
        $firstResult = ($pageIndex - 1) * $maxResult;

        return array(
            'order' => $orderClause . ' ',
            'limit' => $limitClause,
            'limit_params' => array(
                'max_result' => $maxResult,
                'first_result' => $firstResult,
            )
        );
    }

    private function getValidFilters()
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
    private function setDefaultFilter($queryParams)
    {
        $queryParams['filter'] = 'product DESC';

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
}
