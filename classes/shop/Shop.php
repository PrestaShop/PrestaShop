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

use PrestaShop\PrestaShop\Core\Addon\Theme\Theme;
use PrestaShop\PrestaShop\Core\Addon\Theme\ThemeManagerBuilder;
use Symfony\Contracts\Cache\ItemInterface;

/**
 * @since 1.5.0
 */
class ShopCore extends ObjectModel
{
    /** @var int ID of shop group */
    public $id_shop_group;

    /** @var int ID of shop category */
    public $id_category;

    /** @var string directory name of the selected theme */
    public $theme_name;

    /** @var string Shop name */
    public $name;

    /** @var string Shop color */
    public $color;

    public $active = true;
    public $deleted;

    /** @var string Physical uri of main url (read only) */
    public $physical_uri;

    /** @var string Virtual uri of main url (read only) */
    public $virtual_uri;

    /** @var string Domain of main url (read only) */
    public $domain;

    /** @var string Domain SSL of main url (read only) */
    public $domain_ssl;

    /** @var ShopGroup|null Shop group object */
    protected $group;

    /**
     * @var Address|null
     */
    public $address;

    /**
     * @see ObjectModel::$definition
     */
    public static $definition = [
        'table' => 'shop',
        'primary' => 'id_shop',
        'fields' => [
            'active' => ['type' => self::TYPE_BOOL, 'validate' => 'isBool'],
            'deleted' => ['type' => self::TYPE_BOOL, 'validate' => 'isBool'],
            'name' => ['type' => self::TYPE_STRING, 'validate' => 'isGenericName', 'required' => true, 'size' => 64],
            'color' => ['type' => self::TYPE_STRING, 'validate' => 'isColor'],
            'id_category' => ['type' => self::TYPE_INT, 'required' => true],
            'theme_name' => ['type' => self::TYPE_STRING, 'validate' => 'isThemeName'],
            'id_shop_group' => ['type' => self::TYPE_INT, 'required' => true],
        ],
    ];

    /** @var array|null List of shops cached */
    protected static $shops;

    protected static $asso_tables = [];
    protected static $id_shop_default_tables = [];
    protected static $initialized = false;

    protected $webserviceParameters = [
        'fields' => [
            'id_shop_group' => ['xlink_resource' => 'shop_groups'],
            'id_category' => [],
        ],
    ];

    /** @var int|null Store the current context of shop (CONTEXT_ALL, CONTEXT_GROUP, CONTEXT_SHOP) */
    protected static $context;

    /** @var int|null ID shop in the current context (will be empty if context is not CONTEXT_SHOP) */
    protected static $context_id_shop;

    /** @var int|null ID shop group in the current context (will be empty if context is CONTEXT_ALL) */
    protected static $context_id_shop_group;

    /** @var ShopGroup|null Context shop group kept as cache */
    protected static $context_shop_group = null;

    /** @var bool|null is multistore activated */
    protected static $feature_active;

    /** @var Theme * */
    public $theme;

    /**
     * There are 3 kinds of shop context : shop, group shop and general.
     */
    public const CONTEXT_SHOP = 1;
    public const CONTEXT_GROUP = 2;
    public const CONTEXT_ALL = 4;

    /**
     * Some data can be shared between shops, like customers or orders.
     */
    public const SHARE_CUSTOMER = 'share_customer';
    public const SHARE_ORDER = 'share_order';
    public const SHARE_STOCK = 'share_stock';

    /**
     * On shop instance, get its URL data.
     *
     * @param int $id
     * @param int $id_lang
     * @param int $id_shop
     */
    public function __construct($id = null, $id_lang = null, $id_shop = null)
    {
        parent::__construct($id, $id_lang, $id_shop);

        if ($this->id) {
            $this->setUrl();
            if ($this->theme == null) {
                $this->setTheme();
            }
        }
    }

    /**
     * Initialize an array with all the multistore associations in the database.
     */
    protected static function init()
    {
        Shop::$id_shop_default_tables = ['product', 'category'];

        $asso_tables = [
            'carrier' => ['type' => 'shop'],
            'carrier_lang' => ['type' => 'fk_shop'],
            'category' => ['type' => 'shop'],
            'category_lang' => ['type' => 'fk_shop'],
            'cms' => ['type' => 'shop'],
            'cms_lang' => ['type' => 'fk_shop'],
            'cms_category' => ['type' => 'shop'],
            'cms_category_lang' => ['type' => 'fk_shop'],
            'contact' => ['type' => 'shop'],
            'country' => ['type' => 'shop'],
            'currency' => ['type' => 'shop'],
            'employee' => ['type' => 'shop'],
            'hook_module' => ['type' => 'fk_shop'],
            'hook_module_exceptions' => ['type' => 'fk_shop', 'primary' => 'id_hook_module_exceptions'],
            'image' => ['type' => 'shop'],
            'lang' => ['type' => 'shop'],
            'meta_lang' => ['type' => 'fk_shop'],
            'module' => ['type' => 'shop'],
            'module_currency' => ['type' => 'fk_shop'],
            'module_country' => ['type' => 'fk_shop'],
            'module_group' => ['type' => 'fk_shop'],
            'product' => ['type' => 'shop'],
            'product_attribute' => ['type' => 'shop'],
            'product_lang' => ['type' => 'fk_shop'],
            'customization_field_lang' => ['type' => 'fk_shop'],
            'store' => ['type' => 'shop'],
            'webservice_account' => ['type' => 'shop'],
            'warehouse' => ['type' => 'shop'],
            'stock_available' => ['type' => 'fk_shop', 'primary' => 'id_stock_available'],
            'carrier_tax_rules_group_shop' => ['type' => 'fk_shop'],
            'attribute' => ['type' => 'shop'],
            'feature' => ['type' => 'shop'],
            'group' => ['type' => 'shop'],
            'attribute_group' => ['type' => 'shop'],
            'tax_rules_group' => ['type' => 'shop'],
            'zone' => ['type' => 'shop'],
            'manufacturer' => ['type' => 'shop'],
            'supplier' => ['type' => 'shop'],
        ];

        foreach ($asso_tables as $table_name => $table_details) {
            Shop::addTableAssociation($table_name, $table_details);
        }

        Shop::$initialized = true;
    }

