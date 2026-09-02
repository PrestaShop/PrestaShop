<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

use PrestaShop\PrestaShop\Adapter\Module\Configuration\ModuleComplexConfigurationInterface;
use PrestaShop\PrestaShop\Core\Module\ModuleInterface;

class MyComplexModuleConfiguration implements ModuleComplexConfigurationInterface
{
    public function run(ModuleInterface $module, array $params)
    {
    }
}
