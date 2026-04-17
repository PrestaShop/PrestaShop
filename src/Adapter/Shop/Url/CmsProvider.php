<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Adapter\Shop\Url;

use Link;
use PrestaShop\PrestaShop\Core\Shop\Url\UrlProviderInterface;

/**
 * Class CmsProvider provides base Front Office URL for context shop.
 */
final class CmsProvider implements UrlProviderInterface
{
    /**
     * @var Link
     */
    private $link;

    /**
     * @param Link $link
     */
    public function __construct(Link $link)
    {
        $this->link = $link;
    }

    /**
     * Create a link to a cms.
     *
     * @param int $cmsId
     * @param string $rewrite
     *
     * @return string
     */
    public function getUrl($cmsId = null, $rewrite = null)
    {
        return $this->link->getCmsLink((int) $cmsId, $rewrite);
    }
}
