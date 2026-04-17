<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Adapter\Cache\Clearer;

use Media;
use PrestaShop\PrestaShop\Core\Cache\Clearer\CacheClearerInterface;

/**
 * Class MediaCacheClearer clears Front Office theme's Javascript & CSS cache.
 *
 * @internal
 */
final class MediaCacheClearer implements CacheClearerInterface
{
    /**
     * {@inheritdoc}
     */
    public function clear()
    {
        Media::clearCache();
    }
}
