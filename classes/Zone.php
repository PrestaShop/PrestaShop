<?php
/**
 * 2007-2018 PrestaShop.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
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
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2018 PrestaShop SA
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

/**
 * Class ZoneCore.
 */
class ZoneCore extends ObjectModel
{
    /** @var string Name */
    public $name;

    /** @var bool Zone status */
    public $active = true;

    /**
     * @see ObjectModel::$definition
     */
    public static $definition = array(
        'table' => 'zone',
        'primary' => 'id_zone',
        'fields' => array(
            'name' => array('type' => self::TYPE_STRING, 'validate' => 'isGenericName', 'required' => true, 'size' => 64),
            'active' => array('type' => self::TYPE_BOOL, 'validate' => 'isBool'),
        ),
    );

    protected $webserviceParameters = array();

    /**
     * Get all available geographical zones.
     *
     * @param bool $active
     * @param bool $activeFirst
     *
     * @return array Zones
     */
    public static function getZones($active = false, $activeFirst = false)
    {
        $cacheId = 'Zone::getZones_' . (bool) $active;
        if (!Cache::isStored($cacheId)) {
            $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('
				SELECT *
				FROM `' . _DB_PREFIX_ . 'zone`
				' . ($active ? 'WHERE active = 1' : '') . '
				ORDER BY ' . ($activeFirst ? '`active` DESC,' : '') . ' `name` ASC
			');
            Cache::store($cacheId, $result);

            return $result;
        }

        return Cache::retrieve($cacheId);
    }

    /**
     * Get a zone ID from its default language name.
     *
     * @param string $name
     *
     * @return int id_zone
     */
    public static function getIdByName($name)
    {
        return Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue('
			SELECT `id_zone`
			FROM `' . _DB_PREFIX_ . 'zone`
			WHERE `name` = \'' . pSQL($name) . '\'
		');
    }

    /**
     * Delete a zone.
     *
     * @return bool Deletion result
     */
    public function delete()
    {
        if (parent::delete()) {
            // Delete regarding delivery preferences
            $result = Db::getInstance()->delete('carrier_zone', 'id_zone = ' . (int) $this->id);
            $result &= Db::getInstance()->delete('delivery', 'id_zone = ' . (int) $this->id);

            // Update Country & state zone with 0
            $result &= Db::getInstance()->update('country', array('id_zone' => 0), 'id_zone = ' . (int) $this->id);
            $result &= Db::getInstance()->update('state', array('id_zone' => 0), 'id_zone = ' . (int) $this->id);

            return $result;
        }

        return false;
    }
}
