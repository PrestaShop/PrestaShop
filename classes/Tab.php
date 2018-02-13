<?php
/**
 * 2007-2018 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
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
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2018 PrestaShop SA
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

/**
 * Class TabCore
 */
class TabCore extends ObjectModel
{
    /** @var string Displayed name*/
    public $name;

    /** @var string Class and file name*/
    public $class_name;

    public $module;

    /** @var int parent ID */
    public $id_parent;

    /** @var int position */
    public $position;

    /** @var bool active */
    public $active = true;

    /** @var int hide_host_mode */
    public $hide_host_mode = false;

    /** @var string Icon font*/
    public $icon;

    const TAB_MODULE_LIST_URL = _PS_TAB_MODULE_LIST_URL_;

    /**
     * @see ObjectModel::$definition
     */
    public static $definition = array(
        'table' => 'tab',
        'primary' => 'id_tab',
        'multilang' => true,
        'fields' => array(
            'id_parent' => array('type' => self::TYPE_INT, 'validate' => 'isInt'),
            'position' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedInt'),
            'module' => array('type' => self::TYPE_STRING, 'validate' => 'isTabName', 'size' => 64),
            'class_name' => array('type' => self::TYPE_STRING, 'required' => true, 'size' => 64),
            'active' => array('type' => self::TYPE_BOOL, 'validate' => 'isBool'),
            'hide_host_mode' => array('type' => self::TYPE_BOOL, 'validate' => 'isBool'),
            'icon' => array('type' => self::TYPE_STRING, 'size' => 64),
            /* Lang fields */
            'name' => array('type' => self::TYPE_STRING, 'lang' => true, 'required' => true, 'validate' => 'isTabName', 'size' => 64),
        ),
    );

    protected static $_getIdFromClassName = null;

    /**
     * Additional treatments for Tab when creating new one:
     * - generate a new position
     * - add access for admin profile
     *
     * @param bool $autoDate
     * @param bool $nullValues
     *
     * @return int id_tab
     */
    public function add($autoDate = true, $nullValues = false)
    {
        self::$_cache_tabs = array();

        // Set good position for new tab
        $this->position = Tab::getNewLastPosition($this->id_parent);
        $this->module = Tools::strtolower($this->module);

        // Add tab
        if (parent::add($autoDate, $nullValues)) {
            //forces cache to be reloaded
            self::$_getIdFromClassName = null;

            return Tab::initAccess($this->id);
        }

        return false;
    }

    /**
     * @param bool $nullValues
     * @param bool $autoDate
     *
     * @return bool
     */
    public function save($nullValues = false, $autoDate = true)
    {
        self::$_getIdFromClassName = null;

        return parent::save();
    }

    /** When creating a new tab $id_tab, this add default rights to the table access
     *
     * @todo this should not be public static but protected
     *
     * @param int           $idTab
     * @param Context $context
     *
     * @return bool true if succeed
     */
    public static function initAccess($idTab, \Context $context = null)
    {
        if (!$context) {
            $context = Context::getContext();
        }

        /* Right management */
        $slug = 'ROLE_MOD_TAB_'.strtoupper(self::getClassNameById($idTab));

        foreach (array('CREATE', 'READ', 'UPDATE', 'DELETE') as $action) {
            Db::getInstance()->execute('INSERT INTO `'._DB_PREFIX_.'authorization_role` (`slug`) VALUES ("'.$slug.'_'.$action.'")');
        }

        $access = new Access();

        foreach (array('view', 'add', 'edit', 'delete') as $action) {
            $access->updateLgcAccess('1', $idTab, $action, true);

            if ($context->employee && $context->employee->id_profile) {
                $access->updateLgcAccess($context->employee->id_profile, $idTab, $action, true);
            }
        }

        return true;
    }