    public function setUrl()
    {
        $cache_id = 'Shop::setUrl_' . (int) $this->id;
        if (!Cache::isStored($cache_id)) {
            $row = Db::getInstance()->getRow('
              SELECT su.physical_uri, su.virtual_uri, su.domain, su.domain_ssl
              FROM ' . _DB_PREFIX_ . 'shop s
              LEFT JOIN ' . _DB_PREFIX_ . 'shop_url su ON (s.id_shop = su.id_shop)
              WHERE s.id_shop = ' . (int) $this->id . '
              AND s.active = 1 AND s.deleted = 0 AND su.main = 1');
            Cache::store($cache_id, $row);
        } else {
            $row = Cache::retrieve($cache_id);
        }
        if (!$row) {
            return false;
        }

        $this->physical_uri = $row['physical_uri'];
        $this->virtual_uri = $row['virtual_uri'];
        $this->domain = $row['domain'];
        $this->domain_ssl = $row['domain_ssl'];

        return true;
    }

    /**
     * Add a shop, and clear the cache.
     *
     * @param bool $autodate
     * @param bool $null_values
     *
     * @return bool
     */
    public function add($autodate = true, $null_values = false)
    {
        $res = parent::add($autodate, $null_values);
        Shop::resetStaticCache();
        Shop::cacheShops(true);

        return $res;
    }

    public function associateSuperAdmins()
    {
        $super_admins = Employee::getEmployeesByProfile(_PS_ADMIN_PROFILE_);
        foreach ($super_admins as $super_admin) {
            $employee = new Employee((int) $super_admin['id_employee']);
            $employee->associateTo((int) $this->id);
        }
    }

    /**
     * Remove a shop only if it has no dependencies, and remove its associations.
     *
     * @return bool
     */
    public function delete()
    {
        if (Shop::hasDependency($this->id) || !$res = parent::delete()) {
            return false;
        }

        foreach (Shop::getAssoTables() as $table_name => $row) {
            $id = 'id_' . $row['type'];
            if ($row['type'] == 'fk_shop') {
                $id = 'id_shop';
            } else {
                $table_name .= '_' . $row['type'];
            }
            $res &= Db::getInstance()->execute(
                'DELETE FROM `' . bqSQL(_DB_PREFIX_ . $table_name) . '`
                WHERE `' . bqSQL($id) . '`=' . (int) $this->id
            );
        }

        // removes stock available
        $res = $res && Db::getInstance()->delete('stock_available', 'id_shop = ' . (int) $this->id);

        // Remove urls
        $res = $res && Db::getInstance()->delete('shop_url', 'id_shop = ' . (int) $this->id);

        // Remove currency restrictions
        $res = $res && Db::getInstance()->delete('module_currency', 'id_shop = ' . (int) $this->id);

        // Remove group restrictions
        $res = $res && Db::getInstance()->delete('module_group', 'id_shop = ' . (int) $this->id);

        // Remove country restrictions
        $res = $res && Db::getInstance()->delete('module_country', 'id_shop = ' . (int) $this->id);

        // Remove carrier restrictions
        $res = $res && Db::getInstance()->delete('module_carrier', 'id_shop = ' . (int) $this->id);

        Shop::cacheShops(true);

        return $res;
    }

    /**
     * Detect dependency with customer or orders.
     *
     * @param int $id_shop
     *
     * @return bool
     */
    public static function hasDependency($id_shop)
    {
        $has_dependency = false;
        $nbr_customer = (int) Db::getInstance()->getValue(
            'SELECT count(*)
            FROM `' . _DB_PREFIX_ . 'customer`
            WHERE `id_shop`=' . (int) $id_shop
        );
        if ($nbr_customer) {
            $has_dependency = true;
        } else {
            $nbr_order = (int) Db::getInstance()->getValue(
                'SELECT count(*)
                FROM `' . _DB_PREFIX_ . 'orders`
                WHERE `id_shop`=' . (int) $id_shop
            );
            if ($nbr_order) {
                $has_dependency = true;
            }
        }

        return $has_dependency;
    }

    /**
     * Find the shop from current domain / uri and get an instance of this shop
     * if INSTALL_VERSION is defined, will return an empty shop object.
     *
     * @return Shop
     */
    public static function initialize()
    {
        // Find current shop from URL
        if (!($id_shop = Tools::getValue('id_shop')) || defined('_PS_ADMIN_DIR_')) {
            $found_uri = '';
            $is_main_uri = false;
            $host = Tools::getHttpHost(false, false, true);
            $request_uri = rawurldecode($_SERVER['REQUEST_URI']);

            $result = self::findShopByHost($host);

            // If could not find a matching, try with port
            if (empty($result)) {
                $host = Tools::getHttpHost(false, false, false);
                $result = self::findShopByHost($host);
            }

            $through = false;
            foreach ($result as $row) {
                // An URL matching current shop was found
                if (preg_match('#^' . preg_quote($row['uri'], '#') . '#i', $request_uri)) {
                    $through = true;
                    $id_shop = $row['id_shop'];
                    $found_uri = $row['uri'];
                    if ($row['main']) {
                        $is_main_uri = true;
                    }

                    break;
                }
            }

            // If an URL was found but is not the main URL, redirect to main URL
            if ($through && $id_shop && !$is_main_uri) {
                foreach ($result as $row) {
                    if ($row['id_shop'] == $id_shop && $row['main']) {
                        $request_uri = substr($request_uri, strlen($found_uri));
                        $url = str_replace('//', '/', $row['domain'] . $row['uri'] . $request_uri);
                        $redirect_type = Configuration::get('PS_CANONICAL_REDIRECT');
                        $redirect_code = ($redirect_type == 1 ? '302' : '301');
                        $redirect_header = ($redirect_type == 1 ? 'Found' : 'Moved Permanently');
                        header('HTTP/1.0 ' . $redirect_code . ' ' . $redirect_header);
                        header('Cache-Control: no-cache');
                        header('Location: ' . Tools::getShopProtocol() . $url);
                        exit;
                    }
                }
            }
        }

        $http_host = Tools::getHttpHost();
        $all_media = SymfonyCache::getInstance()->get('all_media', function (ItemInterface $item) {
            $item->tag(['configuration']);
            return array_merge(
                Configuration::getMultiShopValues('PS_MEDIA_SERVER_1'),
                Configuration::getMultiShopValues('PS_MEDIA_SERVER_2'),
                Configuration::getMultiShopValues('PS_MEDIA_SERVER_3')
            );
        });

        $isAllShop = 'all' === $id_shop;
        $isApiInUse = defined('_PS_API_IN_USE_') && _PS_API_IN_USE_;
        if ((!$id_shop && defined('_PS_ADMIN_DIR_')) || ($isAllShop && $isApiInUse) || Tools::isPHPCLI() || in_array($http_host, $all_media)) {
            // If in admin, we can access to the shop without right URL
            if ((!$id_shop && Tools::isPHPCLI()) || defined('_PS_ADMIN_DIR_')) {
                $id_shop = (int) Configuration::get('PS_SHOP_DEFAULT');
            }

            $shop = new Shop((int) $id_shop);
            if (!Validate::isLoadedObject($shop)) {
                $shop = new Shop((int) Configuration::get('PS_SHOP_DEFAULT'));
            }

            $shop->virtual_uri = '';

            // Define some $_SERVER variables like HTTP_HOST if PHP is launched with php-cli
            if (Tools::isPHPCLI()) {
                if (!isset($_SERVER['HTTP_HOST']) || empty($_SERVER['HTTP_HOST'])) {
                    $_SERVER['HTTP_HOST'] = $shop->domain;
                }
                if (!isset($_SERVER['SERVER_NAME']) || empty($_SERVER['SERVER_NAME'])) {
                    $_SERVER['SERVER_NAME'] = $shop->domain;
                }
                if (!isset($_SERVER['REMOTE_ADDR']) || empty($_SERVER['REMOTE_ADDR'])) {
                    $_SERVER['REMOTE_ADDR'] = '127.0.0.1';
                }
            }
        } else {
            $shop = new Shop($id_shop);
            if (!Validate::isLoadedObject($shop) || !$shop->active) {
                // No shop found ... too bad, let's redirect to default shop
                $default_shop = new Shop((int) Configuration::get('PS_SHOP_DEFAULT'));

                // Hmm there is something really bad in your Prestashop !
                if (!Validate::isLoadedObject($default_shop)) {
                    throw new PrestaShopException('Shop not found');
                }

                $params = $_GET;
                unset($params['id_shop']);
                $url = $default_shop->domain;
                if (!Configuration::get('PS_REWRITING_SETTINGS')) {
                    $url .= $default_shop->getBaseURI() . 'index.php?' . http_build_query($params);
                } else {
                    // Catch url with subdomain "www"
                    if (strpos($url, 'www.') === 0 && 'www.' . $_SERVER['HTTP_HOST'] === $url || $_SERVER['HTTP_HOST'] === 'www.' . $url) {
                        $url .= $_SERVER['REQUEST_URI'];
                    } else {
                        $url .= $default_shop->getBaseURI();
                    }

                    if (count($params)) {
                        $url .= '?' . http_build_query($params);
                    }
                }

                $redirect_type = Configuration::get('PS_CANONICAL_REDIRECT');
                $redirect_code = ($redirect_type == 1 ? '302' : '301');
                $redirect_header = ($redirect_type == 1 ? 'Found' : 'Moved Permanently');
                header('HTTP/1.0 ' . $redirect_code . ' ' . $redirect_header);
                header('Location: ' . Tools::getShopProtocol() . $url);
                exit;
            } elseif (defined('_PS_ADMIN_DIR_') && empty($shop->physical_uri)) {
                $shop_default = new Shop((int) Configuration::get('PS_SHOP_DEFAULT'));
                $shop->physical_uri = $shop_default->physical_uri;
                $shop->virtual_uri = $shop_default->virtual_uri;
            }
        }

        self::$context_id_shop = $shop->id;
        self::$context_id_shop_group = $shop->id_shop_group;
        static::$context_shop_group = null;
        self::$context = self::CONTEXT_SHOP;

        return $shop;
    }

    /**
     * @return Address the current shop address
     */
    public function getAddress()
    {
        if (!isset($this->address)) {
            $address = new Address();
            $address->company = Configuration::get('PS_SHOP_NAME');
            $address->id_country = Configuration::get('PS_SHOP_COUNTRY_ID') ? (int) Configuration::get('PS_SHOP_COUNTRY_ID') : (int) Configuration::get('PS_COUNTRY_DEFAULT');
            $address->id_state = (int) Configuration::get('PS_SHOP_STATE_ID');
            $address->address1 = Configuration::get('PS_SHOP_ADDR1');
            $address->address2 = Configuration::get('PS_SHOP_ADDR2');
            $address->postcode = Configuration::get('PS_SHOP_CODE');
            $address->city = Configuration::get('PS_SHOP_CITY');

            $this->address = $address;
        }

        return $this->address;
    }

    /**
     * Set shop theme details from Json data.
     */
    public function setTheme()
    {
        $themeManagerBuilder = new ThemeManagerBuilder(Context::getContext(), Db::getInstance());
        $themeRepository = $themeManagerBuilder->buildRepository($this instanceof Shop ? $this : null);
        if (empty($this->theme_name)) {
            $this->theme_name = 'classic';
        }
        $this->theme = $themeRepository->getInstanceByName($this->theme_name);
    }

    /**
     * Get shop URI.
     *
     * @return string
     */
    public function getBaseURI()
    {
        return $this->physical_uri . $this->virtual_uri;
    }

    /**
     * Get shop URL.
     *
     * @param bool $auto_secure_mode if set to true, secure mode will be checked
     * @param bool $add_base_uri if set to true, shop base uri will be added
     *
     * @return string|bool complete base url of current shop
     */
    public function getBaseURL($auto_secure_mode = true, $add_base_uri = true)
    {
        if ($auto_secure_mode && Tools::usingSecureMode()) {
            if (!$this->domain_ssl) {
                return false;
            }
            $url = 'https://' . $this->domain_ssl;
        } else {
            if (!$this->domain) {
                return false;
            }
            $url = 'http://' . $this->domain;
        }

        if ($add_base_uri) {
            $url .= $this->getBaseURI();
        }

        return $url;
    }

    /**
     * Get group of current shop.
     *
     * @return ShopGroup
     */
    public function getGroup()
    {
        if (!$this->group) {
            $this->group = new ShopGroup($this->id_shop_group);
        }

        return $this->group;
    }

    /**
     * Get root category of current shop.
     *
     * @return int
     */
    public function getCategory()
    {
        return (int) ($this->id_category ? $this->id_category : Configuration::get('PS_ROOT_CATEGORY'));
    }

    /**
     * Get list of shop's urls.
     *
     * @return array
     */
    public function getUrls()
    {
        $sql = 'SELECT *
                FROM ' . _DB_PREFIX_ . 'shop_url
                WHERE active = 1
                    AND id_shop = ' . (int) $this->id;

        return Db::getInstance()->executeS($sql);
    }

    /**
     * Check if current shop ID is the same as default shop in configuration.
     *
     * @return bool
     */
    public function isDefaultShop()
    {
        return $this->id == Configuration::get('PS_SHOP_DEFAULT');
    }

    /**
     * Get the associated table if available.
     *
     * @return array|false
     */
    public static function getAssoTable($table)
    {
        if (!Shop::$initialized) {
            Shop::init();
        }

        return Shop::$asso_tables[$table] ?? false;
    }

    /**
     * check if the table has an id_shop_default.
     *
     * @return bool
     */
    public static function checkIdShopDefault($table)
    {
        if (!Shop::$initialized) {
            Shop::init();
        }

        return in_array($table, self::$id_shop_default_tables);
    }

    /**
     * Get list of associated tables to shop.
     *
     * @return array
     */
    public static function getAssoTables()
    {
        if (!Shop::$initialized) {
            Shop::init();
        }

        return Shop::$asso_tables;
    }

    /**
     * Add table associated to shop.
     *
     * @param string $table_name
     * @param array $table_details
     *
     * @return bool
     */
    public static function addTableAssociation($table_name, $table_details)
    {
        if (!isset(Shop::$asso_tables[$table_name])) {
            Shop::$asso_tables[$table_name] = $table_details;
        } else {
            return false;
        }

        return true;
    }

    /**
     * Check if given table is associated to shop.
     *
     * @param string $table
     *
     * @return bool
     */
    public static function isTableAssociated($table)
    {
        if (!Shop::$initialized) {
            Shop::init();
        }

        return isset(Shop::$asso_tables[$table]) && Shop::$asso_tables[$table]['type'] == 'shop';
    }

    /**
     * Load list of groups and shops, and cache it.
     *
     * @param bool $refresh
     */
    public static function cacheShops($refresh = false)
    {
        if (null !== self::$shops && !$refresh) {
            return;
        }
        if ($refresh) {
            SymfonyCache::getInstance()->invalidateTags(['shop']);
        }
        $cache_id = 'shops';
        $employee_id = null;
        $employee = Context::getContext()->employee;

        // If Front Office or if the profile isn't a superAdmin
        if (Validate::isLoadedObject($employee) && $employee->id_profile != _PS_ADMIN_PROFILE_) {
            $employee_id = (int) $employee->id;
            $cache_id .= ' _ ' . $employee_id;
        }

        self::$shops = SymfonyCache::getInstance()->get($cache_id, function (ItemInterface $item) use ($employee_id) {
            $item->tag('shop');
            $value = [];

            $from = '';
            $where = '';

            if (!empty($employee_id)) {
                $from .= 'LEFT JOIN ' . _DB_PREFIX_ . 'employee_shop es ON es.id_shop = s.id_shop';
                $where .= 'AND es.id_employee = ' . $employee_id;
            }

            $sql = 'SELECT gs.*, s.*, gs.name AS group_name, s.name AS shop_name, s.active, su.domain, su.domain_ssl, su.physical_uri, su.virtual_uri
                FROM ' . _DB_PREFIX_ . 'shop_group gs
                LEFT JOIN ' . _DB_PREFIX_ . 'shop s
                    ON s.id_shop_group = gs.id_shop_group
                LEFT JOIN ' . _DB_PREFIX_ . 'shop_url su
                    ON s.id_shop = su.id_shop AND su.main = 1
                ' . $from . '
                WHERE s.deleted = 0
                    AND gs.deleted = 0
                    ' . $where . '
                ORDER BY gs.name, s.name';

            if ($results = Db::getInstance()->executeS($sql)) {
                foreach ($results as $row) {
                    if (!isset($value[$row['id_shop_group']])) {
                        $value[$row['id_shop_group']] = [
                            'id' => $row['id_shop_group'],
                            'name' => $row['group_name'],
                            'share_customer' => $row['share_customer'],
                            'share_order' => $row['share_order'],
                            'share_stock' => $row['share_stock'],
                            'shops' => [],
                        ];
                    }

                    $row = $row + ['theme_name' => ''];

                    $value[$row['id_shop_group']]['shops'][$row['id_shop']] = [
                        'id_shop' => $row['id_shop'],
                        'id_shop_group' => $row['id_shop_group'],
                        'name' => $row['shop_name'],
                        'id_category' => $row['id_category'],
                        'theme_name' => $row['theme_name'],
                        'domain' => $row['domain'],
                        'domain_ssl' => $row['domain_ssl'],
                        'uri' => $row['physical_uri'] . $row['virtual_uri'],
                        'active' => $row['active'],
                    ];
                }
            }

            return $value;
        });
    }

    public static function getCompleteListOfShopsID()
    {
        $cache_id = 'Shop::getCompleteListOfShopsID';
        if (!Cache::isStored($cache_id)) {
            $list = [];
            $sql = 'SELECT id_shop FROM ' . _DB_PREFIX_ . 'shop';
            foreach (Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql) as $row) {
                $list[] = $row['id_shop'];
            }

            Cache::store($cache_id, $list);

            return $list;
        }

        return Cache::retrieve($cache_id);
    }

