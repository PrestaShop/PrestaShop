<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */
class ShopGroupCore extends ObjectModel
{
    public $name;
    public $color;
    public $active = true;
    public $share_customer;
    public $share_stock;
    public $share_order;
    public $deleted;

    /**
     * @see ObjectModel::$definition
     */
    public static $definition = [
        'table' => 'shop_group',
        'primary' => 'id_shop_group',
        'fields' => [
            'name' => ['type' => self::TYPE_STRING, 'validate' => 'isGenericName', 'required' => true, 'size' => 64],
            'color' => ['type' => self::TYPE_STRING, 'validate' => 'isColor', 'size' => 50],
            'share_customer' => ['type' => self::TYPE_BOOL, 'validate' => 'isBool'],
            'share_order' => ['type' => self::TYPE_BOOL, 'validate' => 'isBool'],
            'share_stock' => ['type' => self::TYPE_BOOL, 'validate' => 'isBool'],
            'active' => ['type' => self::TYPE_BOOL, 'validate' => 'isBool'],
            'deleted' => ['type' => self::TYPE_BOOL, 'validate' => 'isBool'],
        ],
    ];

    /**
     * @see ObjectModel::getFields()
     *
     * @return array
     */
    public function getFields()
    {
        if (!$this->share_customer || !$this->share_stock) {
            $this->share_order = false;
        }

        return parent::getFields();
    }

    public static function getShopGroups($active = true)
    {
        $groups = new PrestaShopCollection('ShopGroup');
        $groups->where('deleted', '=', false);
        if ($active) {
            $groups->where('active', '=', true);
        }

        return $groups;
    }

    /**
     * @return int Total of shop groups
     */
    public static function getTotalShopGroup($active = true)
    {
        return count(ShopGroup::getShopGroups($active));
    }

    public function haveShops()
    {
        return (bool) $this->getTotalShops();
    }

    public function getTotalShops()
    {
        $sql = 'SELECT COUNT(*)
                FROM ' . _DB_PREFIX_ . 'shop s
                WHERE id_shop_group=' . (int) $this->id;

        return (int) Db::getInstance()->getValue($sql);
    }

    public static function getShopsFromGroup($id_group)
    {
        $sql = 'SELECT s.`id_shop`
                FROM ' . _DB_PREFIX_ . 'shop s
                WHERE id_shop_group=' . (int) $id_group;

        return Db::getInstance()->executeS($sql);
    }

    /**
     * Return a group shop ID from group shop name.
     *
     * @param string $name
     *
     * @return int
     */
    public static function getIdByName($name)
    {
        $sql = 'SELECT id_shop_group
                FROM ' . _DB_PREFIX_ . 'shop_group
                WHERE name = \'' . pSQL($name) . '\'';

        return (int) Db::getInstance()->getValue($sql);
    }

    /**
     * Detect dependency with customer or orders.
     *
     * @param int $id_shop_group
     * @param string $check all|customer|order
     *
     * @return bool
     */
    public static function hasDependency($id_shop_group, $check = 'all')
    {
        $list_shops = Shop::getShops(false, $id_shop_group, true);
        if (!$list_shops) {
            return false;
        }

        if ($check == 'all' || $check == 'customer') {
            $total_customer = (int) Db::getInstance()->getValue(
                'SELECT count(*)
                FROM `' . _DB_PREFIX_ . 'customer`
                WHERE `id_shop` IN (' . implode(', ', $list_shops) . ')'
            );
            if ($total_customer) {
                return true;
            }
        }

        if ($check == 'all' || $check == 'order') {
            $total_order = (int) Db::getInstance()->getValue(
                'SELECT count(*)
                FROM `' . _DB_PREFIX_ . 'orders`
                WHERE `id_shop` IN (' . implode(', ', $list_shops) . ')'
            );
            if ($total_order) {
                return true;
            }
        }

        return false;
    }

    public function shopNameExists($name, $id_shop = false)
    {
        return Db::getInstance()->getValue(
            'SELECT id_shop
            FROM ' . _DB_PREFIX_ . 'shop
            WHERE name = "' . pSQL($name) . '"
            AND id_shop_group = ' . (int) $this->id . '
            ' . ($id_shop ? 'AND id_shop != ' . (int) $id_shop : ''),
            false
        );
    }
}
