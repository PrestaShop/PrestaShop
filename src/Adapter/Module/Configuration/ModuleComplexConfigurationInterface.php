<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Adapter\Module\Configuration;

use PrestaShop\PrestaShop\Core\Module\ModuleInterface;

/**
 * @todo: document what is the contract of this interface.
 */
interface ModuleComplexConfigurationInterface
{
    /**
     * @param ModuleInterface $module
     * @param array $params
     *
     * @return mixed
     */
    public function run(ModuleInterface $module, array $params);
}
