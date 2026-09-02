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
     * @param bool $id_shop
     *
     * @return array Groups
     */
    public function getGroups($id_lang, $id_shop = false)
    {
        return Group::getGroups($id_lang, $id_shop);
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
