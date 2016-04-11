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
    
    public static function findSlugByIdTab($idTab)
    {
        $result = Db::getInstance()->getRow('
            SELECT `class_name`
            FROM `'._DB_PREFIX_.'tab` t
            WHERE `id_tab` = "'.$idTab.'"
        ');
        return self::sluggifyTab($result);
    }
    
    public static function sluggifyTab($tab, $authorization = '')
    {
        return sprintf('ROLE_MOD_TAB_%s_%s', strtoupper($tab['class_name']), $authorization);
    }
    
    public static function sluggifyModule($module, $authorization = '')
    {
        return sprintf('ROLE_MOD_MODULE_%s_%s', strtoupper($module['name']), $authorization);
    }
    
    public function addAccess($idProfile, $idTab, $authorization)
    {
        $slug = self::findSlugByIdTab($idTab).$authorization;
        $result = Db::getInstance()->getRow('
            SELECT `id_authorization_role`
            FROM `'._DB_PREFIX_.'authorization_role` t
            WHERE `slug` = "'.$slug.'"
        ');
        $sql = '
            INSERT IGNORE INTO `'._DB_PREFIX_.'access` (`id_profile`, `id_authorization_role`)
            VALUES ('.$idProfile.','.$result['id_authorization_role'].')
        ';
        
        return Db::getInstance()->execute($sql) ? 'ok' : 'error';
    }
    
    public function removeAccess($idProfile, $idTab, $authorization)
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
}
