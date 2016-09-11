<?php
/**
 * 2007-2016 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
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
 * @copyright 2007-2016 PrestaShop SA
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

/**
 * Class GroupCore
 */
class GroupCore extends ObjectModel
{
    /** @var int $id Group ID */
    public $id;

    /** @var string Lastname */
    public $name;

    /** @var string Reduction */
    public $reduction;

    /** @var int Price display method (tax inc/tax exc) */
    public $price_display_method;

    /** @var bool Show prices */
    public $show_prices = 1;

    /** @var string Object creation date */
    public $date_add;

    /** @var string Object last modification date */
    public $date_upd;

    /**
     * @see ObjectModel::$definition
     */
    public static $definition = array(
        'table' => 'group',
        'primary' => 'id_group',
        'multilang' => true,
        'fields' => array(
            'reduction' => array('type' => self::TYPE_FLOAT, 'validate' => 'isFloat'),
            'price_display_method' => array('type' => self::TYPE_INT, 'validate' => 'isPriceDisplayMethod', 'required' => true),
            'show_prices' => array('type' => self::TYPE_BOOL, 'validate' => 'isBool'),
            'date_add' => array('type' => self::TYPE_DATE, 'validate' => 'isDate'),
            'date_upd' => array('type' => self::TYPE_DATE, 'validate' => 'isDate'),

            /* Lang fields */
            'name' => array('type' => self::TYPE_STRING, 'lang' => true, 'validate' => 'isGenericName', 'required' => true, 'size' => 32),
        ),
    );

    protected static $cache_reduction = array();
    protected static $group_price_display_method = array();
    protected static $ps_group_feature_active = null;
    protected static $groups = array();
    protected static $ps_unidentified_group = null;
    protected static $ps_customer_group = null;

    protected $webserviceParameters = array();

    /**
     * GroupCore constructor.
     *
     * @param int|null $id
     * @param int|null $idLang
     * @param int|null $idShop
     */
    public function __construct($id = null, $idLang = null, $idShop = null)
    {
        parent::__construct($id, $idLang, $idShop);
        if ($this->id && !isset(Group::$group_price_display_method[$this->id])) {
            self::$group_price_display_method[$this->id] = $this->price_display_method;
        }
    }

    /**
     * WARNING: For testing only. Do NOT rely on this method, it may be removed at any time.
     */
    public static function clearCachedValues()
    {
        self::$cache_reduction = array();
        self::$group_price_display_method = array();
        self::$ps_group_feature_active = null;
        self::$groups = array();
        self::$ps_unidentified_group = null;
        self::$ps_customer_group = null;
    }

