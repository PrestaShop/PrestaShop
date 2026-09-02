<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */
class SupplyOrderStateLangCore extends DataLangCore
{
    // Don't replace domain in init() with $this->domain for translation parsing
    protected $domain = 'Admin.Orderscustomers.Feature';

    protected $keys = ['id_supply_order_state'];

    protected $fieldsToUpdate = ['name'];
}
