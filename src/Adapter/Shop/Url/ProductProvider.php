<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\Shop\Url;

use Link;
use PrestaShop\PrestaShop\Core\Shop\Url\UrlProviderInterface;

/**
 * Class ProductProvider provides base Front Office URL for context shop.
 */
class ProductProvider implements UrlProviderInterface
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
     * Create a link to a product.
     *
     * @param int|null $productId
     * @param string|null $rewrite
     *
     * @return string
     */
    public function getUrl(?int $productId = null, ?string $rewrite = null): string
    {
        return $this->link->getProductLink((int) $productId, $rewrite);
    }
}
