<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Filter\FrontEndObject;

use PrestaShop\PrestaShop\Core\Filter\HashMapWhitelistFilter;

/**
 * Filters Customer objects that will be sent to the client.
 */
class CustomerFilter extends HashMapWhitelistFilter
{
    public function __construct()
    {
        $whitelist = [
            'addresses',
            'ape',
            'birthday',
            'company',
            'email',
            'firstname',
            'gender' => (new HashMapWhitelistFilter())
                ->whitelist([
                    'type',
                    'name',
                ]),
            'is_logged',
            'lastname',
            'newsletter',
            'newsletter_date_add',
            'optin',
            'siret',
            'website',
        ];

        $this->whitelist($whitelist);
    }
}
