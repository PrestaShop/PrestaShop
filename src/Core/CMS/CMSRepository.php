<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\CMS;

use PrestaShop\PrestaShop\Adapter\Entity\CMS;
use PrestaShop\PrestaShop\Core\Foundation\Database\EntityRepository;
use PrestaShop\PrestaShop\Core\Foundation\Database\Exception;

class CMSRepository extends EntityRepository
{
    /**
     * Return all CMSRepositories depending on $id_lang/$id_shop tuple.
     *
     * @param int $id_lang
     * @param int $id_shop
     *
     * @return array|null
     */
    public function i10nFindAll($id_lang, $id_shop)
    {
        $sql = '
			SELECT *
			FROM `' . $this->getTableNameWithPrefix() . '` c
			JOIN `' . $this->getPrefix() . 'cms_lang` cl ON c.`id_cms`= cl.`id_cms`
			WHERE cl.`id_lang` = ' . (int) $id_lang . '
			AND cl.`id_shop` = ' . (int) $id_shop . '

		';

        return $this->hydrateMany($this->db->select($sql));
    }

    /**
     * Return all CMSRepositories depending on $id_lang/$id_shop tuple.
     *
     * @param int $id_cms
     * @param int $id_lang
     * @param int $id_shop
     *
     * @return CMS|null
     *
     * @throws Exception
     */
    public function i10nFindOneById($id_cms, $id_lang, $id_shop)
    {
        $sql = '
			SELECT *
			FROM `' . $this->getTableNameWithPrefix() . '` c
			JOIN `' . $this->getPrefix() . 'cms_lang` cl ON c.`id_cms`= cl.`id_cms`
			WHERE c.`id_cms` = ' . (int) $id_cms . '
			AND cl.`id_lang` = ' . (int) $id_lang . '
			AND cl.`id_shop` = ' . (int) $id_shop . '
			LIMIT 0 , 1
		';

        return $this->hydrateOne($this->db->select($sql));
    }
}
