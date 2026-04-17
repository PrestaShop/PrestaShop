<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Filter\FrontEndObject;

use PrestaShop\PrestaShop\Core\Filter\HashMapWhitelistFilter;

class ShopFilter extends HashMapWhitelistFilter
{
    public function __construct()
    {
        $whitelist = [
            'favicon',
            'logo',
            'name',
            'stores_icon',
        ];

        $this->whitelist($whitelist);
    }
}
