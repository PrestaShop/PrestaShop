<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Adapter\Shop\Url;

use Link;
use PrestaShop\PrestaShop\Core\Shop\Url\UrlProviderInterface;

/**
 * Class BaseUrlProvider provides base Front Office URL for context shop.
 */
final class BaseUrlProvider implements UrlProviderInterface
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
     * {@inheritdoc}
     */
    public function getUrl()
    {
        return $this->link->getBaseLink();
    }
}
