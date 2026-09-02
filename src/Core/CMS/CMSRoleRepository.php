<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\CMS;

class CMSRoleRepository extends \PrestaShop\PrestaShop\Core\Foundation\Database\EntityRepository
{
    /**
     * Return all CMSRoles which are already associated.
     *
     * @return array|null
     */
    public function getCMSRolesAssociated()
    {
        $sql = '
			SELECT *
			FROM `' . $this->getTableNameWithPrefix() . '`
			WHERE `id_cms` != 0';

        return $this->hydrateMany($this->db->select($sql));
    }
}
