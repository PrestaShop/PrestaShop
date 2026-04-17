<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */
class CartRuleTypeLangCore extends DataLangCore
{
    // Don't replace domain in init() with $this->domain for translation parsing
    protected $domain = 'Admin.Catalog.Feature';

    protected $keys = ['id_cart_rule_type'];

    protected $fieldsToUpdate = ['name', 'description'];
}
