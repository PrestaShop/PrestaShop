<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShopBundle\Translation\Provider;

use PrestaShopBundle\Translation\Exception\UnsupportedModuleException;

/**
 * Defines that this provider may need information from module.
 */
interface UseModuleInterface
{
    /**
     * Set the module name
     *
     * @param string $moduleName the name of the module
     *
     * @throws UnsupportedModuleException if the module is not supported or invalid
     */
    public function setModuleName($moduleName);
}
