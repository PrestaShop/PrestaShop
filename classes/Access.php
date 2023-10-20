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
 * Class AccessCore.
 */
class AccessCore extends ObjectModel
{
    /** @var int Profile id which address belongs to */
    public $id_profile = null;

    /** @var int AuthorizationRole id which address belongs to */
    public $id_authorization_role = null;

    /**
     * @see ObjectModel::$definition
     */
    public static $definition = [
        'table' => 'access',
        'primary' => 'id_profile',
        'fields' => [
            'id_profile' => ['type' => self::TYPE_INT, 'validate' => 'isNullOrUnsignedId', 'copy_post' => false],
            'id_authorization_role' => ['type' => self::TYPE_INT, 'validate' => 'isNullOrUnsignedId', 'copy_post' => false],
        ],
    ];

    /**
     * Is access granted to this Role?
     *
     * @param string|array<string> $role Role name ("Superadministrator", "sales", "translator", etc.)
     * @param int $idProfile Profile ID
     *
     * @return bool Whether access is granted
     *
     * @throws Exception
     */
    public static function isGranted($role, $idProfile)
    {
        foreach ((array) $role as $currentRole) {
            preg_match(
                '/ROLE_MOD_(?P<type>[A-Z]+)_(?P<name>[A-Z0-9_]+)_(?P<auth>[A-Z]+)/',
                $currentRole,
                $matches
            );

            if (isset($matches['type']) && $matches['type'] == 'TAB') {
                $joinTable = _DB_PREFIX_ . 'access';
            } elseif (isset($matches['type']) && $matches['type'] == 'MODULE') {
                $joinTable = _DB_PREFIX_ . 'module_access';
            } else {
                throw new Exception('The slug ' . $currentRole . ' is invalid');
            }

            $currentRole = Db::getInstance()->escape($currentRole);

            $isCurrentGranted = (bool) Db::getInstance()->getRow('
                SELECT t.`id_authorization_role`
                FROM `' . _DB_PREFIX_ . 'authorization_role` t
                LEFT JOIN ' . $joinTable . ' j
                ON j.`id_authorization_role` = t.`id_authorization_role`
                WHERE `slug` = "' . $currentRole . '"
                AND j.`id_profile` = "' . (int) $idProfile . '"
            ');

            if (!$isCurrentGranted) {
                return false;
            }
        }

        return true;
    }

    /**
     * Get all roles for the Profile ID.
     *
     * @param int $idProfile Profile ID
     *
     * @return array Roles
     */
    public static function getRoles($idProfile)
    {
        $idProfile = (int) $idProfile;

        $accesses = Db::getInstance()->executeS('
            SELECT r.`slug`
            FROM `' . _DB_PREFIX_ . 'authorization_role` r
            INNER JOIN `' . _DB_PREFIX_ . 'access` a ON a.`id_authorization_role` = r.`id_authorization_role`
            WHERE a.`id_profile` = "' . $idProfile . '"
        ');

        $accessesFromModules = Db::getInstance()->executeS('
            SELECT r.`slug`
            FROM `' . _DB_PREFIX_ . 'authorization_role` r
            INNER JOIN `' . _DB_PREFIX_ . 'module_access` ma ON ma.`id_authorization_role` = r.`id_authorization_role`
            WHERE ma.`id_profile` = "' . $idProfile . '"
        ');

        $roles = array_merge($accesses, $accessesFromModules);

        foreach ($roles as $key => $role) {
            $roles[$key] = $role['slug'];
        }

        return $roles;
    }

    /**
     * Find Tab ID by slug.
     *
     * @param string $authSlug Slug
     *
     * @return string Tab ID
     * @todo: Find out if we should return an int instead. (breaking change)
     */
    public static function findIdTabByAuthSlug($authSlug)
    {
        preg_match(
            '/ROLE_MOD_[A-Z]+_(?P<classname>[A-Z]+)_(?P<auth>[A-Z]+)/',
            $authSlug,
            $matches
        );

        $result = Db::getInstance()->getRow('
            SELECT `id_tab`
            FROM `' . _DB_PREFIX_ . 'tab`
            WHERE UCASE(`class_name`) = "' . $matches['classname'] . '"
        ');

        return $result['id_tab'];
    }

    /**
     * Find slug by Tab ID.
     *
     * @param int $idTab Tab ID
     *
     * @return string Full module slug
     */
    public static function findSlugByIdTab($idTab)
    {
        $result = Db::getInstance()->getRow('
            SELECT `class_name`
            FROM `' . _DB_PREFIX_ . 'tab`
            WHERE `id_tab` = "' . (int) $idTab . '"
        ');

        return self::sluggifyTab($result);
    }

    /**
     * Find slug by Parent Tab ID.
     *
     * @param int $idParentTab Tab ID
     *
     * @return array<int, array<string, string>> Full module slug
     */
    public static function findSlugByIdParentTab($idParentTab)
    {
        return Db::getInstance()->executeS('
            SELECT `class_name`
            FROM `' . _DB_PREFIX_ . 'tab`
            WHERE `id_parent` = "' . (int) $idParentTab . '"
        ');
    }

    /**
     * Find slug by Module ID.
     *
     * @param int $idModule Module ID
     *
     * @return string Full module slug
     */
    public static function findSlugByIdModule($idModule)
    {
        $result = Db::getInstance()->getRow('
            SELECT `name`
            FROM `' . _DB_PREFIX_ . 'module`
            WHERE `id_module` = "' . (int) $idModule . '"
        ');

        return self::sluggifyModule($result);
    }

    /**
     * Sluggify tab.
     *
     * @param array $tab Tab class name
     * @param string $authorization 'CREATE'|'READ'|'UPDATE'|'DELETE'
     *
     * @return string Full slug for tab
     */
    public static function sluggifyTab($tab, $authorization = '')
    {
        return sprintf('ROLE_MOD_TAB_%s_%s', strtoupper($tab['class_name'] ?? ''), $authorization);
    }

    /**
     * Sluggify module.
     *
     * @param array $module Module name
     * @param string $authorization 'CREATE'|'READ'|'UPDATE'|'DELETE'
     *
     * @return string Full slug for module
     */
    public static function sluggifyModule($module, $authorization = '')
    {
        return sprintf('ROLE_MOD_MODULE_%s_%s', strtoupper($module['name'] ?? ''), $authorization);
    }

    /**
     * Get legacy authorization.
     *
     * @param string $legacyAuth Legacy authorization
     *
     * @return bool|string|array Authorization
     */
    public static function getAuthorizationFromLegacy($legacyAuth)
    {
        $auth = [
            'add' => 'CREATE',
            'view' => 'READ',
            'edit' => 'UPDATE',
            'configure' => 'UPDATE',
            'delete' => 'DELETE',
            'uninstall' => 'DELETE',
            'duplicate' => ['CREATE', 'UPDATE'],
            'all' => ['CREATE', 'READ', 'UPDATE', 'DELETE'],
        ];

        return isset($auth[$legacyAuth]) ? $auth[$legacyAuth] : false;
    }

    /**
     * Add access.
     *
     * @param int $idProfile Profile ID
     * @param int $idRole Role ID
     *
     * @return string Whether access has been successfully granted ("ok", "error")
     */
    public function addAccess($idProfile, $idRole)
    {
        $sql = '
            INSERT IGNORE INTO `' . _DB_PREFIX_ . 'access` (`id_profile`, `id_authorization_role`)
            VALUES (' . (int) $idProfile . ',' . (int) $idRole . ')
        ';

        return Db::getInstance()->execute($sql) ? 'ok' : 'error';
    }

    /**
     * Remove access.
     *
     * @param int $idProfile Profile ID
     * @param int $idRole Role ID
     *
     * @return string Whether access has been successfully removed ("ok", "error")
     */
    public function removeAccess($idProfile, $idRole)
    {
        $sql = '
            DELETE FROM `' . _DB_PREFIX_ . 'access`
            WHERE `id_profile` = "' . (int) $idProfile . '"
            AND `id_authorization_role` = "' . (int) $idRole . '"
        ';

        return Db::getInstance()->execute($sql) ? 'ok' : 'error';
    }

    /**
     * Add module access.
     *
     * @param int $idProfile Profile ID
     * @param int $idRole Role ID
     *
     * @return string Whether module access has been successfully granted ("ok", "error")
     */
    public function addModuleAccess($idProfile, $idRole)
    {
        $sql = '
            INSERT IGNORE INTO `' . _DB_PREFIX_ . 'module_access` (`id_profile`, `id_authorization_role`)
            VALUES (' . (int) $idProfile . ',' . (int) $idRole . ')
        ';

        return Db::getInstance()->execute($sql) ? 'ok' : 'error';
    }

    /**
     * @param int $idProfile
     * @param int $idRole
     *
     * @return string 'ok'|'error'
     */
    public function removeModuleAccess($idProfile, $idRole)
    {
        $sql = '
            DELETE FROM `' . _DB_PREFIX_ . 'module_access`
            WHERE `id_profile` = "' . (int) $idProfile . '"
            AND `id_authorization_role` = "' . (int) $idRole . '"
        ';

        return Db::getInstance()->execute($sql) ? 'ok' : 'error';
    }

    /**
     * Update legacy access.
     *
     * @param int $idProfile Profile ID
     * @param int $idTab Tab ID
     * @param string $lgcAuth Legacy authorization
     * @param bool $enabled Whether access should be granted
     * @param bool $addFromParent Child from parents
     *
     * @return string Whether legacy access has been successfully updated ("ok", "error")
     *
     * @throws Exception
     */
    public function updateLgcAccess($idProfile, $idTab, $lgcAuth, $enabled, $addFromParent = true)
    {
        $idProfile = (int) $idProfile;
        $idTab = (int) $idTab;

        if ($idTab == -1) {
            $slug = 'ROLE_MOD_TAB_%_';
        } else {
            $slug = self::findSlugByIdTab($idTab);
        }

        $whereClauses = [];

        foreach ((array) self::getAuthorizationFromLegacy($lgcAuth) as $auth) {
            $slugLike = Db::getInstance()->escape($slug . $auth);
            $whereClauses[] = ' `slug` LIKE "' . $slugLike . '"';
        }

        if ($addFromParent) {
            foreach (self::findSlugByIdParentTab($idTab) as $child) {
                $child = self::sluggifyTab($child);
                foreach ((array) self::getAuthorizationFromLegacy($lgcAuth) as $auth) {
                    $slugLike = Db::getInstance()->escape($child . $auth);
                    $whereClauses[] = ' `slug` LIKE "' . $slugLike . '"';
                }
            }
        }

        $roles = Db::getInstance()->executeS('
            SELECT `id_authorization_role`
            FROM `' . _DB_PREFIX_ . 'authorization_role` t
            WHERE ' . implode(' OR ', $whereClauses) . '
        ');

        if (empty($roles)) {
            throw new \Exception('Cannot find role slug');
        }

        $res = [];
        foreach ($roles as $role) {
            if ($enabled) {
                $res[] = $this->addAccess($idProfile, $role['id_authorization_role']);
            } else {
                $res[] = $this->removeAccess($idProfile, $role['id_authorization_role']);
            }
        }

        return in_array('error', $res) ? 'error' : 'ok';
    }

    /**
     * Update (legacy) Module access.
     *
     * @param int $idProfile Profile ID
     * @param int $idModule Module ID
     * @param string $lgcAuth Legacy authorization
     * @param bool $enabled Whether module access should be granted
     *
     * @return string Whether module access has been succesfully changed ("ok", "error")
     */
    public function updateLgcModuleAccess($idProfile, $idModule, $lgcAuth, $enabled)
    {
        $idProfile = (int) $idProfile;
        $idModule = (int) $idModule;

        if ($idModule == -1) {
            $slug = 'ROLE_MOD_MODULE_%_';
        } else {
            $slug = self::findSlugByIdModule($idModule);
        }

        $whereClauses = [];

        foreach ((array) self::getAuthorizationFromLegacy($lgcAuth) as $auth) {
            $slugLike = Db::getInstance()->escape($slug . $auth);
            $whereClauses[] = ' `slug` LIKE "' . $slugLike . '"';
        }

        $roles = Db::getInstance()->executeS('
            SELECT `id_authorization_role`
            FROM `' . _DB_PREFIX_ . 'authorization_role` t
            WHERE ' . implode(' OR ', $whereClauses) . '
        ');

        $res = [];
        foreach ($roles as $role) {
            if ($enabled) {
                $res[] = $this->addModuleAccess($idProfile, $role['id_authorization_role']);
            } else {
                $res[] = $this->removeModuleAccess($idProfile, $role['id_authorization_role']);
            }
        }

        return in_array('error', $res) ? 'error' : 'ok';
    }
}
