<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Addon;

interface AddonRepositoryInterface
{
    /**
     * @param string $name theme name
     *
     * @return AddonInterface the theme or module
     */
    public function getInstanceByName($name);

    /**
     * @param AddonListFilter $filter
     *
     * @return AddonInterface[] retrieve a list of addons, regarding the $filter used
     */
    public function getFilteredList(AddonListFilter $filter);

    /**
     * @return AddonInterface[] retrieve a list of addons, regardless any $filter
     */
    public function getList();
}
