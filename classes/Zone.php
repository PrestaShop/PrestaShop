<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
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
    public static $definition = [
        'table' => 'zone',
        'primary' => 'id_zone',
        'fields' => [
            'name' => ['type' => self::TYPE_STRING, 'validate' => 'isGenericName', 'required' => true, 'size' => 64],
            'active' => ['type' => self::TYPE_BOOL, 'validate' => 'isBool'],
        ],
    ];

    protected $webserviceParameters = [];

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
        return (int) Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue(
            'SELECT `id_zone`
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
            $result = $result && Db::getInstance()->delete('delivery', 'id_zone = ' . (int) $this->id);

            // Update Country & state zone with 0
            $result = $result && Db::getInstance()->update('country', ['id_zone' => 0], 'id_zone = ' . (int) $this->id);

            return $result && Db::getInstance()->update('state', ['id_zone' => 0], 'id_zone = ' . (int) $this->id);
        }

        return false;
    }
}
