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
 * Class AccessCore
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
    public static $definition = array(
        'table' => 'access',
        'primary' => 'id_profile',
        'fields' => array(
            'id_profile' => array('type' => self::TYPE_INT, 'validate' => 'isNullOrUnsignedId', 'copy_post' => false),
            'id_authorization_role' => array('type' => self::TYPE_INT, 'validate' => 'isNullOrUnsignedId', 'copy_post' => false),
        ),
    );

    /**
     * Is access granted to this Role?
     *
     * @param string $role      Role name ("Superadministrator", "sales", "translator", etc.)
     * @param int    $idProfile Profile ID
     *
     * @return bool Whether access is granted
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
                $joinTable = _DB_PREFIX_.'access';
            } elseif (isset($matches['type']) && $matches['type'] == 'MODULE') {
                $joinTable = _DB_PREFIX_.'module_access';
            } else {
                throw new Exception('The slug '.$currentRole.' is invalid');
            }

            $isCurrentGranted = (bool) Db::getInstance()->getRow('
                SELECT t.`id_authorization_role`
                FROM `'._DB_PREFIX_.'authorization_role` t
                LEFT JOIN '.$joinTable.' j
                ON j.`id_authorization_role` = t.`id_authorization_role`
                WHERE `slug` = "'.$currentRole.'"
                AND j.`id_profile` = "'.$idProfile.'"
            ');

            if (!$isCurrentGranted) {
                return false;
            }
        }

        return true;
    }

    /**
     * Get all roles for the Profile ID
     *
     * @param int $idProfile Profile ID
     *
     * @return array Roles
     */
    public static function getRoles($idProfile)
    {
        $result =  Db::getInstance()->executeS('
            SELECT r.`slug`
            FROM `'._DB_PREFIX_.'authorization_role` r
            LEFT JOIN `'._DB_PREFIX_.'access` a
            ON r.`id_authorization_role` = a.`id_authorization_role`
            AND a.`id_profile` = "'.$idProfile.'"
            LEFT JOIN `'._DB_PREFIX_.'module_access` ma
            ON r.`id_authorization_role` = ma.`id_authorization_role`
            AND ma.`id_profile` = "'.$idProfile.'"
        ');

        foreach ((array) $result as $key => $role) {
            $result[$key] = $role['slug'];
        }

        return $result;
    }

    /**
     * Find Tab ID by slug
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
            FROM `'._DB_PREFIX_.'tab` t
            WHERE UCASE(`class_name`) = "'.$matches['classname'].'"
        ');

        return $result['id_tab'];
    }

    /**
     * Find slug by Tab ID
     *
     *
     * @param int $idTab Tab ID
     *
     * @return string Full module slug
     */
    public static function findSlugByIdTab($idTab)
    {
        $result = Db::getInstance()->getRow('
            SELECT `class_name`
            FROM `'._DB_PREFIX_.'tab` t
            WHERE `id_tab` = "'.$idTab.'"
        ');
        return self::sluggifyTab($result);
    }

    /**
     * Find slug by Module ID
     *
     * @param int $idModule Module ID
     *
     * @return string Full module slug
     */
    public static function findSlugByIdModule($idModule)
    {
        $result = Db::getInstance()->getRow('
            SELECT `name`
            FROM `'._DB_PREFIX_.'module` t
            WHERE `id_module` = "'.$idModule.'"
        ');
        return self::sluggifyModule($result);
    }

    /**
     * Sluggify tab
     *
     * @param string $tab           Tab class name
     * @param string $authorization 'CREATE'|'READ'|'UPDATE'|'DELETE'
     *
     * @return string Full slug for tab
     */
    public static function sluggifyTab($tab, $authorization = '')
    {
        return sprintf('ROLE_MOD_TAB_%s_%s', strtoupper($tab['class_name']), $authorization);
    }

    /**
     * Sluggify module
     *
     * @param string $module        Module name
     * @param string $authorization 'CREATE'|'READ'|'UPDATE'|'DELETE'
     *
     * @return string Full slug for module
     */
    public static function sluggifyModule($module, $authorization = '')
    {
        return sprintf('ROLE_MOD_MODULE_%s_%s', strtoupper($module['name']), $authorization);
    }

    /**
     * Get legacy authorization
     *
     * @param string $legacyAuth Legacy authorization
     *
     * @return bool|string|array Authorization
     */
    public static function getAuthorizationFromLegacy($legacyAuth)
    {
        $auth = array(
            'add' => 'CREATE',
            'view' => 'READ',
            'edit' => 'UPDATE',
            'configure' => 'UPDATE',
            'delete' => 'DELETE',
            'uninstall' => 'DELETE',
            'duplicate' => array('CREATE', 'UPDATE'),
            'all' => array('CREATE', 'READ', 'UPDATE', 'DELETE'),
        );

        return isset($auth[$legacyAuth]) ? $auth[$legacyAuth] : false;
    }

    /**
     * Add access
     *
     * @param int $idProfile Profile ID
     * @param int $idRole    Role ID
     *
     * @return string Whether access has been successfully granted ("ok", "error")
     */
    public function addAccess($idProfile, $idRole)
    {
        $sql = '
            INSERT IGNORE INTO `'._DB_PREFIX_.'access` (`id_profile`, `id_authorization_role`)
            VALUES ('.$idProfile.','.$idRole.')
        ';

        return Db::getInstance()->execute($sql) ? 'ok' : 'error';
    }

    /**
     * Remove access
     *
     * @param int $idProfile Profile ID
     * @param int $idRole    Role ID
     *
     * @return string Whether access has been successfully removed ("ok", "error")
     */
    public function removeAccess($idProfile, $idRole)
    {
        $sql = '
            DELETE FROM `'._DB_PREFIX_.'access`
            WHERE `id_profile` = "'.$idProfile.'"
            AND `id_authorization_role` = "'.$idRole.'"
        ';

        return Db::getInstance()->execute($sql) ? 'ok' : 'error';
    }

    /**
     * Add module access
     *
     * @param int $idProfile Profile ID
     * @param int $idRole    Role ID
     *
     * @return string Whether module access has been successfully granted ("ok", "error")
     */
    public function addModuleAccess($idProfile, $idRole)
    {
        $sql = '
            INSERT IGNORE INTO `'._DB_PREFIX_.'module_access` (`id_profile`, `id_authorization_role`)
            VALUES ('.$idProfile.','.$idRole.')
        ';

        return Db::getInstance()->execute($sql) ? 'ok' : 'error';
    }

    /**
     *
     * @param int $idProfile
     * @param int $idRole
     * @return string 'ok'|'error'
     */
    public function removeModuleAccess($idProfile, $idRole)
    {
        $sql = '
            DELETE FROM `'._DB_PREFIX_.'module_access`
            WHERE `id_profile` = "'.$idProfile.'"
            AND `id_authorization_role` = "'.$idRole.'"
        ';

        return Db::getInstance()->execute($sql) ? 'ok' : 'error';
    }

    /**
     * Update legacy access
     *
     * @param int    $idProfile Profile ID
     * @param int    $idTab     Tab ID
     * @param string $lgcAuth   Legacy authorization
     * @param int    $enabled   Whether access should be granted
     *
     * @return string Whether legacy access has been successfully updated ("ok", "error")
     * @throws Exception
     */
    public function updateLgcAccess($idProfile, $idTab, $lgcAuth, $enabled)
    {
        if ($idTab == -1) {
            $slug = 'ROLE_MOD_TAB_%_';
        } else {
            $slug = self::findSlugByIdTab($idTab);
        }

        $whereClauses = array();

        foreach ((array) self::getAuthorizationFromLegacy($lgcAuth) as $auth) {
            $whereClauses[] = ' `slug` LIKE "'.$slug.$auth.'"';
        }

        $roles = Db::getInstance()->executeS('
            SELECT `id_authorization_role`
            FROM `'._DB_PREFIX_.'authorization_role` t
            WHERE '.implode(' OR ', $whereClauses).'
        ');

        if (empty($roles)) {
            throw new \Exception('Cannot find role slug');
        }

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
     * Update (legacy) Module access
     *
     * @param int    $idProfile Profile ID
     * @param int    $idModule  Module ID
     * @param string $lgcAuth   Legacy authorization
     * @param int    $enabled   Whether module access should be granted
     *
     * @return string Whether module access has been succesfully changed ("ok", "error")
     */
    public function updateLgcModuleAccess($idProfile, $idModule, $lgcAuth, $enabled)
    {
        if ($idModule == -1) {
            $slug = 'ROLE_MOD_MODULE_%_';
        } else {
            $slug = self::findSlugByIdModule($idModule);
        }

        $whereClauses = array();

        foreach ((array) self::getAuthorizationFromLegacy($lgcAuth) as $auth) {
            $whereClauses[] = ' `slug` LIKE "'.$slug.$auth.'"';
        }

        $roles = Db::getInstance()->executeS('
            SELECT `id_authorization_role`
            FROM `'._DB_PREFIX_.'authorization_role` t
            WHERE '.implode(' OR ', $whereClauses).'
        ');

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
