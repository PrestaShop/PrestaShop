<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShopBundle\Api;

class QueryStockMovementParamsCollection extends QueryStockParamsCollection
{
    /**
     * @return array
     */
    protected function getValidFilterParams()
    {
        return [
            'productId',
            'product_name',
            'supplier_id',
            'category_id',
            'keywords',
            'attributes',
            'features',
            'date_add',
            'id_employee',
            'id_stock_mvt_reason',
        ];
    }

    /**
     * @return array
     */
    protected function getValidOrderParams()
    {
        return [
            'product_id',
            'product_name',
            'reference',
            'date_add',
            'id_stock_mvt',
        ];
    }

    /**
     * @param array $queryParams
     *
     * @return mixed
     */
    protected function setDefaultOrderParam($queryParams)
    {
        $queryParams['order'] = ['id_stock_mvt DESC'];

        return $queryParams;
    }
}
