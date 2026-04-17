<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Filter\FrontEndObject;

use PrestaShop\PrestaShop\Core\Filter\HashMapWhitelistFilter;

/**
 * Filters Configuration objects that will be sent to the client.
 */
class ConfigurationFilter extends HashMapWhitelistFilter
{
    public function __construct()
    {
        $whitelist = [
            'display_taxes_label',
            'display_prices_tax_incl',
            'is_catalog',
            'opt_in',
            'quantity_discount',
            'return_enabled',
            'show_prices',
            'voucher_enabled',
        ];

        $this->whitelist($whitelist);
    }
}