    /**
     * Get shops list.
     *
     * @param bool $active
     * @param int $id_shop_group
     * @param bool $get_as_list_id
     *
     * @return array
     */
    public static function getShops($active = true, $id_shop_group = null, $get_as_list_id = false)
    {
        Shop::cacheShops();

        $results = [];
        foreach (self::$shops as $group_id => $group_data) {
            foreach ($group_data['shops'] as $id => $shop_data) {
                if ((!$active || $shop_data['active']) && (!$id_shop_group || $id_shop_group == $group_id)) {
                    if ($get_as_list_id) {
                        $results[$id] = $id;
                    } else {
                        $results[$id] = $shop_data;
                    }
                }
            }
        }

        return $results;
    }

    public function getUrlsSharedCart()
    {
        if (!$this->getGroup()->share_order) {
            return false;
        }

        $query = new DbQuery();
        $query->select('domain');
        $query->from('shop_url');
        $query->where('main = 1');
        $query->where('active = 1');
        $query .= $this->addSqlRestriction(Shop::SHARE_ORDER);
        $domains = [];
        foreach (Db::getInstance()->executeS($query) as $row) {
            $domains[] = $row['domain'];
        }

        return $domains;
    }

    /**
     * Get a collection of shops.
     *
     * @param bool $active
     * @param int $id_shop_group
     *
     * @return PrestaShopCollection Collection of Shop
     */
    public static function getShopsCollection($active = true, $id_shop_group = null)
    {
        $shops = new PrestaShopCollection('Shop');
        if ($active) {
            $shops->where('active', '=', 1);
        }

        if ($id_shop_group) {
            $shops->where('id_shop_group', '=', (int) $id_shop_group);
        }

        return $shops;
    }

