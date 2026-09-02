<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShopBundle\Api;

class QueryStockParamsCollection extends QueryParamsCollection
{
    /**
     * @param array $queryParams
     *
     * @return array|mixed
     */
    protected function parseOrderParams(array $queryParams)
    {
        $queryParams = parent::parseOrderParams($queryParams);

        if (array_key_exists('low_stock', $queryParams) && 1 == $queryParams['low_stock']) {
            array_unshift($queryParams['order'], 'product_low_stock_alert desc');
        }

        return $queryParams;
    }

    /**
     * @return array
     */
    protected function getValidFilterParams()
    {
        return [
            'productId',
            'supplier_id',
            'category_id',
            'keywords',
            'attributes',
            'features',
            'active',
        ];
    }

    /**
     * @return array
     */
    protected function getValidOrderParams()
    {
        return [
            'product',
            'product_id',
            'product_name',
            'combination_id',
            'reference',
            'supplier',
            'available_quantity',
            'physical_quantity',
            'active',
            'low_stock',
        ];
    }

    /**
     * @param array $queryParams
     *
     * @return mixed
     */
    protected function setDefaultOrderParam($queryParams)
    {
        $queryParams['order'] = ['product_id DESC'];

        return $queryParams;
    }
}
