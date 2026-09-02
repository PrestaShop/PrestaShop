<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Adapter\Cache\Clearer;

use PrestaShop\PrestaShop\Core\Cache\Clearer\CacheClearerInterface;
use Tools;

/**
 * Class SmartyCacheClearer clears Smarty cache.
 *
 * @internal
 */
final class SmartyCacheClearer implements CacheClearerInterface
{
    /**
     * {@inheritdoc}
     */
    public function clear()
    {
        Tools::clearSmartyCache();
    }
}