    /**
     * Return some informations cached for one shop.
     *
     * @param int $shop_id
     *
     * @return array|bool
     */
    public static function getShop($shop_id)
    {
        Shop::cacheShops();
        foreach (self::$shops as $group_id => $group_data) {
            if (array_key_exists($shop_id, $group_data['shops'])) {
                return $group_data['shops'][$shop_id];
            }
        }

        return false;
    }

    /**
     * Return a shop ID from shop name.
     *
     * @param string $name
     *
     * @return int|bool
     */
    public static function getIdByName($name)
    {
        Shop::cacheShops();
        foreach (self::$shops as $group_data) {
            foreach ($group_data['shops'] as $shop_id => $shop_data) {
                if (Tools::strtolower($shop_data['name']) == Tools::strtolower($name)) {
                    return $shop_id;
                }
            }
        }

        return false;
    }

    /**
     * @param bool $active
     * @param int $id_shop_group
     *
     * @return int Total of shops
     */
    public static function getTotalShops($active = true, $id_shop_group = null)
    {
        return count(Shop::getShops($active, $id_shop_group));
    }

    /**
     * Retrieve group ID of a shop.
     *
     * @param int $shop_id Shop ID
     * @param bool $as_id
     *
     * @return int|array|bool Group ID
     */
    public static function getGroupFromShop($shop_id, $as_id = true)
    {
        Shop::cacheShops();
        foreach (self::$shops as $group_id => $group_data) {
            if (array_key_exists($shop_id, $group_data['shops'])) {
                return $as_id ? $group_id : $group_data;
            }
        }

        return false;
    }

