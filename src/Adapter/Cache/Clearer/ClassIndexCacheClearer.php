<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Adapter\Cache\Clearer;

use PrestaShop\Autoload\PrestashopAutoload;
use PrestaShop\PrestaShop\Core\Cache\Clearer\CacheClearerInterface;

/**
 * Class ClassIndexCacheClearer clears current class index and generates new one.
 *
 * @internal
 */
final class ClassIndexCacheClearer implements CacheClearerInterface
{
    /**
     * {@inheritdoc}
     */
    public function clear()
    {
        PrestashopAutoload::getInstance()->generateIndex();
    }
}
