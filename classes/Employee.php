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
use PrestaShop\PrestaShop\Adapter\CoreException;
use PrestaShop\PrestaShop\Adapter\ServiceLocator;
use PrestaShop\PrestaShop\Adapter\SymfonyContainer;
use PrestaShop\PrestaShop\Core\Crypto\Hashing;
use PrestaShopBundle\Security\Admin\SessionRenewer;

/**
 * Class EmployeeCore.
 */
class EmployeeCore extends ObjectModel
{
    /** @var int Employee ID */
    public $id;

    /** @var int Employee profile */
    public $id_profile;

    /** @var int Employee language */
    public $id_lang;

    /** @var string Lastname */
    public $lastname;

    /** @var string Firstname */
    public $firstname;

    /** @var string e-mail */
    public $email;

    /** @var string Password */
    public $passwd;

    /** @var string Password */
    public $last_passwd_gen;

    public $stats_date_from;
    public $stats_date_to;

    public $stats_compare_from;
    public $stats_compare_to;
    public $stats_compare_option = 1;

    public $preselect_date_range;

    /** @var string Display back office background in the specified color */
    public $bo_color;

    public $default_tab;

    /** @var string employee's chosen theme */
    public $bo_theme;

    /** @var string employee's chosen css file */
    public $bo_css = 'theme.css';

    /** @var int employee desired screen width */
    public $bo_width;

    /** @var bool */
    public $bo_menu = 1;

    /* Deprecated */
    public $bo_show_screencast = false;

    /** @var bool Status */
    public $active = 1;

    public $remote_addr;

    /* employee notifications */
    public $id_last_order;
    public $id_last_customer_message;
    public $id_last_customer;

    /** @var string Unique token for forgot password feature */
    public $reset_password_token;

    /** @var string token validity date for forgot password feature */
    public $reset_password_validity;

    /**
     * @var bool
     */
    public $has_enabled_gravatar = false;

    /**
     * @see ObjectModel::$definition
     */
    public static $definition = [
        'table' => 'employee',
        'primary' => 'id_employee',
        'fields' => [
            'lastname' => ['type' => self::TYPE_STRING, 'validate' => 'isName', 'required' => true, 'size' => 255],
            'firstname' => ['type' => self::TYPE_STRING, 'validate' => 'isName', 'required' => true, 'size' => 255],
            'email' => ['type' => self::TYPE_STRING, 'validate' => 'isEmail', 'required' => true, 'size' => 255],
            'id_lang' => ['type' => self::TYPE_INT, 'validate' => 'isUnsignedInt', 'required' => true],
            'passwd' => ['type' => self::TYPE_STRING, 'validate' => 'isPasswd', 'required' => true, 'size' => 255],
            'last_passwd_gen' => ['type' => self::TYPE_STRING],
            'active' => ['type' => self::TYPE_BOOL, 'validate' => 'isBool'],
            'id_profile' => ['type' => self::TYPE_INT, 'validate' => 'isInt', 'required' => true],
            'bo_color' => ['type' => self::TYPE_STRING, 'validate' => 'isColor', 'size' => 32],
            'default_tab' => ['type' => self::TYPE_INT, 'validate' => 'isInt'],
            'bo_theme' => ['type' => self::TYPE_STRING, 'validate' => 'isGenericName', 'size' => 32],
            'bo_css' => ['type' => self::TYPE_STRING, 'validate' => 'isGenericName', 'size' => 64],
            'bo_width' => ['type' => self::TYPE_INT, 'validate' => 'isUnsignedInt'],
            'bo_menu' => ['type' => self::TYPE_BOOL, 'validate' => 'isBool'],
            'stats_date_from' => ['type' => self::TYPE_DATE, 'validate' => 'isDate'],
            'stats_date_to' => ['type' => self::TYPE_DATE, 'validate' => 'isDate'],
            'stats_compare_from' => ['type' => self::TYPE_DATE, 'validate' => 'isDate'],
            'stats_compare_to' => ['type' => self::TYPE_DATE, 'validate' => 'isDate'],
            'stats_compare_option' => ['type' => self::TYPE_INT, 'validate' => 'isUnsignedInt'],
            'preselect_date_range' => ['type' => self::TYPE_STRING, 'size' => 32],
            'id_last_order' => ['type' => self::TYPE_INT, 'validate' => 'isUnsignedInt'],
            'id_last_customer_message' => ['type' => self::TYPE_INT, 'validate' => 'isUnsignedInt'],
            'id_last_customer' => ['type' => self::TYPE_INT, 'validate' => 'isUnsignedInt'],
            'reset_password_token' => ['type' => self::TYPE_STRING, 'validate' => 'isSha1', 'size' => 40, 'copy_post' => false],
            'reset_password_validity' => ['type' => self::TYPE_DATE, 'validate' => 'isDateOrNull', 'copy_post' => false],
            'has_enabled_gravatar' => ['type' => self::TYPE_BOOL, 'validate' => 'isBool'],
        ],
    ];