    /**
     * If the shop group has the option $type activated, get all shops ID of this group, else get current shop ID.
     *
     * @param int $shop_id
     * @param string $type Shop::SHARE_CUSTOMER | Shop::SHARE_ORDER
     *
     * @return array
     */
    public static function getSharedShops($shop_id, $type)
    {
        if (!in_array($type, [Shop::SHARE_CUSTOMER, Shop::SHARE_ORDER, Shop::SHARE_STOCK])) {
            die('Wrong argument ($type) in Shop::getSharedShops() method');
        }

        Shop::cacheShops();
        foreach (self::$shops as $group_data) {
            if (array_key_exists($shop_id, $group_data['shops']) && $group_data[$type]) {
                return array_keys($group_data['shops']);
            }
        }

        return [$shop_id];
    }

    /**
     * Get a list of ID concerned by the shop context (E.g. if context is shop group, get list of children shop ID).
     *
     * @param bool|string $share If false, dont check share datas from group. Else can take a Shop::SHARE_* constant value
     *
     * @return array
     */
    public static function getContextListShopID($share = false)
    {
        if (Shop::getContext() == Shop::CONTEXT_SHOP) {
            $list = $share ? Shop::getSharedShops(Shop::getContextShopID(), $share) : [Shop::getContextShopID()];
        } elseif (Shop::getContext() == Shop::CONTEXT_GROUP) {
            $list = Shop::getShops(true, Shop::getContextShopGroupID(), true);
        } else {
            $list = Shop::getShops(true, null, true);
        }

        return $list;
    }

