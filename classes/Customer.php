<?php
/**
 * 2007-2017 PrestaShop
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
 * @copyright 2007-2017 PrestaShop SA
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */
use PrestaShop\PrestaShop\Adapter\ServiceLocator;
use PrestaShop\PrestaShop\Adapter\CoreException;

/***
 * Class CustomerCore
 */
class CustomerCore extends ObjectModel
{
    /** @var int $id Customer ID */
    public $id;

    /** @var int $id_shop Shop ID */
    public $id_shop;

    /** @var int $id_shop_group ShopGroup ID */
    public $id_shop_group;

    /** @var string Secure key */
    public $secure_key;

    /** @var string protected note */
    public $note;

    /** @var int Gender ID */
    public $id_gender = 0;

    /** @var int Default group ID */
    public $id_default_group;

    /** @var int Current language used by the customer */
    public $id_lang;

    /** @var string Lastname */
    public $lastname;

    /** @var string Firstname */
    public $firstname;

    /** @var string Birthday (yyyy-mm-dd) */
    public $birthday = null;

    /** @var string e-mail */
    public $email;

    /** @var bool Newsletter subscription */
    public $newsletter;

    /** @var string Newsletter ip registration */
    public $ip_registration_newsletter;

    /** @var string Newsletter ip registration */
    public $newsletter_date_add;

    /** @var bool Opt-in subscription */
    public $optin;

    /** @var string WebSite * */
    public $website;

    /** @var string Company */
    public $company;

    /** @var string SIRET */
    public $siret;

    /** @var string APE */
    public $ape;

    /** @var float Outstanding allow amount (B2B opt) */
    public $outstanding_allow_amount = 0;

    /** @var int Show public prices (B2B opt) */
    public $show_public_prices = 0;

    /** @var int Risk ID (B2B opt) */
    public $id_risk;

    /** @var int Max payment day */
    public $max_payment_days = 0;

    /** @var int Password */
    public $passwd;

    /** @var string Datetime Password */
    public $last_passwd_gen;

    /** @var bool Status */
    public $active = true;

    /** @var bool Status */
    public $is_guest = 0;

    /** @var bool True if carrier has been deleted (staying in database as deleted) */
    public $deleted = 0;

    /** @var string Object creation date */
    public $date_add;

    /** @var string Object last modification date */
    public $date_upd;

    public $years;
    public $days;
    public $months;

    /** @var int customer id_country as determined by geolocation */
    public $geoloc_id_country;
    /** @var int customer id_state as determined by geolocation */
    public $geoloc_id_state;
    /** @var string customer postcode as determined by geolocation */
    public $geoloc_postcode;

    /** @var bool is the customer logged in */
    public $logged = 0;

    /** @var int id_guest meaning the guest table, not the guest customer */
    public $id_guest;

    public $groupBox;

    /** @var string Unique token for forgot passsword feature */
    public $reset_password_token;

    /** @var string token validity date for forgot password feature */
    public $reset_password_validity;

    protected $webserviceParameters = array(
        'fields' => array(
            'id_default_group' => array('xlink_resource' => 'groups'),
            'id_lang' => array('xlink_resource' => 'languages'),
            'newsletter_date_add' => array(),
            'ip_registration_newsletter' => array(),
            'last_passwd_gen' => array('setter' => null),
            'secure_key' => array('setter' => null),
            'deleted' => array(),
            'passwd' => array('setter' => 'setWsPasswd'),
        ),
        'associations' => array(
            'groups' => array('resource' => 'group'),
        ),
    );

