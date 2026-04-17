<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Filter\FrontEndObject;

use PrestaShop\PrestaShop\Core\Filter\HashMapWhitelistFilter;

/**
 * Filters Cart objects that will be sent to the client.
 */
class CartFilter extends HashMapWhitelistFilter
{
    public function __construct($productsFilter)
    {
        $whitelist = [
            'discounts',
            'minimalPurchase',
            'minimalPurchaseRequired',
            'products' => $productsFilter,
            'products_count',
            'subtotals',
            'summary_string',
            'totals',
            'vouchers',
        ];

        $this->whitelist($whitelist);
    }
}