    /**
     * Return the list of shop by id.
     *
     * @param int $id
     * @param string $identifier
     * @param string $table
     *
     * @return array
     */
    public static function getShopById($id, $identifier, $table)
    {
        return Db::getInstance()->executeS(
            'SELECT `id_shop`, `' . bqSQL($identifier) . '`
            FROM `' . _DB_PREFIX_ . bqSQL($table) . '_shop`
            WHERE `' . bqSQL($identifier) . '` = ' . (int) $id
        );
    }

    /**
     * Change the current shop context.
     *
     * @param int $type Shop::CONTEXT_ALL | Shop::CONTEXT_GROUP | Shop::CONTEXT_SHOP
     * @param int $id ID shop if CONTEXT_SHOP or id shop group if CONTEXT_GROUP
     */
    public static function setContext($type, $id = null)
    {
        switch ($type) {
            case self::CONTEXT_ALL:
                self::$context_id_shop = null;
                self::$context_id_shop_group = null;

                break;
            case self::CONTEXT_GROUP:
                self::$context_id_shop = null;
                self::$context_id_shop_group = (int) $id;

                break;
            case self::CONTEXT_SHOP:
                self::$context_id_shop = (int) $id;
                self::$context_id_shop_group = Shop::getGroupFromShop($id);

                break;
            default:
                throw new PrestaShopException('Unknown context for shop');
        }
        static::$context_shop_group = null;
        self::$context = $type;
    }

    /**
     * Get current context of shop.
     *
     * @return int
     */
    public static function getContext()
    {
        return self::$context;
    }

    public static function resetStaticCache()
    {
        parent::resetStaticCache();
        static::$shops = null;
        static::$feature_active = null;
        static::$context_shop_group = null;
        SymfonyCache::getInstance()->invalidateTags(['shop']);
        Cache::clean('Shop::*');
    }

    /**
     * Reset current context of shop.
     */
    public static function resetContext()
    {
        self::$context = null;
        self::$feature_active = null;
        self::$context_id_shop = null;
    }

    /**
     * @return int
     */
    public function getContextType()
    {
        return self::getContext();
    }

    /**
     * Get current ID of shop if context is CONTEXT_SHOP.
     *
     * @return int|null
     */
    public static function getContextShopID($null_value_without_multishop = false)
    {
        if ($null_value_without_multishop && !Shop::isFeatureActive()) {
            return null;
        }

        return self::$context_id_shop;
    }

    /**
     * @return int
     */
    public function getContextualShopId()
    {
        if ($this->getContextType() !== self::CONTEXT_SHOP) {
            throw new LogicException('The retrieval of the contextual shop id is only possible in "single shop mode".');
        }

        return (int) self::$context_id_shop;
    }

    /**
     * Get current ID of shop group if context is CONTEXT_SHOP or CONTEXT_GROUP.
     *
     * @return int|null
     */
    public static function getContextShopGroupID($null_value_without_multishop = false)
    {
        if ($null_value_without_multishop && !Shop::isFeatureActive()) {
            return null;
        }

        return self::$context_id_shop_group;
    }

