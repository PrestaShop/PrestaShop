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

class ProfileCore extends ObjectModel
{
    /** @var string Name */
    public $name;

    /**
     * @see ObjectModel::$definition
     */
    public static $definition = array(
        'table' => 'profile',
        'primary' => 'id_profile',
        'multilang' => true,
        'fields' => array(
            /* Lang fields */
            'name' => array('type' => self::TYPE_STRING, 'lang' => true, 'validate' => 'isGenericName', 'required' => true, 'size' => 32),
        ),
    );

    protected static $_cache_accesses = array();

    /**
    * Get all available profiles
    *
    * @return array Profiles
    */
    public static function getProfiles($id_lang)
    {
        return Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('
		SELECT p.`id_profile`, `name`
		FROM `'._DB_PREFIX_.'profile` p
		LEFT JOIN `'._DB_PREFIX_.'profile_lang` pl ON (p.`id_profile` = pl.`id_profile` AND `id_lang` = '.(int)$id_lang.')
		ORDER BY `id_profile` ASC');
    }

    /**
    * Get the current profile name
    *
    * @return string Profile
    */
    public static function getProfile($id_profile, $id_lang = null)
    {
        if (!$id_lang) {
            $id_lang = Configuration::get('PS_LANG_DEFAULT');
        }

        return Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow('
			SELECT `name`
			FROM `'._DB_PREFIX_.'profile` p
			LEFT JOIN `'._DB_PREFIX_.'profile_lang` pl ON (p.`id_profile` = pl.`id_profile`)
			WHERE p.`id_profile` = '.(int)$id_profile.'
			AND pl.`id_lang` = '.(int)$id_lang
        );
    }

    public function add($autodate = true, $null_values = false)
    {
        return parent::add($autodate, true);
    }

    public function delete()
    {
        if (parent::delete()) {
            return (
                Db::getInstance()->execute('DELETE FROM `'._DB_PREFIX_.'access` WHERE `id_profile` = '.(int)$this->id)
                && Db::getInstance()->execute('DELETE FROM `'._DB_PREFIX_.'module_access` WHERE `id_profile` = '.(int)$this->id)
            );
        }
        return false;
    }

    public static function getProfileAccess($id_profile, $id_tab)
    {
        // getProfileAccesses is cached so there is no performance leak
        $accesses = Profile::getProfileAccesses($id_profile);
        return (isset($accesses[$id_tab]) ? $accesses[$id_tab] : false);
    }
    
    public static function getProfileAccesses($id_profile, $type = 'id_tab')
    {
        if (!in_array($type, array('id_tab', 'class_name'))) {
            return false;
        }

        if (!isset(self::$_cache_accesses[$id_profile])) {
            self::$_cache_accesses[$id_profile] = array();
        }

        if (!isset(self::$_cache_accesses[$id_profile][$type])) {
            self::$_cache_accesses[$id_profile][$type] = array();
            // Super admin profile has full auth
            if ($id_profile == _PS_ADMIN_PROFILE_) {
                self::fillCacheAccesses(
                    $id_profile,
                    $type,
                    array(
                        'id_profile' => _PS_ADMIN_PROFILE_,
                        'view' => '1',
                        'add' => '1',
                        'edit' => '1',
                        'delete' => '1'
                    )
                );
            } else {
                self::fillCacheAccesses(
                    $id_profile,
                    $type,
                    array(
                        'id_profile' => _PS_ADMIN_PROFILE_,
                        'view' => '0',
                        'add' => '0',
                        'edit' => '0',
                        'delete' => '0'
                    )
                );
                
                $result = Db::getInstance()->executeS('
				SELECT `slug`,
                                    `slug` LIKE "%CREATE" as "add",
                                    `slug` LIKE "%READ" as "view",
                                    `slug` LIKE "%UPDATE" as "edit",
                                    `slug` LIKE "%DELETE" as "delete"
				FROM `'._DB_PREFIX_.'authorization_role` a
				LEFT JOIN `'._DB_PREFIX_.'access` j ON j.id_authorization_role = a.id_authorization_role
				WHERE j.`id_profile` = '.(int)$id_profile);

                foreach ($result as $row) {
                    $id_tab = self::findIdTabByAuthSlug($row['slug']);
                    
                    self::$_cache_accesses[$id_profile][$type][$id_tab][array_search('1', $row)] = '1';
                }
            }
        }

        return self::$_cache_accesses[$id_profile][$type];
    }
    
    /**
     * 
     * @param int $id_profile
     * @param string $type
     * @param array $cacheData
     */
    private static function fillCacheAccesses($id_profile, $type, $cacheData = [])
    {
        foreach (Tab::getTabs(Context::getContext()->language->id) as $tab) {
            self::$_cache_accesses[$id_profile][$type][$tab[$type]] = array_merge(
                array(
                    'id_tab' => $tab['id_tab'],
                    'class_name' => $tab['class_name']
                ),
                $cacheData
            );
        }
    }
    
    /**
     * 
     * @param string $authSlug
     * @return int
     */
    private static function findIdTabByAuthSlug($authSlug)
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
}