    public function delete()
    {
        if (parent::delete()) {
            $slug = 'ROLE_MOD_TAB_'.strtoupper($this->class_name);

            foreach (array('CREATE', 'READ', 'UPDATE', 'DELETE') as $action) {
                Db::getInstance()->execute('DELETE FROM `'._DB_PREFIX_.'authorization_role` WHERE `slug` = "'.$slug.'_'.$action.'"');
            }

            if (is_array(self::$_getIdFromClassName) && isset(self::$_getIdFromClassName[strtolower($this->class_name)])) {
                self::$_getIdFromClassName = null;
            }

            return $this->cleanPositions($this->id_parent);
        }

        return false;
    }

    /**
     * Get tab id
     *
     * @return int tab id
     */
    public static function getCurrentTabId()
    {
        $idTab = Tab::getIdFromClassName(Tools::getValue('controller'));
        // retro-compatibility 1.4/1.5
        if (empty($idTab)) {
            $idTab = Tab::getIdFromClassName(Tools::getValue('tab'));
        }
        return $idTab;
    }

    /**
     * Get tab parent id
     *
     * @return int tab parent id
     */
    public static function getCurrentParentId()
    {
        $cacheId = 'getCurrentParentId_'.Tools::strtolower(Tools::getValue('controller'));
        if (!Cache::isStored($cacheId)) {
            $value = Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue('
			SELECT `id_parent`
			FROM `'._DB_PREFIX_.'tab`
			WHERE LOWER(class_name) = \''.pSQL(Tools::strtolower(Tools::getValue('controller'))).'\'');
            if (!$value) {
                $value = -1;
            }
            Cache::store($cacheId, $value);

            return $value;
        }

        return Cache::retrieve($cacheId);
    }

    /**
     * Get tab
     *
     * @return array tab
     */
    public static function getTab($idLang, $idTab)
    {
        $cacheId = 'Tab::getTab_'.(int) $idLang.'-'.(int) $idTab;
        if (!Cache::isStored($cacheId)) {
            /* Tabs selection */
            $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow('
				SELECT *
				FROM `'._DB_PREFIX_.'tab` t
				LEFT JOIN `'._DB_PREFIX_.'tab_lang` tl
					ON (t.`id_tab` = tl.`id_tab` AND tl.`id_lang` = '.(int) $idLang.')
				WHERE t.`id_tab` = '.(int) $idTab.(defined('_PS_HOST_MODE_') ? ' AND `hide_host_mode` = 0' : '')
            );
            Cache::store($cacheId, $result);

            return $result;
        }

        return Cache::retrieve($cacheId);
    }