    public static function getContextShopGroup()
    {
        if (static::$context_shop_group === null) {
            static::$context_shop_group = new ShopGroup((int) self::$context_id_shop_group);
        }

        return static::$context_shop_group;
    }

    /**
     * Add an sql restriction for shops fields.
     *
     * @param bool|int|string $share If false, dont check share datas from group. Else can take a Shop::SHARE_* constant value
     * @param string|null $alias
     */
    public static function addSqlRestriction($share = false, $alias = null)
    {
        if ($alias) {
            $alias .= '.';
        }

        if (is_string($alias)) {
            $alias = Db::getInstance()->escape($alias);
        }

        $group = Shop::getGroupFromShop(Shop::getContextShopID(), false);
        if ($share == Shop::SHARE_CUSTOMER && Shop::getContext() == Shop::CONTEXT_SHOP && $group['share_customer']) {
            $restriction = ' AND ' . $alias . 'id_shop_group = ' . (int) Shop::getContextShopGroupID() . ' ';
        } else {
            $restriction = ' AND ' . $alias . 'id_shop IN (' . implode(', ', Shop::getContextListShopID($share)) . ') ';
        }

        return $restriction;
    }

    /**
     * Add an SQL JOIN in query between a table and its associated table in multishop.
     *
     * @param string $table Table name (E.g. product, module, etc.)
     * @param string $alias Alias of table
     * @param bool $inner_join Use or not INNER JOIN
     * @param string $on
     *
     * @return string
     */
    public static function addSqlAssociation($table, $alias, $inner_join = true, $on = null, $force_not_default = false)
    {
        $table_alias = $table . '_shop';
        if (strpos($table, '.') !== false) {
            list($table_alias, $table) = explode('.', $table);
        }

        $asso_table = Shop::getAssoTable($table);
        if ($asso_table === false || $asso_table['type'] != 'shop') {
            return '';
        }
        $sql = (($inner_join) ? ' INNER' : ' LEFT') . ' JOIN ' . _DB_PREFIX_ . $table . '_shop ' . $table_alias . '
        ON (' . $table_alias . '.id_' . $table . ' = ' . $alias . '.id_' . $table;
        if ((int) self::$context_id_shop) {
            $sql .= ' AND ' . $table_alias . '.id_shop = ' . (int) self::$context_id_shop;
        } elseif (Shop::checkIdShopDefault($table) && !$force_not_default) {
            $sql .= ' AND ' . $table_alias . '.id_shop = ' . $alias . '.id_shop_default';
        } else {
            $sql .= ' AND ' . $table_alias . '.id_shop IN (' . implode(', ', Shop::getContextListShopID()) . ')';
        }
        $sql .= (($on) ? ' AND ' . $on : '') . ')';

        return $sql;
    }

    /**
     * Add a restriction on id_shop for multishop lang table.
     *
     * @param string|null $alias
     * @param string|int|null $id_shop
     *
     * @return string
     */
    public static function addSqlRestrictionOnLang($alias = null, $id_shop = null)
    {
        if (isset(Context::getContext()->shop) && null === $id_shop) {
            $id_shop = (int) Context::getContext()->shop->id;
        }
        if (!$id_shop) {
            $id_shop = (int) Configuration::get('PS_SHOP_DEFAULT');
        }

        return ' AND ' . ($alias ? Db::getInstance()->escape($alias) . '.' : '') . 'id_shop = ' . $id_shop . ' ';
    }

    /**
     * Get all groups and associated shops as subarrays.
     *
     * @return array
     */
    public static function getTree()
    {
        Shop::cacheShops();

        return self::$shops;
    }

    /**
     * @return bool Return true if multishop feature is active and at last 2 shops have been created
     */
    public static function isFeatureActive()
    {
        if (static::$feature_active === null) {
            static::$feature_active = SymfonyCache::getInstance()->get('isFeatureActive', function (ItemInterface $item) {
                $item->tag('shop');

                return Db::getInstance()->getValue('SELECT value FROM `' . _DB_PREFIX_ . 'configuration` WHERE `name` = "PS_MULTISHOP_FEATURE_ACTIVE"')
                && (Db::getInstance()->getValue('SELECT COUNT(*) FROM ' . _DB_PREFIX_ . 'shop') > 1);
            });
        }

        return static::$feature_active;
    }

    public function copyShopData($old_id, $tables_import = false, $deleted = false)
    {
        // If we duplicate some specific data, automatically duplicate other data linked to the first
        // E.g. if carriers are duplicated for the shop, duplicate carriers langs too

        if (!$old_id) {
            $old_id = Configuration::get('PS_SHOP_DEFAULT');
        }

        if (isset($tables_import['carrier'])) {
            $tables_import['carrier_tax_rules_group_shop'] = true;
            $tables_import['carrier_lang'] = true;
        }

        if (isset($tables_import['cms'])) {
            $tables_import['cms_lang'] = true;
            $tables_import['cms_category'] = true;
            $tables_import['cms_category_lang'] = true;
        }

        $tables_import['category_lang'] = true;
        if (isset($tables_import['product'])) {
            $tables_import['product_lang'] = true;
            $tables_import['customization_field_lang'] = true;
        }

        if (isset($tables_import['module'])) {
            $tables_import['module_currency'] = true;
            $tables_import['module_country'] = true;
            $tables_import['module_group'] = true;
        }

        if (isset($tables_import['hook_module'])) {
            $tables_import['hook_module_exceptions'] = true;
        }

        if (isset($tables_import['attribute_group'])) {
            $tables_import['attribute'] = true;
        }

        // Browse and duplicate data
        foreach (Shop::getAssoTables() as $table_name => $row) {
            if ($tables_import && !isset($tables_import[$table_name])) {
                continue;
            }

            // Special case for stock_available if current shop is in a share stock group
            if ($table_name == 'stock_available') {
                $group = new ShopGroup($this->id_shop_group);
                if ($group->share_stock && $group->haveShops()) {
                    continue;
                }
            }

            $id = 'id_' . $row['type'];
            if ($row['type'] == 'fk_shop') {
                $id = 'id_shop';
            } else {
                $table_name .= '_' . $row['type'];
            }

            if (!$deleted) {
                $res = Db::getInstance()->getRow('SELECT * FROM `' . _DB_PREFIX_ . $table_name . '` WHERE `' . $id . '` = ' . (int) $old_id);
                if ($res) {
                    unset($res[$id]);
                    if (isset($row['primary'])) {
                        unset($res[$row['primary']]);
                    }

                    $categories = Tools::getValue('categoryBox');
                    if ($table_name == 'product_shop' && count($categories) == 1) {
                        unset($res['id_category_default']);
                        $keys = implode('`, `', array_keys($res));
                        $sql = 'INSERT IGNORE INTO `' . _DB_PREFIX_ . $table_name . '` (`' . $keys . '`, `id_category_default`, ' . $id . ')
                                (SELECT `' . $keys . '`, ' . (int) $categories[0] . ', ' . (int) $this->id . ' FROM ' . _DB_PREFIX_ . $table_name . '
                                WHERE `' . $id . '` = ' . (int) $old_id . ')';
                    } else {
                        $keys = implode('`, `', array_keys($res));
                        $sql = 'INSERT IGNORE INTO `' . _DB_PREFIX_ . $table_name . '` (`' . $keys . '`, ' . $id . ')
                                (SELECT `' . $keys . '`, ' . (int) $this->id . ' FROM ' . _DB_PREFIX_ . $table_name . '
                                WHERE `' . $id . '` = ' . (int) $old_id . ')';
                    }
                    Db::getInstance()->execute($sql);
                }
            }
        }

        // Hook for duplication of shop data
        $modules_list = Hook::getHookModuleExecList('actionShopDataDuplication');
        if (is_array($modules_list) && count($modules_list) > 0) {
            foreach ($modules_list as $m) {
                if (!$tables_import || isset($tables_import['Module' . ucfirst($m['module'])])) {
                    Hook::exec('actionShopDataDuplication', [
                        'old_id_shop' => (int) $old_id,
                        'new_id_shop' => (int) $this->id,
                    ], $m['id_module']);
                }
            }
        }
    }

    /**
     * @param int $id
     *
     * @return array
     */
    public static function getCategories($id = 0, $only_id = true)
    {
        // build query
        $query = new DbQuery();
        if ($only_id) {
            $query->select('cs.`id_category`');
        } else {
            $query->select('DISTINCT cs.`id_category`, cl.`name`, cl.`link_rewrite`');
        }
        $query->from('category_shop', 'cs');
        $query->leftJoin('category_lang', 'cl', 'cl.`id_category` = cs.`id_category` AND cl.`id_lang` = ' . (int) Context::getContext()->language->id);
        $query->where('cs.`id_shop` = ' . (int) $id);
        $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($query);

        if ($only_id) {
            $array = [];
            foreach ($result as $row) {
                $array[] = $row['id_category'];
            }
            $array = array_unique($array);
        } else {
            return $result;
        }

        return $array;
    }

    /**
     * @param string $entity
     * @param int $id_shop
     *
     * @return array|bool
     */
    public static function getEntityIds($entity, $id_shop, $active = false, $delete = false)
    {
        if (!Shop::isTableAssociated($entity)) {
            return false;
        }

        return Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS(
            'SELECT entity.`id_' . bqSQL($entity) . '`
            FROM `' . _DB_PREFIX_ . bqSQL($entity) . '_shop`es
            LEFT JOIN ' . _DB_PREFIX_ . bqSQL($entity) . ' entity
                ON (entity.`id_' . bqSQL($entity) . '` = es.`id_' . bqSQL($entity) . '`)
            WHERE es.`id_shop` = ' . (int) $id_shop .
            ($active ? ' AND entity.`active` = 1' : '') .
            ($delete ? ' AND entity.deleted = 0' : '')
        );
    }

    /**
     * @param string $host
     *
     * @return array
     *
     * @throws PrestaShopDatabaseException
     */
    private static function findShopByHost($host)
    {
        $sql = 'SELECT s.id_shop, CONCAT(su.physical_uri, su.virtual_uri) AS uri, su.domain, su.main
                    FROM ' . _DB_PREFIX_ . 'shop_url su
                    LEFT JOIN ' . _DB_PREFIX_ . 'shop s ON (s.id_shop = su.id_shop)
                    WHERE (su.domain = \'' . pSQL($host) . '\' OR su.domain_ssl = \'' . pSQL($host) . '\')
                        AND s.active = 1
                        AND s.deleted = 0
                    ORDER BY LENGTH(CONCAT(su.physical_uri, su.virtual_uri)) DESC';

        $result = Db::getInstance()->executeS($sql);

        return $result;
    }
}
