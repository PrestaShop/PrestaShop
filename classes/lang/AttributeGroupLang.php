<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */
class AttributeGroupLangCore extends DataLangCore
{
    // Don't replace domain in init() with $this->domain for translation parsing
    protected $domain = 'Shop.Demo.Catalog';

    protected $keys = ['id_attribute_group'];

    protected $fieldsToUpdate = ['name', 'public_name'];
}
