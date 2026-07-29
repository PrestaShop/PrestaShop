<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Adapter\Group;

use Db;
use Group;

/**
 * This class will provide data from DB / ORM about Group.
 */
class GroupDataProvider
{
    /**
     * Return available groups.
     *
     * @param int $id_lang
     * @param bool $filterByShop When true, restricts the result to the groups of the current shop
     *                           context. The value is a flag, not a shop id.
     *
     * @return array Groups
     */
    public function getGroups($id_lang, $filterByShop = false)
    {
        return Group::getGroups($id_lang, $filterByShop);
    }

    /**
     * Return current group object
     * Use context.
     *
     * @return Group Group object
     */
    public static function getCurrent()
    {
        return Group::getCurrent();
    }

    public function getAllGroupIds(): array
    {
        return Group::getAllGroupIds();
    }
}
