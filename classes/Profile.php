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
 * needs please refer to https://devdocs.prestashop-project.org/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

/**
 * Class ProfileCore.
 */
class ProfileCore extends ObjectModel
{
    public const ALLOWED_PROFILE_TYPE_CHECK = [
        'id_tab',
        'class_name',
    ];

    /** @var string|array<int, string> Name */
    public $name;

    /**
     * @see ObjectModel::$definition
     */
    public static $definition = [
        'table' => 'profile',
        'primary' => 'id_profile',
        'multilang' => true,
        'fields' => [
            /* Lang fields */
            'name' => ['type' => self::TYPE_STRING, 'lang' => true, 'validate' => 'isGenericName', 'required' => true, 'size' => 32],
        ],
    ];

    protected static $_cache_accesses = [];

    /**
     * {@inheritdoc}
     */
    public function __construct($id = null, $idLang = null, $idShop = null, $translator = null)
    {
        parent::__construct($id, $idLang, $idShop, $translator);

        $this->image_dir = _PS_PROFILE_IMG_DIR_;
    }

    /**
     * @return string|null
     */
    public function getProfileImage(): ?string
    {
        $path = $this->image_dir . $this->id . '.jpg';

        return file_exists($path)
            ? Context::getContext()->link->getMediaLink(
                str_replace($this->image_dir, _THEME_PROFILE_DIR_, $path)
            )
            : null;
    }

    /**
     * Get all available profiles.
     *
     * @return array Profiles
     */
    public static function getProfiles($idLang)
    {
        return Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('
		SELECT p.`id_profile`, `name`
		FROM `' . _DB_PREFIX_ . 'profile` p
		LEFT JOIN `' . _DB_PREFIX_ . 'profile_lang` pl ON (p.`id_profile` = pl.`id_profile` AND `id_lang` = ' . (int) $idLang . ')
		ORDER BY `id_profile` ASC');
    }

    /**
     * Get the current profile name.
     *
     * @param int $idProfile Profile ID
     * @param int|null $idLang Language ID
     *
     * @return array Profile
     */
    public static function getProfile($idProfile, $idLang = null)
    {
        if (!$idLang) {
            $idLang = Configuration::get('PS_LANG_DEFAULT');
        }

        return Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow(
            'SELECT `name`
			FROM `' . _DB_PREFIX_ . 'profile` p
			LEFT JOIN `' . _DB_PREFIX_ . 'profile_lang` pl ON (p.`id_profile` = pl.`id_profile`)
			WHERE p.`id_profile` = ' . (int) $idProfile . '
			AND pl.`id_lang` = ' . (int) $idLang
        );
    }

    public function add($autodate = true, $null_values = false)
    {
        return parent::add($autodate, true);
    }

    public function delete()
    {
        if (parent::delete()) {
            return
                Db::getInstance()->execute('DELETE FROM `' . _DB_PREFIX_ . 'access` WHERE `id_profile` = ' . (int) $this->id)
                && Db::getInstance()->execute('DELETE FROM `' . _DB_PREFIX_ . 'module_access` WHERE `id_profile` = ' . (int) $this->id);
        }

        return false;
    }

    /**
     * Get access profile.
     *
     * @param int $idProfile Profile ID
     * @param int $idTab Tab ID
     *
     * @return array|bool
     */
    public static function getProfileAccess($idProfile, $idTab)
    {
        // getProfileAccesses is cached so there is no performance leak
        $accesses = Profile::getProfileAccesses($idProfile);

        return isset($accesses[$idTab]) ? $accesses[$idTab] : false;
    }

    /**
     * Get access profiles.
     *
     * @param int $idProfile Profile ID
     * @param string $type Type
     *
     * @return array|false
     */
    public static function getProfileAccesses($idProfile, $type = 'id_tab')
    {
        if (!in_array($type, self::ALLOWED_PROFILE_TYPE_CHECK)) {
            return false;
        }

        if (!isset(self::$_cache_accesses[$idProfile])) {
            self::$_cache_accesses[$idProfile] = [];
        }

        if (!isset(self::$_cache_accesses[$idProfile][$type])) {
            self::$_cache_accesses[$idProfile][$type] = [];
            // Super admin profile has full auth
            if ($idProfile == _PS_ADMIN_PROFILE_) {
                $defaultPermission = [
                    'id_profile' => _PS_ADMIN_PROFILE_,
                    'view' => '1',
                    'add' => '1',
                    'edit' => '1',
                    'delete' => '1',
                ];
                $roles = [];
            } else {
                $defaultPermission = [
                    'id_profile' => $idProfile,
                    'view' => '0',
                    'add' => '0',
                    'edit' => '0',
                    'delete' => '0',
                ];
                $roles = self::generateAccessesArrayFromPermissions(
                    Db::getInstance()->executeS('
                        SELECT `slug`,
                            `slug` LIKE "%CREATE" as "add",
                            `slug` LIKE "%READ" as "view",
                            `slug` LIKE "%UPDATE" as "edit",
                            `slug` LIKE "%DELETE" as "delete"
                        FROM `' . _DB_PREFIX_ . 'authorization_role` a
                        LEFT JOIN `' . _DB_PREFIX_ . 'access` j ON j.id_authorization_role = a.id_authorization_role
                        WHERE j.`id_profile` = ' . (int) $idProfile)
                );
            }
            self::fillCacheAccesses(
                $idProfile,
                $defaultPermission,
                $roles
            );
        }

        return self::$_cache_accesses[$idProfile][$type];
    }

    public static function resetStaticCache()
    {
        parent::resetStaticCache();
        self::resetCacheAccesses();
    }

    public static function resetCacheAccesses()
    {
        self::$_cache_accesses = [];
    }

    /**
     * @param int $idProfile Profile ID
     * @param array $defaultData Cached data
     * @param array $accesses Data loaded from the database
     */
    private static function fillCacheAccesses($idProfile, $defaultData = [], $accesses = [])
    {
        foreach (Tab::getTabs(Context::getContext()->language->id) as $tab) {
            $accessData = [];
            if (isset($accesses[strtoupper($tab['class_name'])])) {
                $accessData = $accesses[strtoupper($tab['class_name'])];
            }

            foreach (self::ALLOWED_PROFILE_TYPE_CHECK as $type) {
                self::$_cache_accesses[$idProfile][$type][$tab[$type]] = array_merge(
                    [
                        'id_tab' => $tab['id_tab'],
                        'class_name' => $tab['class_name'],
                    ],
                    $defaultData,
                    $accessData
                );
            }
        }
    }

    /**
     * Creates the array of accesses [role => add / view / edit / delete] from a given list of roles
     *
     * @param array $rolesGiven
     *
     * @return array
     */
    private static function generateAccessesArrayFromPermissions($rolesGiven)
    {
        // Modify array to merge the class names together.
        $accessPerTab = [];
        foreach ($rolesGiven as $role) {
            preg_match(
                '/ROLE_MOD_[A-Z]+_(?P<classname>[A-Z][A-Z0-9]*)_[A-Z]+/',
                $role['slug'],
                $matches
            );
            if (empty($matches['classname'])) {
                continue;
            }
            $accessPerTab[$matches['classname']][array_search('1', $role)] = '1';
        }

        return $accessPerTab;
    }
}
