<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Module\DataProvider;

/**
 * Interface TabModuleListProviderInterface defines contract for tab module provider.
 */
interface TabModuleListProviderInterface
{
    /**
     * Get tab modules.
     *
     * @param string $tabClassName
     *
     * @return array
     */
    public function getTabModules($tabClassName);
}
