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

namespace PrestaShop\PrestaShop\Adapter\Product;

use PrestaShop\PrestaShop\Core\Exception\ProductException;

/**
 * Can manage filter parameters from request in Product Catalogue Page.
 * For internal use only.
 */
final class ListParametersUpdater
{
    /**
     * In case of position ordering all the filters should be reset.
     *
     * @param array $filterParameters
     * @param string $orderBy
     * @param bool $hasCategoryFilter
     *
     * @return array $filterParameters
     */
    public function cleanFiltersForPositionOrdering($filterParameters, $orderBy, $hasCategoryFilter)
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
     * @param array $queryFilterParameters
     * @param array $persistedFilterParameters
     * @param array $defaultFilterParameters
     *
     * @return array
     *
     * @throws ProductException
     */
    public function buildListParameters(
        array $queryFilterParameters,
        array $persistedFilterParameters,
        array $defaultFilterParameters
    ) {
        $filters = [
            'offset' => (int) $this->getParameter(
                'offset',
                $queryFilterParameters,
                $persistedFilterParameters,
                $defaultFilterParameters
            ),
            'limit' => (int) $this->getParameter(
                'limit',
                $queryFilterParameters,
                $persistedFilterParameters,
                $defaultFilterParameters
            ),
            'orderBy' => (string) $this->getParameter(
                'orderBy',
                $queryFilterParameters,
                $persistedFilterParameters,
                $defaultFilterParameters
            ),
            'sortOrder' => (string) $this->getParameter(
                'sortOrder',
                $queryFilterParameters,
                $persistedFilterParameters,
                $defaultFilterParameters
            ),
        ];

        /*
         * We need to force the sort order when the order by
         * is set to position_ordering
         */
        if ('position_ordering' === $filters['orderBy']) {
            $filters['sortOrder'] = 'asc';
        }

        return $filters;
    }

    /**
     * @param string $parameterName
     * @param array $queryFilterParameters
     * @param array $persistedFilterParameters
     * @param array $defaultFilterParameters
     *
     * @return string|int
     *
     * @throws ProductException
     */
    private function getParameter(
        $parameterName,
        array $queryFilterParameters,
        array $persistedFilterParameters,
        array $defaultFilterParameters
    ) {
        if (isset($queryFilterParameters[$parameterName])) {
            $value = $queryFilterParameters[$parameterName];
        } elseif (isset($persistedFilterParameters[$parameterName])) {
            $value = $persistedFilterParameters[$parameterName];
        } elseif (isset($defaultFilterParameters[$parameterName])) {
            $value = $defaultFilterParameters[$parameterName];
        } else {
            throw new ProductException('Could not find the parameter %s', 'Admin.Notifications.Error', [$parameterName]);
        }

        if ($value === 'last' && isset($persistedFilterParameters['last_' . $parameterName])) {
            $value = $persistedFilterParameters['last_' . $parameterName];
        }

        return $value;
    }
}
