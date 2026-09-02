<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */
class LinkProxyCore
{
    private $link;

    public function __construct(Link $link)
    {
        $this->link = $link;
    }

    public function getPageLink($controller, $ssl = null, $id_lang = null, $request = null, $request_url_encode = false, $id_shop = null, $relative_protocol = false)
    {
        return $this->link->getPageLink($controller, $ssl, $id_lang, $request, $request_url_encode, $id_shop, $relative_protocol);
    }

    public function getProductLink($product, $alias = null, $category = null, $ean13 = null, $id_lang = null, $id_shop = null, $ipa = 0, $force_routes = false, $relative_protocol = false, $add_anchor = false, $extra_params = [])
    {
        return $this->link->getProductLink($product, $alias, $category, $ean13, $id_lang, $id_shop, $ipa, $force_routes, $relative_protocol, $add_anchor, $extra_params);
    }
}
