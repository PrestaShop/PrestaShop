<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShopBundle\Api;

class QueryTranslationParamsCollection extends QueryParamsCollection
{
    protected $defaultPageIndex = 1;

    protected $defaultPageSize = 20;

    /**
     * @return array
     */
    protected function getValidFilterParams()
    {
        return [];
    }

    /**
     * @return array
     */
    protected function getValidOrderParams()
    {
        return [];
    }

    /**
     * @param array $queryParams
     *
     * @return mixed
     */
    protected function setDefaultOrderParam($queryParams)
    {
        $queryParams['order'] = ['unknown'];

        return $queryParams;
    }
}
