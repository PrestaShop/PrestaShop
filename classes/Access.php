<?php
/**
 * 2007-2015 PrestaShop
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
 * @copyright 2007-2015 PrestaShop SA
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
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
            'id_profile' =>        array('type' => self::TYPE_INT, 'validate' => 'isNullOrUnsignedId', 'copy_post' => false),
            'id_authorization_role' =>    array('type' => self::TYPE_INT, 'validate' => 'isNullOrUnsignedId', 'copy_post' => false),
        ),
    );
    
    /**
     * 
     * @param string $authSlug
     * @return string
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
     * 
     * @param string $idTab
     * @return string
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
     * 
     * @param string $tab Tab class name
     * @param string $authorization 'CREATE'|'READ'|'UPDATE'|'DELETE'
     * @return string
     */
    public static function sluggifyTab($tab, $authorization = '')
    {
        return sprintf('ROLE_MOD_TAB_%s_%s', strtoupper($tab['class_name']), $authorization);
    }
    
    /**
     * 
     * @param string $module Module name
     * @param string $authorization 'CREATE'|'READ'|'UPDATE'|'DELETE'
     * @return string
     */
    public static function sluggifyModule($module, $authorization = '')
    {
        return sprintf('ROLE_MOD_MODULE_%s_%s', strtoupper($module['name']), $authorization);
    }
    
    /**
     * 
     * @param string $legacyAuth
     * @return string|array
     */
    public static function getAuthorizationFromLegacy($legacyAuth)
    {
        $auth = array(
            'add' => 'CREATE',
            'view' => 'READ',
            'edit' => 'UPDATE',
            'delete' => 'DELETE',
            'all' => array('CREATE', 'READ', 'UPDATE', 'DELETE'),
        );
        
        return isset($auth[$legacyAuth]) ? $auth[$legacyAuth] : false;
    }
    
    /**
     * 
     * @param int $idProfile
     * @param int $idRole
     * @return string
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
     * 
     * @param int $idProfile
     * @param string $slug
     * @return string
     */
    public function addAccessBySlug($idProfile, $slug)
    {
        $result = Db::getInstance()->getRow('
            SELECT `id_authorization_role`
            FROM `'._DB_PREFIX_.'authorization_role` t
            WHERE `slug` = "'.$slug.'"
        ');
        
        return $this->addAccess($idProfile, $result['id_authorization_role']);
    }
    
    /**
     * 
     * @param string|int $idProfile
     * @param string|int $idTab
     * @param string $authorization 'CREATE'|'READ'|'UPDATE'|'DELETE'
     * @return string 'ok'|'error'
     */
    public function addLgcAccess($idProfile, $idTab, $authorization)
    {
        return $this->addAccessBySlug($idProfile, self::findSlugByIdTab($idTab).$authorization);
    }
    
    /**
     * 
     * @param int $idProfile
     * @param int $idRole
     * @return string 'ok'|'error'
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
     * 
     * @param string|int $idProfile
     * @param string|int $idTab
     * @param string $authorization 'CREATE'|'READ'|'UPDATE'|'DELETE'
     * @return string 'ok'|'error'
     */
    public function removeLgcAccess($idProfile, $idTab, $authorization)
    {
        $slug = self::findSlugByIdTab($idTab).$authorization;
        $result = Db::getInstance()->getRow('
            SELECT `id_authorization_role`
            FROM `'._DB_PREFIX_.'authorization_role` t
            WHERE `slug` = "'.$slug.'"
        ');
        $sql = '
            DELETE FROM `'._DB_PREFIX_.'authorization_role` t
            WHERE `id_profile` = "'.$idProfile.'"
            AND `id_authorization_role` = "'.$result['id_authorization_role'].'"
        ';
        
        return Db::getInstance()->execute($sql) ? 'ok' : 'error';
    }
    
    /**
     * 
     * @param int $idProfile
     * @param int $idTab
     * @param string $lgcAuth
     * @param int $enabled
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
        
        foreach ($roles as $role) {
            if ($enabled) {
                $res[] = $this->addAccess($idProfile, $role['id_authorization_role']);
            } else {
                $res[] = $this->removeAccess($idProfile, $role['id_authorization_role']);
            }
        }
        
        return in_array('error', $res) ? 'error' : 'ok';
    }
}