    protected $webserviceParameters = [
        'fields' => [
            'id_lang' => ['xlink_resource' => 'languages'],
            'last_passwd_gen' => ['setter' => null],
            'stats_date_from' => ['setter' => null],
            'stats_date_to' => ['setter' => null],
            'stats_compare_from' => ['setter' => null],
            'stats_compare_to' => ['setter' => null],
            'passwd' => ['setter' => 'setWsPasswd'],
        ],
    ];

    protected $associated_shops = [];

    /**
     * EmployeeCore constructor.
     *
     * @param int|null $id Employee ID
     * @param int|null $idLang Language ID
     * @param int|null $idShop Shop ID
     */
    public function __construct($id = null, $idLang = null, $idShop = null)
    {
        parent::__construct($id, null, $idShop);

        if (null !== $idLang) {
            $this->id_lang = (int) (Language::getLanguage($idLang) !== false) ? $idLang : Configuration::get('PS_LANG_DEFAULT');
        }

        if ($this->id) {
            $this->associated_shops = $this->getAssociatedShops();
        }

        $this->image_dir = _PS_EMPLOYEE_IMG_DIR_;
    }

    /**
     * @see ObjectModel::getFields()
     *
     * @return array
     */
    public function getFields()
    {
        if (empty($this->stats_date_from) || $this->stats_date_from == '0000-00-00') {
            $this->stats_date_from = date('Y-m-d', strtotime('-1 month'));
        }

        if (empty($this->stats_compare_from) || $this->stats_compare_from == '0000-00-00') {
            $this->stats_compare_from = null;
        }

        if (empty($this->stats_date_to) || $this->stats_date_to == '0000-00-00') {
            $this->stats_date_to = date('Y-m-d');
        }

        if (empty($this->stats_compare_to) || $this->stats_compare_to == '0000-00-00') {
            $this->stats_compare_to = null;
        }

        return parent::getFields();
    }

    /**
     * Adds current Employee as a new Object to the database.
     *
     * @param bool $autoDate Automatically set `date_upd` and `date_add` columns
     * @param bool $nullValues Whether we want to use NULL values instead of empty quotes values
     *
     * @return bool Indicates whether the Employee has been successfully added
     *
     * @throws PrestaShopDatabaseException
     * @throws PrestaShopException
     */
    public function add($autoDate = true, $nullValues = true)
    {
        $this->last_passwd_gen = date('Y-m-d H:i:s', strtotime('-' . Configuration::get('PS_PASSWD_TIME_BACK') . 'minutes'));
        $this->updateTextDirection();

        return parent::add($autoDate, $nullValues);
    }

    /**
     * Updates the current object in the database.
     *
     * @param bool $nullValues Whether we want to use NULL values instead of empty quotes values
     *
     * @return bool Indicates whether the Employee has been successfully updated
     *
     * @throws PrestaShopDatabaseException
     * @throws PrestaShopException
     */
    public function update($nullValues = false)
    {
        if (empty($this->stats_date_from) || $this->stats_date_from == '0000-00-00') {
            $this->stats_date_from = date('Y-m-d');
        }

        if (empty($this->stats_date_to) || $this->stats_date_to == '0000-00-00') {
            $this->stats_date_to = date('Y-m-d');
        }

        $currentEmployee = new Employee((int) $this->id);

        $this->updateTextDirection();

        return parent::update($nullValues);
    }

