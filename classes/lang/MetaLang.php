<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */
class MetaLangCore extends DataLangCore
{
    // Don't replace domain in init() with $this->domain for translation parsing
    protected $domain = 'Shop.Navigation';

    protected $keys = ['id_meta', 'id_shop'];

    protected $fieldsToUpdate = ['title', 'description', 'url_rewrite'];
}