    /**
     * Get Groups
     *
     * @param int  $idLang Language ID
     * @param bool $idShop Shop ID
     *
     * @return array|false|mysqli_result|null|PDOStatement|resource
     */
    public static function getGroups($idLang, $idShop = false)
    {
        $shopCriteria = '';
        if ($idShop) {
            $shopCriteria = Shop::addSqlAssociation('group', 'g');
        }

        return Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('
		SELECT DISTINCT g.`id_group`, g.`reduction`, g.`price_display_method`, gl.`name`
		FROM `'._DB_PREFIX_.'group` g
		LEFT JOIN `'._DB_PREFIX_.'group_lang` AS gl ON (g.`id_group` = gl.`id_group` AND gl.`id_lang` = '.(int)$idLang.')
		'.$shopCriteria.'
		ORDER BY g.`id_group` ASC');
    }

    /**
     * Get Customers
     *
     * @param bool $count
     * @param int  $start
     * @param int  $limit
     * @param bool $shopFiltering
     *
     * @return array|false|mysqli_result|null|PDOStatement|resource|string
     */
    public function getCustomers($count = false, $start = 0, $limit = 0, $shopFiltering = false)
    {
        if ($count) {
            return Db::getInstance()->getValue('
			SELECT COUNT(*)
			FROM `'._DB_PREFIX_.'customer_group` cg
			LEFT JOIN `'._DB_PREFIX_.'customer` c ON (cg.`id_customer` = c.`id_customer`)
			WHERE cg.`id_group` = '.(int) $this->id.'
			'.($shopFiltering ? Shop::addSqlRestriction(Shop::SHARE_CUSTOMER) : '').'
			AND c.`deleted` != 1');
        }

        return Db::getInstance()->executeS('
		SELECT cg.`id_customer`, c.*
		FROM `'._DB_PREFIX_.'customer_group` cg
		LEFT JOIN `'._DB_PREFIX_.'customer` c ON (cg.`id_customer` = c.`id_customer`)
		WHERE cg.`id_group` = '.(int) $this->id.'
		AND c.`deleted` != 1
		'.($shopFiltering ? Shop::addSqlRestriction(Shop::SHARE_CUSTOMER) : '').'
		ORDER BY cg.`id_customer` ASC
		'.($limit > 0 ? 'LIMIT '.(int) $start.', '.(int) $limit : ''));
    }

    /**
     * Get Group reduction for Customer
     *
     * @param int|null $idCustomer Customer ID
     *
     * @return mixed
     */
    public static function getReduction($idCustomer = null)
    {
        if (!isset(self::$cache_reduction['customer'][(int) $idCustomer])) {
            $idGroup = $idCustomer ? Customer::getDefaultGroupId((int) $idCustomer) : (int) Group::getCurrent()->id;
            self::$cache_reduction['customer'][(int) $idCustomer] = Group::getReductionByIdGroup($idGroup);
        }
        return self::$cache_reduction['customer'][(int) $idCustomer];
    }

    /**
     * Get reduction for Group
     *
     * @param int $idGroup Group ID
     *
     * @return mixed
     */
    public static function getReductionByIdGroup($idGroup)
    {
        if (!isset(self::$cache_reduction['group'][$idGroup])) {
            self::$cache_reduction['group'][$idGroup] = Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue('
			SELECT `reduction`
			FROM `'._DB_PREFIX_.'group`
			WHERE `id_group` = '.(int) $idGroup);
        }

        return self::$cache_reduction['group'][$idGroup];
    }

    /**
     * Get price display method for Group
     * (tax included/excluded)
     *
     * @param int $idGroup Group ID
     *
     * @return mixed
     */
    public static function getPriceDisplayMethod($idGroup)
    {
        if (!isset(Group::$group_price_display_method[$idGroup])) {
            self::$group_price_display_method[$idGroup] = Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue('
			SELECT `price_display_method`
			FROM `'._DB_PREFIX_.'group`
			WHERE `id_group` = '.(int) $idGroup);
        }

        return self::$group_price_display_method[$idGroup];
    }

    /**
     * Get Default price display method
     *
     * @return mixed
     */
    public static function getDefaultPriceDisplayMethod()
    {
        return Group::getPriceDisplayMethod((int) Configuration::get('PS_CUSTOMER_GROUP'));
    }

    public function add($autoDate = true, $nullValues = false)
    {
        Configuration::updateGlobalValue('PS_GROUP_FEATURE_ACTIVE', '1');
        if (parent::add($autoDate, $nullValues)) {
            Category::setNewGroupForHome((int) $this->id);
            Carrier::assignGroupToAllCarriers((int) $this->id);

            return true;
        }

        return false;
    }

    /**
     * Updates the current Group in the database
     *
     * @param bool $nullValues Whether we want to use NULL values instead of empty quotes values
     *
     * @return bool Indicates whether the Group has been successfully updated
     * @throws PrestaShopDatabaseException
     * @throws PrestaShopException
     */
    public function update($nullValues = false)
    {
        if (!Configuration::getGlobalValue('PS_GROUP_FEATURE_ACTIVE') && $this->reduction > 0) {
            Configuration::updateGlobalValue('PS_GROUP_FEATURE_ACTIVE', 1);
        }

        return parent::update($nullValues);
    }

    /**
     * Deletes current Group from the database
     *
     * @return bool True if delete was successful
     * @throws PrestaShopException
     */
    public function delete()
    {
        if ($this->id == (int) Configuration::get('PS_CUSTOMER_GROUP')) {
            return false;
        }
        if (parent::delete()) {
            Db::getInstance()->execute('DELETE FROM `'._DB_PREFIX_.'cart_rule_group` WHERE `id_group` = '.(int) $this->id);
            Db::getInstance()->execute('DELETE FROM `'._DB_PREFIX_.'customer_group` WHERE `id_group` = '.(int) $this->id);
            Db::getInstance()->execute('DELETE FROM `'._DB_PREFIX_.'category_group` WHERE `id_group` = '.(int) $this->id);
            Db::getInstance()->execute('DELETE FROM `'._DB_PREFIX_.'group_reduction` WHERE `id_group` = '.(int) $this->id);
            Db::getInstance()->execute('DELETE FROM `'._DB_PREFIX_.'product_group_reduction_cache` WHERE `id_group` = '.(int) $this->id);
            $this->truncateModulesRestrictions($this->id);

            // Add default group (id 3) to customers without groups
            Db::getInstance()->execute('INSERT INTO `'._DB_PREFIX_.'customer_group` (
				SELECT c.id_customer, '.(int) Configuration::get('PS_CUSTOMER_GROUP').' FROM `'._DB_PREFIX_.'customer` c
				LEFT JOIN `'._DB_PREFIX_.'customer_group` cg
				ON cg.id_customer = c.id_customer
				WHERE cg.id_customer IS NULL)');

            // Set to the customer the default group
            // Select the minimal id from customer_group
            Db::getInstance()->execute('UPDATE `'._DB_PREFIX_.'customer` cg
				SET id_default_group =
					IFNULL((
						SELECT min(id_group) FROM `'._DB_PREFIX_.'customer_group`
						WHERE id_customer = cg.id_customer),
						'.(int) Configuration::get('PS_CUSTOMER_GROUP').')
				WHERE `id_default_group` = '.(int) $this->id);

            // Remove group restrictions
            $res = Db::getInstance()->delete('module_group', 'id_group = '.(int) $this->id);

            return $res;
        }

        return false;
    }

    /**
     * This method is allow to know if a feature is used or active
     *
     * @return bool
     *
     * @since 1.5.0.1
     */
    public static function isFeatureActive()
    {
        if (self::$ps_group_feature_active === null) {
            self::$ps_group_feature_active = Configuration::get('PS_GROUP_FEATURE_ACTIVE');
        }
        return self::$ps_group_feature_active;
    }

    /**
     * This method is allow to know if there are other groups than the default ones
     * 
     * @param $table
     * @param $hasActiveColumn
     *
     * @return bool
     * 
     * @since 1.5.0.1
     */
    public static function isCurrentlyUsed($table = null, $hasActiveColumn = false)
    {
        return (bool) (Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue('SELECT COUNT(*) FROM `'._DB_PREFIX_.'group`') > 3);
    }

    /**
     * Truncate all modules restrictions for the group
     *
     * @param int $idGroup
     *
     * @return bool
     */
    public static function truncateModulesRestrictions($idGroup)
    {
        return Db::getInstance()->execute('
		DELETE FROM `'._DB_PREFIX_.'module_group`
		WHERE `id_group` = '.(int) $idGroup);
    }

    /**
     * Truncate all restrictions by module
     *
     * @param int $idModule
     *
     * @return bool
     */
    public static function truncateRestrictionsByModule($idModule)
    {
        return Db::getInstance()->execute('
		DELETE FROM `'._DB_PREFIX_.'module_group`
		WHERE `id_module` = '.(int) $idModule);
    }

    /**
     * Adding restrictions modules to the group with id $id_group
     *
     * @param int   $idGroup
     * @param array $modules
     * @param array $shops
     *
     * @return bool
     */
    public static function addModulesRestrictions($idGroup, $modules, $shops = array(1))
    {
        if (!is_array($modules) || !count($modules) || !is_array($shops) || !count($shops)) {
            return false;
        }

        // Delete all record for this group
        Db::getInstance()->execute('DELETE FROM `'._DB_PREFIX_.'module_group` WHERE `id_group` = '.(int) $idGroup);

        $sql = 'INSERT INTO `'._DB_PREFIX_.'module_group` (`id_module`, `id_shop`, `id_group`) VALUES ';
        foreach ($modules as $module) {
            foreach ($shops as $shop) {
                $sql .= '("'.(int) $module.'", "'.(int) $shop.'", "'.(int) $idGroup.'"),';
            }
        }
        $sql = rtrim($sql, ',');

        return (bool) Db::getInstance()->execute($sql);
    }

    /**
     * Add restrictions for a new module.
     * We authorize every groups to the new module
     *
     * @param int   $idModule
     * @param array $shops
     *
     * @return bool
     */
    public static function addRestrictionsForModule($idModule, $shops = array(1))
    {
        if (!is_array($shops) || !count($shops)) {
            return false;
        }

        $res = true;
        foreach ($shops as $shop) {
            $res &= Db::getInstance()->execute('
			INSERT INTO `'._DB_PREFIX_.'module_group` (`id_module`, `id_shop`, `id_group`)
			(SELECT '.(int) $idModule.', '.(int) $shop.', id_group FROM `'._DB_PREFIX_.'group`)');
        }
        return $res;
    }

    /**
     * Return current group object
     * Use context
     *
     * @return Group Group object
     */
    public static function getCurrent()
    {
        if (self::$ps_unidentified_group === null) {
            self::$ps_unidentified_group = Configuration::get('PS_UNIDENTIFIED_GROUP');
        }

        if (self::$ps_customer_group === null) {
            self::$ps_customer_group = Configuration::get('PS_CUSTOMER_GROUP');
        }

        $customer = Context::getContext()->customer;
        if (Validate::isLoadedObject($customer)) {
            $idGroup = (int) $customer->id_default_group;
        } else {
            $idGroup = (int) self::$ps_unidentified_group;
        }

        if (!isset(self::$groups[$idGroup])) {
            self::$groups[$idGroup] = new Group($idGroup);
        }

        if (!self::$groups[$idGroup]->isAssociatedToShop(Context::getContext()->shop->id)) {
            $idGroup = (int) self::$ps_customer_group;
            if (!isset(self::$groups[$idGroup])) {
                self::$groups[$idGroup] = new Group($idGroup);
            }
        }

        return self::$groups[$idGroup];
    }

    /**
     * Light back office search for Group
     *
     * @param string $query Searched string
     * @return array Corresponding groups
     */
    public static function searchByName($query)
    {
        return Db::getInstance()->getRow('
			SELECT g.*, gl.*
			FROM `'._DB_PREFIX_.'group` g
			LEFT JOIN `'._DB_PREFIX_.'group_lang` gl
				ON (g.`id_group` = gl.`id_group`)
			WHERE `name` = \''.pSQL($query).'\'
		');
    }
}
