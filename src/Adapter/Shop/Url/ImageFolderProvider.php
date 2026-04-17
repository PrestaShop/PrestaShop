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
 * Returns the base url of an image folder (product, categories, ...)
 */
class ImageFolderProvider implements UrlProviderInterface
{
    /**
     * @var Link
     */
    private $link;

    /**
     * @var string
     */
    private $imagesRelativeFolder;

    /**
     * @param Link $link
     * @param string $imagesRelativeFolder
     */
    public function __construct(
        Link $link,
        string $imagesRelativeFolder
    ) {
        $this->link = $link;
        $this->imagesRelativeFolder = $imagesRelativeFolder;
    }

    /**
     * Create a link to product images base folder.
     *
     * @return string
     */
    public function getUrl(): string
    {
        return rtrim($this->link->getBaseLink(), '/') . '/' . rtrim($this->imagesRelativeFolder, '/');
    }
}
