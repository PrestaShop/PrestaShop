<?php
/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/OSL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to https://devdocs.prestashop.com/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
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
