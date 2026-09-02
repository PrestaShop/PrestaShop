<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShopBundle\Service\DataProvider\Admin;

/**
 * Data provider for new Architecture, about Module object model.
 *
 * This class will provide data from DB / ORM about Modules for the Admin interface.
 */
interface ModuleInterface
{
    /**
     * Return all module entities coming from DB.
     *
     * @return array|bool
     */
    public function getAllModules();
}
