<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Adapter\Shop\Url;

use Link;
use PrestaShop\PrestaShop\Core\Shop\Url\UrlProviderInterface;

/**
 * Class CategoryProvider provides base Front Office URL for context shop.
 */
final class CategoryProvider implements UrlProviderInterface
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
     * Create a link to a category.
     *
     * @param int $categoryId
     * @param string $rewrite
     *
     * @return string
     */
    public function getUrl($categoryId = null, $rewrite = null)
    {
        return $this->link->getCategoryLink((int) $categoryId, $rewrite);
    }
}
