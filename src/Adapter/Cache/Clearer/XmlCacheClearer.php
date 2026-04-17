<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Adapter\Cache\Clearer;

use PrestaShop\PrestaShop\Core\Cache\Clearer\CacheClearerInterface;
use Tools;

/**
 * Class XmlCacheClearer clears cache under /config/xml/ directory.
 *
 * @internal
 */
final class XmlCacheClearer implements CacheClearerInterface
{
    /**
     * {@inheritdoc}
     */
    public function clear()
    {
        Tools::clearXMLCache();
    }
}