    /**
     * Update Employee text direction.
     */
    protected function updateTextDirection()
    {
        if (!defined('_PS_ADMIN_DIR_')) {
            return;
        }

        $path = _PS_ADMIN_DIR_ . DIRECTORY_SEPARATOR . 'themes' . DIRECTORY_SEPARATOR . $this->bo_theme . DIRECTORY_SEPARATOR . 'css' . DIRECTORY_SEPARATOR;
        $language = new Language($this->id_lang);

        if ($language->is_rtl && !strpos($this->bo_css, '_rtl')) {
            $boCss = preg_replace('/^(.*)\.css$/', '$1_rtl.css', $this->bo_css);

            if (file_exists($path . $boCss)) {
                $this->bo_css = $boCss;
            }
        } elseif (!$language->is_rtl && strpos($this->bo_css, '_rtl')) {
            $boCss = preg_replace('/^(.*)_rtl\.css$/', '$1.css', $this->bo_css);

            if (file_exists($path . $boCss)) {
                $this->bo_css = $boCss;
            }
        }
    }

    /**
     * Return list of employees.
     *
     * @param bool $activeOnly Filter employee by active status
     *
     * @return array|false Employees or false
     */
    public static function getEmployees($activeOnly = true)
    {
        return Db::getInstance()->executeS('
			SELECT `id_employee`, `firstname`, `lastname`
			FROM `' . _DB_PREFIX_ . 'employee`
			' . ($activeOnly ? ' WHERE `active` = 1' : '') . '
			ORDER BY `lastname` ASC
		');
    }

    /**
     * Return employee instance from its e-mail (optionally check password).
     *
     * @param string $email e-mail
     * @param string $plaintextPassword Password is also checked if specified
     * @param bool $activeOnly Filter employee by active status
     *
     * @return bool|Employee|EmployeeCore Employee instance
     *                                    `false` if not found
     */
    public function getByEmail($email, $plaintextPassword = null, $activeOnly = true)
    {
        if (!Validate::isEmail($email) || ($plaintextPassword != null && !Validate::isPlaintextPassword($plaintextPassword))) {
            die(Tools::displayError());
        }

        $sql = new DbQuery();
        $sql->select('e.*');
        $sql->from('employee', 'e');
        $sql->where('e.`email` = \'' . pSQL($email) . '\'');
        if ($activeOnly) {
            $sql->where('e.`active` = 1');
        }

        $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow($sql);
        if (!$result) {
            return false;
        }

        /** @var Hashing $crypto */
        $crypto = ServiceLocator::get(Hashing::class);

        $passwordHash = $result['passwd'];
        $shouldCheckPassword = null !== $plaintextPassword;
        if ($shouldCheckPassword && !$crypto->checkHash($plaintextPassword, $passwordHash)) {
            return false;
        }

        $this->id = $result['id_employee'];
        $this->id_profile = $result['id_profile'];
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
     * Check if Employee exists.
     *
     * @param string $email Employee email
     *
     * @return bool Indicates whether the Employee exists
     */
    public static function employeeExists($email)
    {
        if (!Validate::isEmail($email)) {
            die(Tools::displayError());
        }

        return (bool) Db::getInstance()->getValue('
		    SELECT `id_employee`
		    FROM `' . _DB_PREFIX_ . 'employee`
		    WHERE `email` = \'' . pSQL($email) . '\'
        ', false);
    }

    /**
     * Check if employee password is the right one.
     *
     * @param string $passwordHash Password
     *
     * @return bool result
     */
    public static function checkPassword($idEmployee, $passwordHash)
    {
        if (!Validate::isUnsignedId($idEmployee)) {
            die(Tools::displayError());
        }

        $sql = new DbQuery();
        $sql->select('e.`id_employee`');
        $sql->from('employee', 'e');
        $sql->where('e.`id_employee` = ' . (int) $idEmployee);
        $sql->where('e.`passwd` = \'' . pSQL($passwordHash) . '\'');
        $sql->where('e.`active` = 1');

        // Get result from DB
        return Db::getInstance()->getValue($sql);
    }

    /**
     * Count amount of Employees with the given Profile ID.
     *
     * @param int $idProfile Profile ID
     * @param bool $activeOnly Only active Employees
     *
     * @return false|string|null
     */
    public static function countProfile($idProfile, $activeOnly = false)
    {
        return Db::getInstance()->getValue(
            '
		    SELECT COUNT(*)
		    FROM `' . _DB_PREFIX_ . 'employee`
		    WHERE `id_profile` = ' . (int) $idProfile . '
		    ' . ($activeOnly ? ' AND `active` = 1' : '')
        );
    }

    /**
     * Check if this Employee is the only SuperAdmin left.
     *
     * @return bool Indicates whether this Employee is the last one
     */
    public function isLastAdmin()
    {
        return $this->isSuperAdmin()
            && Employee::countProfile($this->id_profile, true) == 1
            && $this->active;
    }

    /**
     * Set password
     * (for webservice).
     *
     * @param string $passwd Password
     *
     * @return bool Indicates whether the password was succesfully set
     */
    public function setWsPasswd($passwd)
    {
        try {
            /** @var \PrestaShop\PrestaShop\Core\Crypto\Hashing $crypto */
            $crypto = ServiceLocator::get('\\PrestaShop\\PrestaShop\\Core\\Crypto\\Hashing');
        } catch (CoreException $e) {
            return false;
        }

        if ($this->id != 0) {
            if ($this->passwd != $passwd) {
                $this->passwd = $crypto->hash($passwd);
            }
        } else {
            $this->passwd = $crypto->hash($passwd);
        }

        return true;
    }

    /**
     * Check employee informations saved into cookie and return employee validity.
     *
     * @return bool employee validity
     */
    public function isLoggedBack()
    {
        if (!Cache::isStored('isLoggedBack' . $this->id)) {
            /* Employee is valid only if it can be load and if cookie password is the same as database one */
            $result = (
                $this->id
                && Validate::isUnsignedId($this->id)
                && Context::getContext()->cookie
                && Context::getContext()->cookie->isSessionAlive()
                && Employee::checkPassword($this->id, Context::getContext()->cookie->passwd)
                && (
                    !isset(Context::getContext()->cookie->remote_addr)
                    || Context::getContext()->cookie->remote_addr == ip2long(Tools::getRemoteAddr())
                    || !Configuration::get('PS_COOKIE_CHECKIP')
                )
            );
            Cache::store('isLoggedBack' . $this->id, $result);

            return $result;
        }

        return Cache::retrieve('isLoggedBack' . $this->id);
    }

    /**
     * Logout.
     */
    public function logout()
    {
        if (isset(Context::getContext()->cookie)) {
            Context::getContext()->cookie->logout();
            Context::getContext()->cookie->write();
        }

        $sfContainer = SymfonyContainer::getInstance();
        if ($sfContainer !== null) {
            $sfContainer->get(SessionRenewer::class)->renew();
        }

        $this->id = null;
    }

    /**
     * Get favorite Module list.
     *
     * @return array|false|mysqli_result|PDOStatement|resource|null
     */
    public function favoriteModulesList()
    {
        return Db::getInstance()->executeS(
            '
		    SELECT `module`
		    FROM `' . _DB_PREFIX_ . 'module_preference`
		    WHERE `id_employee` = ' . (int) $this->id . ' AND `favorite` = 1 AND (`interest` = 1 OR `interest` IS NULL)'
        );
    }

    /**
     * Check if the employee is associated to a specific shop.
     *
     * @param int $idShop
     *
     * @return bool
     *
     * @since 1.5.0
     */
    public function hasAuthOnShop($idShop)
    {
        return $this->isSuperAdmin() || in_array($idShop, $this->associated_shops);
    }

    /**
     * Check if the employee is associated to a specific shop group.
     *
     * @param int $id_shop_group ShopGroup ID
     *
     * @return bool
     *
     * @since 1.5.0
     */
    public function hasAuthOnShopGroup($idShopGroup)
    {
        if ($this->isSuperAdmin()) {
            return true;
        }

        foreach ($this->associated_shops as $idShop) {
            if ($idShopGroup == Shop::getGroupFromShop($idShop, true)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Get default id_shop with auth for current employee.
     *
     * @return int
     *
     * @since 1.5.0
     */
    public function getDefaultShopID()
    {
        if ($this->isSuperAdmin() || in_array(Configuration::get('PS_SHOP_DEFAULT'), $this->associated_shops)) {
            return Configuration::get('PS_SHOP_DEFAULT');
        }

        return $this->associated_shops[0];
    }

    /**
     * Get Employees by Profile.
     *
     * @param int $idProfile Profile ID
     * @param bool $activeOnly Only active Employees
     *
     * @return array|false|mysqli_result|PDOStatement|resource|null
     */
    public static function getEmployeesByProfile($idProfile, $activeOnly = false)
    {
        return Db::getInstance()->executeS(
            '
		    SELECT *
		    FROM `' . _DB_PREFIX_ . 'employee`
		    WHERE `id_profile` = ' . (int) $idProfile . '
		    ' . ($activeOnly ? ' AND `active` = 1' : '')
        );
    }

    /**
     * Check if current employee is super administrator.
     *
     * @return bool
     */
    public function isSuperAdmin()
    {
        return $this->id_profile == _PS_ADMIN_PROFILE_;
    }

    /**
     * Get Employee image.
     *
     * @return string Image URL
     */
    public function getImage()
    {
        $defaultSystem = Tools::getAdminImageUrl('pr/default.jpg');
        $imageUrl = null;

        // Default from Profile
        $profile = new Profile($this->id_profile);
        $defaultProfile = (int) $profile->id === (int) $this->id_profile ? $profile->getProfileImage() : null;
        $imageUrl = $imageUrl ?? $defaultProfile;

        // Gravatar
        if ($this->has_enabled_gravatar) {
            $imageUrl = $imageUrl ?? 'https://www.gravatar.com/avatar/' . md5(strtolower(trim($this->email))) . '?d=' . urlencode($defaultSystem);
        }

        // Local Image
        $imagePath = $this->image_dir . $this->id . '.jpg';
        if (file_exists($imagePath)) {
            $imageUrl = $imageUrl ?? Context::getContext()->link->getMediaLink(
                str_replace($this->image_dir, _THEME_EMPLOYEE_DIR_, $imagePath)
            );
        }

        // Default from System
        $imageUrl = $imageUrl ?? $defaultSystem;

        // Hooks
        Hook::exec(
            'actionOverrideEmployeeImage',
            [
                'employee' => $this,
                'imageUrl' => &$imageUrl,
            ]
        );

        return $imageUrl;
    }

    /**
     * Get last elements for notify.
     *
     * @param $element
     *
     * @return int
     */
    public function getLastElementsForNotify($element)
    {
        $element = bqSQL($element);
        $max = Db::getInstance()->getValue('
			SELECT MAX(`id_' . $element . '`) as `id_' . $element . '`
			FROM `' . _DB_PREFIX_ . $element . ($element == 'order' ? 's' : '') . '`');

        // if no rows in table, set max to 0
        if ((int) $max < 1) {
            $max = 0;
        }

        return (int) $max;
    }

    /**
     * Set last connection date.
     *
     * @param int $idEmployee Employee ID
     *
     * @return bool
     */
    public static function setLastConnectionDate($idEmployee)
    {
        return Db::getInstance()->execute('
            UPDATE `' . _DB_PREFIX_ . 'employee`
            SET `last_connection_date` = CURRENT_DATE()
            WHERE `id_employee` = ' . (int) $idEmployee . '
            AND (`last_connection_date` < CURRENT_DATE()
            OR `last_connection_date` IS NULL)
        ');
    }

    /**
     * Fill Reset password unique token with random sha1 and its validity date. For forgot password feature.
     */
    public function stampResetPasswordToken()
    {
        $salt = $this->id . '+' . uniqid(mt_rand(0, mt_getrandmax()), true);
        $this->reset_password_token = sha1(time() . $salt);
        $validity = (int) Configuration::get('PS_PASSWD_RESET_VALIDITY') ?: 1440;
        $this->reset_password_validity = date('Y-m-d H:i:s', strtotime('+' . $validity . ' minutes'));
    }

    /**
     * Test if a reset password token is present and is recent enough to avoid creating a new one (in case of employee triggering the forgot password link too often).
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

    /**
     * Is the Employee allowed to do the given action.
     *
     * @param $action
     * @param $tab
     *
     * @return bool
     */
    public function can($action, $tab)
    {
        $access = Profile::getProfileAccess($this->id_profile, Tab::getIdFromClassName($tab));

        return is_array($access) && $access[$action] == '1';
    }

    /**
     * Returns the default tab class name.
     *
     * @return string|null
     */
    public function getDefaultTabClassName()
    {
        if ($tabId = (int) $this->default_tab) {
            return Tab::getClassNameById($tabId) ?: null;
        }

        return null;
    }
}