    /**
     * @see ObjectModel::$definition
     */
    public static $definition = array(
        'table' => 'customer',
        'primary' => 'id_customer',
        'fields' => array(
            'secure_key' => array('type' => self::TYPE_STRING, 'validate' => 'isMd5', 'copy_post' => false),
            'lastname' => array('type' => self::TYPE_STRING, 'validate' => 'isName', 'required' => true, 'size' => 255),
            'firstname' => array('type' => self::TYPE_STRING, 'validate' => 'isName', 'required' => true, 'size' => 255),
            'email' => array('type' => self::TYPE_STRING, 'validate' => 'isEmail', 'required' => true, 'size' => 128),
            'passwd' => array('type' => self::TYPE_STRING, 'validate' => 'isPasswd', 'required' => true, 'size' => 60),
            'last_passwd_gen' => array('type' => self::TYPE_STRING, 'copy_post' => false),
            'id_gender' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),
            'birthday' => array('type' => self::TYPE_DATE, 'validate' => 'isBirthDate'),
            'newsletter' => array('type' => self::TYPE_BOOL, 'validate' => 'isBool'),
            'newsletter_date_add' => array('type' => self::TYPE_DATE, 'copy_post' => false),
            'ip_registration_newsletter' => array('type' => self::TYPE_STRING, 'copy_post' => false),
            'optin' => array('type' => self::TYPE_BOOL, 'validate' => 'isBool'),
            'website' => array('type' => self::TYPE_STRING, 'validate' => 'isUrl'),
            'company' => array('type' => self::TYPE_STRING, 'validate' => 'isGenericName'),
            'siret' => array('type' => self::TYPE_STRING, 'validate' => 'isSiret'),
            'ape' => array('type' => self::TYPE_STRING, 'validate' => 'isApe'),
            'outstanding_allow_amount' => array('type' => self::TYPE_FLOAT, 'validate' => 'isFloat', 'copy_post' => false),
            'show_public_prices' => array('type' => self::TYPE_BOOL, 'validate' => 'isBool', 'copy_post' => false),
            'id_risk' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedInt', 'copy_post' => false),
            'max_payment_days' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedInt', 'copy_post' => false),
            'active' => array('type' => self::TYPE_BOOL, 'validate' => 'isBool', 'copy_post' => false),
            'deleted' => array('type' => self::TYPE_BOOL, 'validate' => 'isBool', 'copy_post' => false),
            'note' => array('type' => self::TYPE_HTML, 'validate' => 'isCleanHtml', 'size' => 65000, 'copy_post' => false),
            'is_guest' => array('type' => self::TYPE_BOOL, 'validate' => 'isBool', 'copy_post' => false),
            'id_shop' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'copy_post' => false),
            'id_shop_group' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'copy_post' => false),
            'id_default_group' => array('type' => self::TYPE_INT, 'copy_post' => false),
            'id_lang' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'copy_post' => false),
            'date_add' => array('type' => self::TYPE_DATE, 'validate' => 'isDate', 'copy_post' => false),
            'date_upd' => array('type' => self::TYPE_DATE, 'validate' => 'isDate', 'copy_post' => false),
            'reset_password_token' => array('type' => self::TYPE_STRING, 'validate' => 'isSha1', 'size' => 40, 'copy_post' => false),
            'reset_password_validity' => array('type' => self::TYPE_DATE, 'validate' => 'isDateOrNull', 'copy_post' => false),
        ),
    );

    protected static $_defaultGroupId = array();
    protected static $_customerHasAddress = array();
    protected static $_customer_groups = array();

    /**
     * CustomerCore constructor.
     *
     * @param null $id
     */
    public function __construct($id = null)
    {
        $this->id_default_group = (int) Configuration::get('PS_CUSTOMER_GROUP');
        parent::__construct($id);
    }

    /**
     * Adds current Customer as a new Object to the database.
     *
     * @param bool $autoDate   Automatically set `date_upd` and `date_add` columns
     * @param bool $nullValues Whether we want to use NULL values instead of empty quotes values
     *
     * @return bool Indicates whether the Customer has been successfully added
     *
     * @throws PrestaShopDatabaseException
     * @throws PrestaShopException
     */
    public function add($autoDate = true, $nullValues = true)
    {
        $this->id_shop = ($this->id_shop) ? $this->id_shop : Context::getContext()->shop->id;
        $this->id_shop_group = ($this->id_shop_group) ? $this->id_shop_group : Context::getContext()->shop->id_shop_group;
        $this->id_lang = ($this->id_lang) ? $this->id_lang : Context::getContext()->language->id;
        $this->birthday = (empty($this->years) ? $this->birthday : (int) $this->years.'-'.(int) $this->months.'-'.(int) $this->days);
        $this->secure_key = md5(uniqid(rand(), true));
        $this->last_passwd_gen = date('Y-m-d H:i:s', strtotime('-'.Configuration::get('PS_PASSWD_TIME_FRONT').'minutes'));

        if ($this->newsletter && !Validate::isDate($this->newsletter_date_add)) {
            $this->newsletter_date_add = date('Y-m-d H:i:s');
        }

        if ($this->id_default_group == Configuration::get('PS_CUSTOMER_GROUP')) {
            if ($this->is_guest) {
                $this->id_default_group = (int) Configuration::get('PS_GUEST_GROUP');
            } else {
                $this->id_default_group = (int) Configuration::get('PS_CUSTOMER_GROUP');
            }
        }

        /* Can't create a guest customer, if this feature is disabled */
        if ($this->is_guest && !Configuration::get('PS_GUEST_CHECKOUT_ENABLED')) {
            return false;
        }
        $success = parent::add($autoDate, $nullValues);
        $this->updateGroup($this->groupBox);

        return $success;
    }

    /**
     * Updates the current Customer in the database.
     *
     * @param bool $nullValues Whether we want to use NULL values instead of empty quotes values
     *
     * @return bool Indicates whether the Customer has been successfully updated
     *
     * @throws PrestaShopDatabaseException
     * @throws PrestaShopException
     */
    public function update($nullValues = false)
    {
        $this->birthday = (empty($this->years) ? $this->birthday : (int) $this->years.'-'.(int) $this->months.'-'.(int) $this->days);

        if ($this->newsletter && !Validate::isDate($this->newsletter_date_add)) {
            $this->newsletter_date_add = date('Y-m-d H:i:s');
        }
        if (isset(Context::getContext()->controller) && Context::getContext()->controller->controller_type == 'admin') {
            $this->updateGroup($this->groupBox);
        }

        if ($this->deleted) {
            $addresses = $this->getAddresses((int) Configuration::get('PS_LANG_DEFAULT'));
            foreach ($addresses as $address) {
                $obj = new Address((int) $address['id_address']);
                $obj->delete();
            }
        }

        try {
            return parent::update(true);
        } catch (\PrestaShopException $exception) {
            $message = $exception->getMessage();
            error_log($message);

            return false;
        }
    }

    /**
     * Deletes current Customer from the database.
     *
     * @return bool True if delete was successful
     *
     * @throws PrestaShopException
     */
    public function delete()
    {
        if (!count(Order::getCustomerOrders((int) $this->id))) {
            $addresses = $this->getAddresses((int) Configuration::get('PS_LANG_DEFAULT'));
            foreach ($addresses as $address) {
                $obj = new Address((int) $address['id_address']);
                $obj->delete();
            }
        }
        Db::getInstance()->execute('DELETE FROM `'._DB_PREFIX_.'customer_group` WHERE `id_customer` = '.(int) $this->id);
        Db::getInstance()->execute('DELETE FROM '._DB_PREFIX_.'message WHERE id_customer='.(int) $this->id);
        Db::getInstance()->execute('DELETE FROM '._DB_PREFIX_.'specific_price WHERE id_customer='.(int) $this->id);

        $carts = Db::getInstance()->executes('SELECT id_cart FROM '._DB_PREFIX_.'cart WHERE id_customer='.(int) $this->id);
        if ($carts) {
            foreach ($carts as $cart) {
                Db::getInstance()->execute('DELETE FROM '._DB_PREFIX_.'cart WHERE id_cart='.(int) $cart['id_cart']);
                Db::getInstance()->execute('DELETE FROM '._DB_PREFIX_.'cart_product WHERE id_cart='.(int) $cart['id_cart']);
            }
        }

        $cts = Db::getInstance()->executes('SELECT id_customer_thread FROM '._DB_PREFIX_.'customer_thread WHERE id_customer='.(int) $this->id);
        if ($cts) {
            foreach ($cts as $ct) {
                Db::getInstance()->execute('DELETE FROM '._DB_PREFIX_.'customer_thread WHERE id_customer_thread='.(int) $ct['id_customer_thread']);
                Db::getInstance()->execute('DELETE FROM '._DB_PREFIX_.'customer_message WHERE id_customer_thread='.(int) $ct['id_customer_thread']);
            }
        }

        CartRule::deleteByIdCustomer((int) $this->id);

        return parent::delete();
    }

    /**
     * Return customers list.
     *
     * @param null|bool $onlyActive Returns only active customers when `true`
     *
     * @return array Customers
     */
    public static function getCustomers($onlyActive = null)
    {
        return Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('
            SELECT `id_customer`, `email`, `firstname`, `lastname`
            FROM `'._DB_PREFIX_.'customer`
            WHERE 1 '.Shop::addSqlRestriction(Shop::SHARE_CUSTOMER).
            ($onlyActive ? ' AND `active` = 1' : '').'
            ORDER BY `id_customer` ASC'
        );
    }

    /**
     * Return customer instance from its e-mail (optionally check password).
     *
     * @param string $email             e-mail
     * @param string $plaintextPassword Password is also checked if specified
     * @param bool   $ignoreGuest
     *
     * @return bool|Customer|CustomerCore Customer instance
     */
    public function getByEmail($email, $plaintextPassword = null, $ignoreGuest = true)
    {
        if (!Validate::isEmail($email) || ($plaintextPassword && !Validate::isPasswd($plaintextPassword))) {
            die(Tools::displayError());
        }

        $shopGroup = Shop::getGroupFromShop(Shop::getContextShopID(), false);

        $sql = new DbQuery();
        $sql->select('c.`passwd`');
        $sql->from('customer', 'c');
        $sql->where('c.`email` = \''.pSQL($email).'\'');
        if (Shop::getContext() == Shop::CONTEXT_SHOP && $shopGroup['share_customer']) {
            $sql->where('c.`id_shop_group` = '.(int) Shop::getContextShopGroupID());
        } else {
            $sql->where('c.`id_shop` IN ('.implode(', ', Shop::getContextListShopID(Shop::SHARE_CUSTOMER)).')');
        }

        if ($ignoreGuest) {
            $sql->where('c.`is_guest` = 0');
        }
        $sql->where('c.`deleted` = 0');

        $passwordHash = Db::getInstance()->getValue($sql);
        try {
            /** @var \PrestaShop\PrestaShop\Core\Crypto\Hashing $crypto */
            $crypto = ServiceLocator::get('\\PrestaShop\\PrestaShop\\Core\\Crypto\\Hashing');
        } catch (CoreException $e) {
            return false;
        }

        $shouldCheckPassword = !is_null($plaintextPassword);
        if ($shouldCheckPassword && !$crypto->checkHash($plaintextPassword, $passwordHash)) {
            return false;
        }

        $sql = new DbQuery();
        $sql->select('c.*');
        $sql->from('customer', 'c');
        $sql->where('c.`email` = \''.pSQL($email).'\'');
        if (Shop::getContext() == Shop::CONTEXT_SHOP && $shopGroup['share_customer']) {
            $sql->where('c.`id_shop_group` = '.(int) Shop::getContextShopGroupID());
        } else {
            $sql->where('c.`id_shop` IN ('.implode(', ', Shop::getContextListShopID(Shop::SHARE_CUSTOMER)).')');
        }
        if ($ignoreGuest) {
            $sql->where('c.`is_guest` = 0');
        }
        $sql->where('c.`deleted` = 0');

        $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow($sql);

        if (!$result) {
            return false;
        }

        $this->id = $result['id_customer'];
        foreach ($result as $key => $value) {
            if (property_exists($this, $key)) {
                $this->{$key} = $value;
            }
        }

        if ($shouldCheckPassword && !$crypto->isFirstHash($plaintextPassword, $passwordHash)) {
            $this->passwd = $crypto->hash($plaintextPassword);
            $this->update();
        }

        return $this;
    }

    /**
     * Retrieve customers by email address.
     *
     * @param string $email
     *
     * @return array
     */
    public static function getCustomersByEmail($email)
    {
        $sql = 'SELECT *
                FROM `'._DB_PREFIX_.'customer`
                WHERE `email` = \''.pSQL($email).'\'
                    '.Shop::addSqlRestriction(Shop::SHARE_CUSTOMER);

        return Db::getInstance()->executeS($sql);
    }

    /**
     * Check id the customer is active or not.
     *
     * @param int $idCustomer
     *
     * @return bool Customer validity
     */
    public static function isBanned($idCustomer)
    {
        if (!Validate::isUnsignedId($idCustomer)) {
            return true;
        }
        $cacheId = 'Customer::isBanned_'.(int) $idCustomer;
        if (!Cache::isStored($cacheId)) {
            $result = (bool) !Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow('
            SELECT `id_customer`
            FROM `'._DB_PREFIX_.'customer`
            WHERE `id_customer` = \''.(int) $idCustomer.'\'
            AND active = 1
            AND `deleted` = 0');
            Cache::store($cacheId, $result);

            return $result;
        }

        return Cache::retrieve($cacheId);
    }

    /**
     * Check if e-mail is already registered in database.
     *
     * @param string $email       e-mail
     * @param bool   $returnId
     * @param bool   $ignoreGuest To exclude guest customer
     *
     * @return bool|int Customer ID if found
     *                  `false` otherwise
     */
    public static function customerExists($email, $returnId = false, $ignoreGuest = true)
    {
        if (!Validate::isEmail($email)) {
            if (defined('_PS_MODE_DEV_') && _PS_MODE_DEV_) {
                die(Context::getContext()->getTranslator()->trans('Invalid email', array(), 'Admin.Orderscustomers.Notification'));
            }

            return false;
        }

        $result = Db::getInstance()->getValue('
        SELECT `id_customer`
        FROM `'._DB_PREFIX_.'customer`
        WHERE `email` = \''.pSQL($email).'\'
        '.Shop::addSqlRestriction(Shop::SHARE_CUSTOMER).'
        '.($ignoreGuest ? ' AND `is_guest` = 0' : ''));

        return $returnId ? (int) $result : (bool) $result;
    }

    /**
     * Check if an address is owned by a customer.
     *
     * @param int $idCustomer Customer ID
     * @param int $idAddress  Address ID
     *
     * @return bool result
     */
    public static function customerHasAddress($idCustomer, $idAddress)
    {
        $key = (int) $idCustomer.'-'.(int) $idAddress;
        if (!array_key_exists($key, self::$_customerHasAddress)) {
            self::$_customerHasAddress[$key] = (bool) Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue('
            SELECT `id_address`
            FROM `'._DB_PREFIX_.'address`
            WHERE `id_customer` = '.(int) $idCustomer.'
            AND `id_address` = '.(int) $idAddress.'
            AND `deleted` = 0');
        }

        return self::$_customerHasAddress[$key];
    }

    /**
     * Reset Address cache.
     *
     * @param int $idCustomer Customer ID
     * @param int $idAddress  Address ID
     */
    public static function resetAddressCache($idCustomer, $idAddress)
    {
        $key = (int) $idCustomer.'-'.(int) $idAddress;
        if (array_key_exists($key, self::$_customerHasAddress)) {
            unset(self::$_customerHasAddress[$key]);
        }
    }

    /**
     * Return customer addresses.
     *
     * @param int $idLang Language ID
     *
     * @return array Addresses
     */
    public function getAddresses($idLang)
    {
        $shareOrder = (bool) Context::getContext()->shop->getGroup()->share_order;
        $cacheId = 'Customer::getAddresses'.(int) $this->id.'-'.(int) $idLang.'-'.$shareOrder;
        if (!Cache::isStored($cacheId)) {
            $sql = 'SELECT DISTINCT a.*, cl.`name` AS country, s.name AS state, s.iso_code AS state_iso
                    FROM `'._DB_PREFIX_.'address` a
                    LEFT JOIN `'._DB_PREFIX_.'country` c ON (a.`id_country` = c.`id_country`)
                    LEFT JOIN `'._DB_PREFIX_.'country_lang` cl ON (c.`id_country` = cl.`id_country`)
                    LEFT JOIN `'._DB_PREFIX_.'state` s ON (s.`id_state` = a.`id_state`)
                    '.($shareOrder ? '' : Shop::addSqlAssociation('country', 'c')).'
                    WHERE `id_lang` = '.(int) $idLang.' AND `id_customer` = '.(int) $this->id.' AND a.`deleted` = 0';

            $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);
            Cache::store($cacheId, $result);

            return $result;
        }

        return Cache::retrieve($cacheId);
    }

    /**
     * Get simplified Addresses arrays.
     *
     * @param int|null $idLang Language ID
     *
     * @return array
     */
    public function getSimpleAddresses($idLang = null)
    {
        if (!$this->id) {
            return array();
        }

        if (is_null($idLang)) {
            $idLang = Context::getContext()->language->id;
        }

        $sql = $this->getSimpleAddressSql(null, $idLang);
        $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);
        $addresses = array();
        foreach ($result as $addr) {
            $addresses[$addr['id']] = $addr;
        }

        return $addresses;
    }

    /**
     * Get Address as array.
     *
     * @param int      $idAddress Address ID
     * @param int|null $idLang    Language ID
     *
     * @return array|false|mysqli_result|null|PDOStatement|resource
     */
    public function getSimpleAddress($idAddress, $idLang = null)
    {
        if (!$this->id || !intval($idAddress) || !$idAddress) {
            return array(
                'id' => '',
                'alias' => '',
                'firstname' => '',
                'lastname' => '',
                'company' => '',
                'address1' => '',
                'address2' => '',
                'postcode' => '',
                'city' => '',
                'id_state' => '',
                'state' => '',
                'state_iso' => '',
                'id_country' => '',
                'country' => '',
                'country_iso' => '',
                'other' => '',
                'phone' => '',
                'phone_mobile' => '',
                'vat_number' => '',
                'dni' => '',
            );
        }

        $sql = $this->getSimpleAddressSql($idAddress, $idLang);
        $res = Db::getInstance()->executeS($sql);
        if (count($res) === 1) {
            return $res[0];
        } else {
            return $res;
        }
    }

    /**
     * Get SQL query to retrieve Address in an array.
     *
     * @param int|null $idAddress Address ID
     * @param int|null $idLang    Language ID
     *
     * @return string
     */
    public function getSimpleAddressSql($idAddress = null, $idLang = null)
    {
        if (is_null($idLang)) {
            $idLang = Context::getContext()->language->id;
        }
        $shareOrder = (bool) Context::getContext()->shop->getGroup()->share_order;

        $sql = 'SELECT DISTINCT
                      a.`id_address` AS `id`,
                      a.`alias`,
                      a.`firstname`,
                      a.`lastname`,
                      a.`company`,
                      a.`address1`,
                      a.`address2`,
                      a.`postcode`,
                      a.`city`,
                      a.`id_state`,
                      s.name AS state,
                      s.`iso_code` AS state_iso,
                      a.`id_country`,
                      cl.`name` AS country,
                      co.`iso_code` AS country_iso,
                      a.`other`,
                      a.`phone`,
                      a.`phone_mobile`,
                      a.`vat_number`,
                      a.`dni`
                    FROM `'._DB_PREFIX_.'address` a
                    LEFT JOIN `'._DB_PREFIX_.'country` co ON (a.`id_country` = co.`id_country`)
                    LEFT JOIN `'._DB_PREFIX_.'country_lang` cl ON (co.`id_country` = cl.`id_country`)
                    LEFT JOIN `'._DB_PREFIX_.'state` s ON (s.`id_state` = a.`id_state`)
                    '.($shareOrder ? '' : Shop::addSqlAssociation('country', 'co')).'
                    WHERE
                        `id_lang` = '.(int) $idLang.'
                        AND `id_customer` = '.(int) $this->id.'
                        AND a.`deleted` = 0
                        AND a.`active` = 1';

        if (!is_null($idAddress)) {
            $sql .= ' AND a.`id_address` = '.(int) $idAddress;
        }

        return $sql;
    }

    /**
     * Count the number of addresses for a customer.
     *
     * @param int $idCustomer Customer ID
     *
     * @return int Number of addresses
     */
    public static function getAddressesTotalById($idCustomer)
    {
        return Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue('
            SELECT COUNT(`id_address`)
            FROM `'._DB_PREFIX_.'address`
            WHERE `id_customer` = '.(int) $idCustomer.'
            AND `deleted` = 0'
        );
    }

    /**
     * Check if customer password is the right one.
     *
     * @param int    $idCustomer   Customer ID
     * @param string $passwordHash Hashed password
     *
     * @return bool result
     */
    public static function checkPassword($idCustomer, $passwordHash)
    {
        if (!Validate::isUnsignedId($idCustomer)) {
            die(Tools::displayError());
        }
        $cacheId = 'Customer::checkPassword'.(int) $idCustomer.'-'.$passwordHash;
        if (!Cache::isStored($cacheId)) {
            $sql = new DbQuery();
            $sql->select('c.`id_customer`');
            $sql->from('customer', 'c');
            $sql->where('c.`id_customer` = '.(int) $idCustomer);
            $sql->where('c.`passwd` = \''.pSQL($passwordHash).'\'');

            $result = (bool) Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue($sql);

            Cache::store($cacheId, $result);

            return $result;
        }

        return Cache::retrieve($cacheId);
    }

    /**
     * Light back office search for customers.
     *
     * @param string   $query Searched string
     * @param null|int $limit Limit query results
     *
     * @return array|false|mysqli_result|null|PDOStatement|resource Corresponding customers
     *
     * @throws PrestaShopDatabaseException
     */
    public static function searchByName($query, $limit = null)
    {
        $sql = 'SELECT *
                FROM `'._DB_PREFIX_.'customer`
                WHERE 1';
        $search_items = explode(' ', $query);
        $research_fields = array('id_customer', 'firstname', 'lastname', 'email');
        if (Configuration::get('PS_B2B_ENABLE')) {
            $research_fields[] = 'company';
        }

        $items = array();
        foreach ($research_fields as $field) {
            foreach ($search_items as $item) {
                $items[$item][] = $field.' LIKE \'%'.pSQL($item).'%\' ';
            }
        }

        foreach ($items as $likes) {
            $sql .= ' AND ('.implode(' OR ', $likes).') ';
        }

        $sql .= Shop::addSqlRestriction(Shop::SHARE_CUSTOMER);

        if ($limit) {
            $sql .= ' LIMIT 0, '.(int) $limit;
        }

        return Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);
    }

    /**
     * Search for customers by ip address.
     *
     * @param string $ip Searched string
     *
     * @return array|false|mysqli_result|null|PDOStatement|resource
     */
    public static function searchByIp($ip)
    {
        return Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('
        SELECT DISTINCT c.*
        FROM `'._DB_PREFIX_.'customer` c
        LEFT JOIN `'._DB_PREFIX_.'guest` g ON g.id_customer = c.id_customer
        LEFT JOIN `'._DB_PREFIX_.'connections` co ON g.id_guest = co.id_guest
        WHERE co.`ip_address` = \''.(int) ip2long(trim($ip)).'\'');
    }

    /**
     * Return several useful statistics about customer.
     *
     * @return array Stats
     */
    public function getStats()
    {
        $result = Db::getInstance()->getRow('
        SELECT COUNT(`id_order`) AS nb_orders, SUM(`total_paid` / o.`conversion_rate`) AS total_orders
        FROM `'._DB_PREFIX_.'orders` o
        WHERE o.`id_customer` = '.(int) $this->id.'
        AND o.valid = 1');

        $result2 = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow('
		SELECT c.`date_add` AS last_visit
		FROM `'._DB_PREFIX_.'connections` c
      	LEFT JOIN `'._DB_PREFIX_.'guest` g USING (id_guest)
		WHERE g.`id_customer` = '.(int) $this->id.' ORDER BY c.`date_add` DESC ');

        $result3 = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow('
        SELECT (YEAR(CURRENT_DATE)-YEAR(c.`birthday`)) - (RIGHT(CURRENT_DATE, 5)<RIGHT(c.`birthday`, 5)) AS age
        FROM `'._DB_PREFIX_.'customer` c
        WHERE c.`id_customer` = '.(int) $this->id);

        $result['last_visit'] = $result2['last_visit'];
        $result['age'] = ($result3['age'] != date('Y') ? $result3['age'] : '--');

        return $result;
    }

    /**
     * Get last 10 emails sent to the Customer.
     *
     * @return array|false|mysqli_result|null|PDOStatement|resource
     */
    public function getLastEmails()
    {
        if (!$this->id) {
            return array();
        }

        return Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('
        SELECT m.*, l.name as language
        FROM `'._DB_PREFIX_.'mail` m
        LEFT JOIN `'._DB_PREFIX_.'lang` l ON m.id_lang = l.id_lang
        WHERE `recipient` = "'.pSQL($this->email).'"
        ORDER BY m.date_add DESC
        LIMIT 10');
    }

    /**
     * Get last 10 Connections of the Customer.
     *
     * @return array|false|mysqli_result|null|PDOStatement|resource
     */
    public function getLastConnections()
    {
        if (!$this->id) {
            return array();
        }

        return Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('
    		SELECT c.id_connections, c.date_add, COUNT(cp.id_page) AS pages, TIMEDIFF(MAX(cp.time_end), c.date_add) as time, http_referer,INET_NTOA(ip_address) as ipaddress
    		FROM `'._DB_PREFIX_.'guest` g
    		LEFT JOIN `'._DB_PREFIX_.'connections` c ON c.id_guest = g.id_guest
    		LEFT JOIN `'._DB_PREFIX_.'connections_page` cp ON c.id_connections = cp.id_connections
    		WHERE g.`id_customer` = '.(int) $this->id.'
    		GROUP BY c.`id_connections`
    		ORDER BY c.date_add DESC
    		LIMIT 10'
        );
    }

    /**
     * Check if Customer ID exists.
     *
     * @param int $idCustomer Customer ID
     *
     * @return int|null Customer ID if found
     */
    public static function customerIdExistsStatic($idCustomer)
    {
        $cacheId = 'Customer::customerIdExistsStatic'.(int) $idCustomer;
        if (!Cache::isStored($cacheId)) {
            $result = (int) Db::getInstance()->getValue('
            SELECT `id_customer`
            FROM '._DB_PREFIX_.'customer c
            WHERE c.`id_customer` = '.(int) $idCustomer);
            Cache::store($cacheId, $result);

            return $result;
        }

        return Cache::retrieve($cacheId);
    }

    /**
     * Update customer groups associated to the object.
     *
     * @param array $list groups
     */
    public function updateGroup($list)
    {
        Hook::exec('actionCustomerBeforeUpdateGroup', array('id_customer' => $this->id, 'groups' => $list));
        if ($list && !empty($list)) {
            $this->cleanGroups();
            $this->addGroups($list);
        } else {
            $this->addGroups(array($this->id_default_group));
        }
    }

    /**
     * Remove this Customer ID from Customer Groups.
     *
     * @return bool Indicates whether the Customer ID has been successfully removed
     *              from the Customer Group Db table
     */
    public function cleanGroups()
    {
        return Db::getInstance()->delete('customer_group', 'id_customer = '.(int) $this->id);
    }

    /**
     * Add the Customer to the given Customer Groups.
     *
     * @param array $groups Customer Group IDs
     */
    public function addGroups($groups)
    {
        Hook::exec('actionCustomerAddGroups', array('id_customer' => $this->id, 'groups' => $groups));
        foreach ($groups as $group) {
            $row = array('id_customer' => (int) $this->id, 'id_group' => (int) $group);
            Db::getInstance()->insert('customer_group', $row, false, true, Db::INSERT_IGNORE);
        }
    }

    /**
     * Get Groups that have the given Customer ID.
     *
     * @param int $idCustomer Customer ID
     *
     * @return array|mixed
     */
    public static function getGroupsStatic($idCustomer)
    {
        if (!Group::isFeatureActive()) {
            return array(Configuration::get('PS_CUSTOMER_GROUP'));
        }

        if ($idCustomer == 0) {
            self::$_customer_groups[$idCustomer] = array((int) Configuration::get('PS_UNIDENTIFIED_GROUP'));
        }

        if (!isset(self::$_customer_groups[$idCustomer])) {
            self::$_customer_groups[$idCustomer] = array();
            $result = Db::getInstance()->executeS('
            SELECT cg.`id_group`
            FROM '._DB_PREFIX_.'customer_group cg
            WHERE cg.`id_customer` = '.(int) $idCustomer);
            foreach ($result as $group) {
                self::$_customer_groups[$idCustomer][] = (int) $group['id_group'];
            }
        }

        return self::$_customer_groups[$idCustomer];
    }

    public function getGroups()
    {
        return Customer::getGroupsStatic((int) $this->id);
    }

    /**
     * Get Products bought by this Customer.
     *
     * @return array|false|mysqli_result|null|PDOStatement|resource
     */
    public function getBoughtProducts()
    {
        return Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('
        SELECT * FROM `'._DB_PREFIX_.'orders` o
        LEFT JOIN `'._DB_PREFIX_.'order_detail` od ON o.id_order = od.id_order
        WHERE o.valid = 1 AND o.`id_customer` = '.(int) $this->id);
    }

    /**
     * Get Default Customer Group ID.
     *
     * @param int $idCustomer Customer ID
     *
     * @return mixed|null|string
     */
    public static function getDefaultGroupId($idCustomer)
    {
        if (!Group::isFeatureActive()) {
            static $psCustomerGroup = null;
            if ($psCustomerGroup === null) {
                $psCustomerGroup = Configuration::get('PS_CUSTOMER_GROUP');
            }

            return $psCustomerGroup;
        }

        if (!isset(self::$_defaultGroupId[(int) $idCustomer])) {
            self::$_defaultGroupId[(int) $idCustomer] = Db::getInstance()->getValue('
                SELECT `id_default_group`
                FROM `'._DB_PREFIX_.'customer`
                WHERE `id_customer` = '.(int) $idCustomer
            );
        }

        return self::$_defaultGroupId[(int) $idCustomer];
    }

    /**
     * Get current Country.
     *
     * @param int       $idCustomer
     * @param Cart|null $cart
     *
     * @return int Country ID
     */
    public static function getCurrentCountry($idCustomer, Cart $cart = null)
    {
        if (!$cart) {
            $cart = Context::getContext()->cart;
        }
        if (!$cart || !$cart->{Configuration::get('PS_TAX_ADDRESS_TYPE')}) {
            $idAddress = (int) Db::getInstance()->getValue('
                SELECT `id_address`
                FROM `'._DB_PREFIX_.'address`
                WHERE `id_customer` = '.(int) $idCustomer.'
                AND `deleted` = 0 ORDER BY `id_address`'
            );
        } else {
            $idAddress = $cart->{Configuration::get('PS_TAX_ADDRESS_TYPE')};
        }
        $ids = Address::getCountryAndState($idAddress);

        return (int) ($ids['id_country'] ? $ids['id_country'] : Configuration::get('PS_COUNTRY_DEFAULT'));
    }

    /**
     * Toggle Customer status.
     *
     * @return bool Indicates whether the status has been successfully toggled
     */
    public function toggleStatus()
    {
        parent::toggleStatus();

        /* Change status to active/inactive */
        return Db::getInstance()->execute('
        UPDATE `'._DB_PREFIX_.bqSQL($this->def['table']).'`
        SET `date_upd` = NOW()
        WHERE `'.bqSQL($this->def['primary']).'` = '.(int) $this->id);
    }

    /**
     * Is the current Customer a Guest?
     *
     * @return bool Indicates whether the Customer is a Guest
     */
    public function isGuest()
    {
        return (bool) $this->is_guest;
    }

    /**
     * Transform the Guest to a Customer.
     *
     * @param int         $idLang   Language ID
     * @param string|null $password Password
     *
     * @return bool
     */
    public function transformToCustomer($idLang, $password = null)
    {
        if (!$this->isGuest()) {
            return false;
        }
        if (empty($password)) {
            $password = Tools::passwdGen(8, 'RANDOM');
        }
        if (!Validate::isPasswd($password)) {
            return false;
        }

        $language = new Language($idLang);
        if (!Validate::isLoadedObject($language)) {
            $language = Context::getContext()->language;
        }

        /** @var \PrestaShop\PrestaShop\Core\Crypto\Hashing $crypto */
        $crypto = ServiceLocator::get('\\PrestaShop\\PrestaShop\\Core\\Crypto\\Hashing');
        $this->is_guest = 0;
        $this->passwd = $crypto->hash($password);
        $this->cleanGroups();
        $this->addGroups(array(Configuration::get('PS_CUSTOMER_GROUP')));
        $this->id_default_group = Configuration::get('PS_CUSTOMER_GROUP');
        if ($this->update()) {
            $vars = array(
                '{firstname}' => $this->firstname,
                '{lastname}' => $this->lastname,
                '{email}' => $this->email,
            );
            Mail::Send(
                (int) $idLang,
                'guest_to_customer',
                Context::getContext()->getTranslator()->trans(
                    'Your guest account has been transformed into a customer account',
                    array(),
                    'Emails.Subject',
                    $language->locale
                ),
                $vars,
                $this->email,
                $this->firstname.' '.$this->lastname,
                null,
                null,
                null,
                null,
                _PS_MAIL_DIR_,
                false,
                (int) $this->id_shop
            );

            return true;
        }

        return false;
    }

    /**
     * Set password
     * (for webservice).
     *
     * @param string $passwd Password
     *
     * @return bool Indictes whether the password has been successfully set
     */
    public function setWsPasswd($passwd)
    {
        /** @var \PrestaShop\PrestaShop\Core\Crypto\Hashing $crypto */
        $crypto = ServiceLocator::get('\\PrestaShop\\PrestaShop\\Core\\Crypto\\Hashing');
        if ($this->id == 0 || $this->passwd != $passwd) {
            $this->passwd = $crypto->hash($passwd);
        }

        return true;
    }

    /**
     * Check customer information and return customer validity.
     *
     * @since 1.5.0
     *
     * @param bool $withGuest
     *
     * @return bool customer validity
     */
    public function isLogged($withGuest = false)
    {
        if (!$withGuest && $this->is_guest == 1) {
            return false;
        }

        /* Customer is valid only if it can be load and if object password is the same as database one */
        return $this->logged == 1 && $this->id && Validate::isUnsignedId($this->id) && Customer::checkPassword($this->id, $this->passwd);
    }

    /**
     * Logout.
     *
     * @since 1.5.0
     */
    public function logout()
    {
        Hook::exec('actionCustomerLogoutBefore', array('customer' => $this));

        if (isset(Context::getContext()->cookie)) {
            Context::getContext()->cookie->logout();
        }

        $this->logged = 0;

        Hook::exec('actionCustomerLogoutAfter', array('customer' => $this));
    }

    /**
     * Soft logout, delete everything that links to the customer
     * but leave there affiliate's information.
     *
     * @since 1.5.0
     */
    public function mylogout()
    {
        Hook::exec('actionCustomerLogoutBefore', array('customer' => $this));

        if (isset(Context::getContext()->cookie)) {
            Context::getContext()->cookie->mylogout();
        }

        $this->logged = 0;

        Hook::exec('actionCustomerLogoutAfter', array('customer' => $this));
    }

    /**
     * Get last empty Cart for this Customer, when last cart is not empty return false.
     *
     * @param bool|true $withOrder
     *
     * @return bool|int
     */
    public function getLastEmptyCart($withOrder = true)
    {
        $carts = Cart::getCustomerCarts((int) $this->id, $withOrder);
        if (!count($carts)) {
            return false;
        }
        $cart = array_shift($carts);
        $cart = new Cart((int) $cart['id_cart']);

        return $cart->nbProducts() === 0 ? (int) $cart->id : false;
    }

    /**
     * Validate controller.
     *
     * @param bool $htmlentities
     *
     * @return array
     */
    public function validateController($htmlentities = true)
    {
        $errors = parent::validateController($htmlentities);
        /** @var \PrestaShop\PrestaShop\Core\Crypto\Hashing $crypto */
        $crypto = ServiceLocator::get('\\PrestaShop\\PrestaShop\\Core\\Crypto\\Hashing');
        if ($value = Tools::getValue('passwd')) {
            $this->passwd = $crypto->hash($value);
        }

        return $errors;
    }

    /**
     * Get outstanding amount.
     *
     * @return float Outstanding amount
     */
    public function getOutstanding()
    {
        $query = new DbQuery();
        $query->select('SUM(oi.total_paid_tax_incl)');
        $query->from('order_invoice', 'oi');
        $query->leftJoin('orders', 'o', 'oi.id_order = o.id_order');
        $query->groupBy('o.id_customer');
        $query->where('o.id_customer = '.(int) $this->id);
        $totalPaid = (float) Db::getInstance()->getValue($query->build());

        $query = new DbQuery();
        $query->select('SUM(op.amount)');
        $query->from('order_payment', 'op');
        $query->leftJoin('order_invoice_payment', 'oip', 'op.id_order_payment = oip.id_order_payment');
        $query->leftJoin('orders', 'o', 'oip.id_order = o.id_order');
        $query->groupBy('o.id_customer');
        $query->where('o.id_customer = '.(int) $this->id);
        $totalRest = (float) Db::getInstance()->getValue($query->build());

        return $totalPaid - $totalRest;
    }

    /**
     * Get Customer Groups
     * (for webservice).
     *
     * @return array|false|mysqli_result|null|PDOStatement|resource
     */
    public function getWsGroups()
    {
        return Db::getInstance()->executeS('
            SELECT cg.`id_group` as id
            FROM '._DB_PREFIX_.'customer_group cg
            '.Shop::addSqlAssociation('group', 'cg').'
            WHERE cg.`id_customer` = '.(int) $this->id
        );
    }

    /**
     * Set Customer Groups
     * (for webservice).
     *
     * @param $result
     *
     * @return bool
     */
    public function setWsGroups($result)
    {
        $groups = array();
        foreach ($result as $row) {
            $groups[] = $row['id'];
        }
        $this->cleanGroups();
        $this->addGroups($groups);

        return true;
    }

    /**
     * @see ObjectModel::getWebserviceObjectList()
     */
    public function getWebserviceObjectList($sqlJoin, $sqlFilter, $sqlSort, $sqlLimit)
    {
        $sqlFilter .= Shop::addSqlRestriction(Shop::SHARE_CUSTOMER, 'main');

        return parent::getWebserviceObjectList($sqlJoin, $sqlFilter, $sqlSort, $sqlLimit);
    }

    /**
     * Fill Reset password unique token with random sha1 and its validity date. For forgot password feature.
     */
    public function stampResetPasswordToken()
    {
        $salt = $this->id.'-'.$this->secure_key;
        $this->reset_password_token = sha1(time().$salt);
        $validity = (int) Configuration::get('PS_PASSWD_RESET_VALIDITY') ?: 1440;
        $this->reset_password_validity = date('Y-m-d H:i:s', strtotime('+'.$validity.' minutes'));
    }

    /**
     * Test if a reset password token is present and is recent enough to avoid creating a new one (in case of customer triggering the forgot password link too often).
     */
    public function hasRecentResetPasswordToken()
    {
        if (!$this->reset_password_token || $this->reset_password_token == '') {
            return false;
        }

        // TODO maybe use another 'recent' value for this test. For instance, equals password validity value.
        if (!$this->reset_password_validity || strtotime($this->reset_password_validity) < time()) {
            return false;
        }

        return true;
    }

    /**
     * Returns the valid reset password token if it validity date is > now().
     */
    public function getValidResetPasswordToken()
    {
        if (!$this->reset_password_token || $this->reset_password_token == '') {
            return false;
        }

        if (!$this->reset_password_validity || strtotime($this->reset_password_validity) < time()) {
            return false;
        }

        return $this->reset_password_token;
    }

    /**
     * Delete reset password token data.
     */
    public function removeResetPasswordToken()
    {
        $this->reset_password_token = null;
        $this->reset_password_validity = null;
    }
}
