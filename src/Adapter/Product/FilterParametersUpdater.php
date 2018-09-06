<?php
/**
 * 2007-2017 PrestaShop.
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
 * @copyright 2007-2017 PrestaShop SA
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShop\PrestaShop\Adapter\Product;

/**
 * Can manage filter parameters from request in Product Catalogue Page.
 * For internal use only.
 */
final class FilterParametersUpdater
{
    /**
     * In case of position ordering all the filters should be reset.
     *
     * @param bool $hasCategoryFilter
     * @param string $orderBy
     * @param array $filterParameters
     *
     * @return array $filterParameters
     */
    public function setPositionOrdering($filterParameters, $orderBy, $hasCategoryFilter)
    {
        if ($orderBy == 'position_ordering' && $hasCategoryFilter) {
            foreach (array_keys($filterParameters) as $key) {
                if (strpos($key, 'filter_column_') === 0) {
                    $filterParameters[$key] = '';
                }
            }
        }

        return $filterParameters;
    }

    /**
     * Gets previous Product query values from persistence.
     *
     * @param array $filterParameters
     * @param string $offset
     * @param string $limit
     * @param string $orderBy
     * @param string $sortOrder
     *
     * @return array
     */
    public function setValues(
        array $filterParameters,
        $offset,
        $limit,
        $orderBy,
        $sortOrder
    ) {
        return [
            'offset' => $this->getOffset($offset, $filterParameters),
            'limit' => $this->getLimit($limit, $filterParameters),
            'orderBy' => $this->getOrderBy($orderBy, $filterParameters),
            'sortOrder' => $this->getSortOrder($sortOrder, $filterParameters),
        ];
    }

    /**
     * @param int $offset
     * @param array $filterParameters
     *
     * @return int
     */
    private function getOffset($offset, array $filterParameters)
    {
        return ($offset === 'last' && isset($filterParameters['last_offset'])) ? $filterParameters['last_offset'] : $offset;
    }

    /**
     * @param int $limit
     * @param array $filterParameters
     *
     * @return int
     */
    private function getLimit($limit, array $filterParameters)
    {
        return ($limit === 'last' && isset($filterParameters['last_limit'])) ? $filterParameters['last_limit'] : $limit;
    }

    /**
     * @param string $orderBy
     * @param array $filterParameters
     *
     * @return string
     */
    private function getOrderBy($orderBy, array $filterParameters)
    {
        return ($orderBy === 'last' && isset($filterParameters['last_orderBy'])) ? $filterParameters['last_orderBy'] : $orderBy;
    }

    /**
     * @param string $sortOrder
     * @param array $filterParameters
     *
     * @return string
     */
    private function getSortOrder($sortOrder, array $filterParameters)
    {
        return ($sortOrder === 'last' && isset($filterParameters['last_sortOrder'])) ? $filterParameters['last_sortOrder'] : $sortOrder;
    }
}
