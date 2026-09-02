<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */
class ContactLangCore extends DataLangCore
{
    // Don't replace domain in init() with $this->domain for translation parsing
    protected $domain = 'Admin.Shopparameters.Feature';

    protected $keys = ['id_contact'];

    protected $fieldsToUpdate = ['name', 'description'];
}