    /**
     * Return the list of tab used by a module
     *
     * @return array
     */
    public static function getModuleTabList()
    {
        $list = array();

        $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('
			SELECT t.`class_name`, t.`module`
			FROM `'._DB_PREFIX_.'tab` t
			WHERE t.`module` IS NOT NULL AND t.`module` != ""');

        if (is_array($result)) {
            foreach ($result as $detail) {
                $list[strtolower($detail['class_name'])] = $detail;
            }
        }

        return $list;
    }

    /**
     * Get tabs
     *
     * @return array tabs
     */
    protected static $_cache_tabs = array();
    public static function getTabs($idLang, $idParent = null)
    {
        if (!isset(self::$_cache_tabs[$idLang])) {
            self::$_cache_tabs[$idLang] = array();
            // Keep t.*, tl.name instead of only * because if translations are missing, the join on tab_lang will overwrite the id_tab in the results
            $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('
				SELECT t.*, tl.name
				FROM `'._DB_PREFIX_.'tab` t
				LEFT JOIN `'._DB_PREFIX_.'tab_lang` tl ON (t.`id_tab` = tl.`id_tab` AND tl.`id_lang` = '.(int) $idLang.')
				WHERE 1 '.(defined('_PS_HOST_MODE_') ? ' AND `hide_host_mode` = 0' : '').'
				ORDER BY t.`position` ASC'
            );

            if (is_array($result)) {
                foreach ($result as $row) {
                    if (!isset(self::$_cache_tabs[$idLang][$row['id_parent']])) {
                        self::$_cache_tabs[$idLang][$row['id_parent']] = array();
                    }
                    self::$_cache_tabs[$idLang][$row['id_parent']][] = $row;
                }
            }
        }
        if ($idParent === null) {
            $arrayAll = array();
            foreach (self::$_cache_tabs[$idLang] as $arrayParent) {
                $arrayAll = array_merge($arrayAll, $arrayParent);
            }
            return $arrayAll;
        }

        return (isset(self::$_cache_tabs[$idLang][$idParent]) ? self::$_cache_tabs[$idLang][$idParent] : array());
    }

    /**
     * Get tab id from name
     * @deprecated since version 1.7.1.0, available now in PrestaShopBundle\Entity\Repository\TabRepository::getOneIdByClassName($className)
     *
     * @param string $className
     *
     * @return int Tab ID
     */
    public static function getIdFromClassName($className)
    {
        $className = self::getClassName($className);
        if (self::$_getIdFromClassName === null) {
            self::$_getIdFromClassName = array();
            $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('SELECT id_tab, class_name FROM `'._DB_PREFIX_.'tab`', true, false);

            if (is_array($result)) {
                foreach ($result as $row) {
                    self::$_getIdFromClassName[strtolower($row['class_name'])] = $row['id_tab'];
                }
            }
        }

        return (isset(self::$_getIdFromClassName[$className]) ? (int)self::$_getIdFromClassName[$className] : false);
    }

    /**
     * @deprecated 1.7.0, to be removed in 1.7.1. Upgrade module to 1.7.
     */
    private static function getClassName($className)
    {
        $legacyClassNames = array(
            'AdminTools',
            'AdminPriceRule',
            'AdminAdmin',
            'AdminParentStats',
            'AdminMarketing',
            'AdminCarrierWizard',
            'AdminTabs',
        );

        if (in_array($className, $legacyClassNames)) {
            @trigger_error($className.' is a deprecated tab since version 1.7.0 and "Default" will be removed in 1.7.1.. Upgrade module using the docs: http://build.prestashop.com/news/how-we-reorganized-main-menu-prestashop-1.7/.', E_USER_DEPRECATED);
            $className = 'DEFAULT';
        }

        return strtolower($className);
    }

    /**
     * Get collection from module name
     *
     * @param      $module string Module name
     * @param null $idLang integer Language ID
     *
     * @return array|PrestaShopCollection Collection of tabs (or empty array)
     */
    public static function getCollectionFromModule($module, $idLang = null)
    {
        if (is_null($idLang)) {
            $idLang = Context::getContext()->language->id;
        }

        if (!Validate::isModuleName($module)) {
            return array();
        }

        $tabs = new PrestaShopCollection('Tab', (int) $idLang);
        $tabs->where('module', '=', $module);

        return $tabs;
    }

    /**
     * Enabling tabs for module
     *
     * @param $module string Module Name
     *
     * @return bool Status
     */
    public static function enablingForModule($module)
    {
        $tabs = Tab::getCollectionFromModule($module);
        if (!empty($tabs)) {
            foreach ($tabs as $tab) {
                $tab->active = 1;
                $tab->save();
            }

            return true;
        }

        return false;
    }

    /**
     * Disabling tabs for module
     *
     * @param $module string Module name
     * @return bool Status
     */
    public static function disablingForModule($module)
    {
        $tabs = Tab::getCollectionFromModule($module);
        if (!empty($tabs)) {
            foreach ($tabs as $tab) {
                $tab->active = 0;
                $tab->save();
            }

            return true;
        }

        return false;
    }

    /**
     * Get Instance from tab class name
     *
     * @param $className string Name of tab class
     * @param $idLang     integer id_lang
     *
     * @return Tab Tab object (empty if bad id or class name)
     */
    public static function getInstanceFromClassName($className, $idLang = null)
    {
        $idTab = (int) Tab::getIdFromClassName($className);
        return new Tab($idTab, $idLang);
    }

    public static function getNbTabs($idParent = null)
    {
        return (int)Db::getInstance()->getValue('
			SELECT COUNT(*)
			FROM `'._DB_PREFIX_.'tab` t
			'.(!is_null($idParent) ? 'WHERE t.`id_parent` = '.(int) $idParent : '')
        );
    }

    /**
     * return an available position in subtab for parent $id_parent
     *
     * @param mixed $idParent
     *
     * @return int
     */
    public static function getNewLastPosition($idParent)
    {
        return (Db::getInstance()->getValue('
			SELECT IFNULL(MAX(position),0)+1
			FROM `'._DB_PREFIX_.'tab`
			WHERE `id_parent` = '.(int) $idParent
        ));
    }

    public function move($direction)
    {
        $nbTabs = Tab::getNbTabs($this->id_parent);
        if ($direction != 'l' && $direction != 'r') {
            return false;
        }
        if ($nbTabs <= 1) {
            return false;
        }
        if ($direction == 'l' && $this->position <= 1) {
            return false;
        }
        if ($direction == 'r' && $this->position >= $nbTabs) {
            return false;
        }

        $newPosition = ($direction == 'l') ? $this->position - 1 : $this->position + 1;
        Db::getInstance()->execute('
			UPDATE `'._DB_PREFIX_.'tab` t
			SET position = '.(int) $this->position.'
			WHERE id_parent = '.(int) $this->id_parent.'
				AND position = '.(int) $newPosition
        );
        $this->position = $newPosition;

        return $this->update();
    }

    /**
     * Clean positions
     *
     * @param int $idParent Parent ID
     *
     * @return bool
     */
    public function cleanPositions($idParent)
    {
        $result = Db::getInstance()->executeS('
			SELECT `id_tab`
			FROM `'._DB_PREFIX_.'tab`
			WHERE `id_parent` = '.(int)$idParent.'
			ORDER BY `position`
		');
        $sizeof = count($result);
        for ($i = 0; $i < $sizeof; ++$i) {
            Db::getInstance()->execute('
				UPDATE `'._DB_PREFIX_.'tab`
				SET `position` = '.($i + 1).'
				WHERE `id_tab` = '.(int) $result[$i]['id_tab']
            );
        }
        return true;
    }

    /**
     * Update position
     *
     * @param bool $way
     * @param int  $position
     *
     * @return bool
     */
    public function updatePosition($way, $position)
    {
        if (!$res = Db::getInstance()->executeS('
			SELECT t.`id_tab`, t.`position`, t.`id_parent`
			FROM `'._DB_PREFIX_.'tab` t
			WHERE t.`id_parent` = '.(int) $this->id_parent.'
			ORDER BY t.`position` ASC'
        )) {
            return false;
        }

        foreach ($res as $tab) {
            if ((int) $tab['id_tab'] == (int) $this->id) {
                $movedTab = $tab;
            }
        }

        if (!isset($movedTab) || !isset($position)) {
            return false;
        }
        // < and > statements rather than BETWEEN operator
        // since BETWEEN is treated differently according to databases
        $result = (Db::getInstance()->execute('
			UPDATE `'._DB_PREFIX_.'tab`
			SET `position`= `position` '.($way ? '- 1' : '+ 1').'
			WHERE `position`
			'.($way
                ? '> '.(int) $movedTab['position'].' AND `position` <= '.(int) $position
                : '< '.(int) $movedTab['position'].' AND `position` >= '.(int) $position).'
			AND `id_parent`='.(int) $movedTab['id_parent'])
        && Db::getInstance()->execute('
			UPDATE `'._DB_PREFIX_.'tab`
			SET `position` = '.(int) $position.'
			WHERE `id_parent` = '.(int) $movedTab['id_parent'].'
			AND `id_tab`='.(int) $movedTab['id_tab']));

        return $result;
    }

    /**
     * Check Tab rights
     *
     * @param int $idTab Tab ID
     *
     * @return bool Current employee has access to tab
     */
    public static function checkTabRights($idTab)
    {
        static $tabAccesses = null;

        if (Context::getContext()->employee->id_profile == _PS_ADMIN_PROFILE_) {
            return true;
        }

        if ($tabAccesses === null) {
            $tabAccesses = Profile::getProfileAccesses(Context::getContext()->employee->id_profile);
        }

        if (isset($tabAccesses[(int) $idTab]['view'])) {
            return ($tabAccesses[(int) $idTab]['view'] === '1');
        }

        return false;
    }

    public static function recursiveTab($idTab, $tabs)
    {
        $adminTab = Tab::getTab((int) Context::getContext()->language->id, $idTab);
        $tabs[] = $adminTab;
        if ($adminTab['id_parent'] > 0) {
            $tabs = Tab::recursiveTab($adminTab['id_parent'], $tabs);
        }

        return $tabs;
    }

    /**
     * Overrides update to set position to last when changing parent tab
     *
     * @see ObjectModel::update
     *
     * @param bool $nullValues
     *
     * @return bool
     */
    public function update($nullValues = false)
    {
        $current_tab = new Tab($this->id);
        if ($current_tab->id_parent != $this->id_parent) {
            $this->position = Tab::getNewLastPosition($this->id_parent);
        }

        self::$_cache_tabs = array();

        return parent::update($nullValues);
    }

    /**
     * Get Tab by Profile ID
     *
     * @param int $idParent
     * @param int $idProfile
     *
     * @return array|false|mysqli_result|null|PDOStatement|resource
     */
    public static function getTabByIdProfile($idParent, $idProfile)
    {
        return Db::getInstance()->executeS('
			SELECT t.`id_tab`, t.`id_parent`, tl.`name`, a.`id_profile`
			FROM `'._DB_PREFIX_.'tab` t
			LEFT JOIN `'._DB_PREFIX_.'access` a
				ON (a.`id_tab` = t.`id_tab`)
			LEFT JOIN `'._DB_PREFIX_.'tab_lang` tl
				ON (t.`id_tab` = tl.`id_tab` AND tl.`id_lang` = '.(int)Context::getContext()->language->id.')
			WHERE a.`id_profile` = '.(int) $idProfile.'
			AND t.`id_parent` = '.(int) $idParent.'
			AND a.`view` = 1
			AND a.`edit` = 1
			AND a.`delete` = 1
			AND a.`add` = 1
			AND t.`id_parent` != 0 AND t.`id_parent` != -1
			'.(defined('_PS_HOST_MODE_') ? ' AND `hide_host_mode` = 0' : '').'
			ORDER BY t.`id_parent` ASC
		');
    }

    /**
     * @since 1.5.0
     */
    public static function getClassNameById($idTab)
    {
        return Db::getInstance()->getValue('SELECT class_name FROM '._DB_PREFIX_.'tab WHERE id_tab = '.(int) $idTab);
    }

    public static function getTabModulesList($idTab)
    {
        $modulesList = array('default_list' => array(), 'slider_list' => array());
        $xmlTabModulesList = false;

        if (file_exists(_PS_ROOT_DIR_.Module::CACHE_FILE_TAB_MODULES_LIST)) {
            $xmlTabModulesList = @simplexml_load_file(_PS_ROOT_DIR_.Module::CACHE_FILE_TAB_MODULES_LIST);
        }

        $className = null;
        $displayType = 'default_list';
        if ($xmlTabModulesList) {
            foreach ($xmlTabModulesList->tab as $tab) {
                foreach ($tab->attributes() as $key => $value) {
                    if ($key == 'class_name') {
                        $className = (string) $value;
                    }
                }

                if (Tab::getIdFromClassName((string) $className) == $idTab) {
                    foreach ($tab->attributes() as $key => $value) {
                        if ($key == 'display_type') {
                            $displayType = (string) $value;
                        }
                    }

                    foreach ($tab->children() as $module) {
                        $modulesList[$displayType][(int) $module['position']] = (string) $module['name'];
                    }
                    ksort($modulesList[$displayType]);
                }
            }
        }

        return $modulesList;
    }
}
