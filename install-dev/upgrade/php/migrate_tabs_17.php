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

use PrestaShopBundle\Install\Install;
use PrestaShopBundle\Install\LanguageList;

/**
 * Migrate BO tabs for 1.7 (new reorganization of BO)
 */
function migrate_tabs_17()
{
    include_once(_PS_INSTALL_PATH_.'upgrade/php/add_new_tab.php');

    /* first make some room for new tabs */
    $moduleTabs = Db::getInstance()->executeS(
        'SELECT id_parent FROM '._DB_PREFIX_.'tab WHERE module IS NOT NULL AND module != "" ORDER BY id_tab ASC'
    );

    $moduleParents = array();

    foreach ($moduleTabs as $tab) {
        $idParent = $tab['id_parent'];
        $moduleParents[$idParent] = Db::getInstance()->getValue('SELECT class_name FROM '._DB_PREFIX_.'tab WHERE id_tab='.$idParent);
    }

    /* delete the old structure */
    Db::getInstance()->execute(
        'DELETE t, tl FROM '._DB_PREFIX_.'tab t JOIN '._DB_PREFIX_.'tab_lang tl ON (t.id_tab=tl.id_tab) WHERE module IS NULL OR module = ""'
    );

    $defaultLanguage = new Language((int)Configuration::get('PS_LANG_DEFAULT'));

    $languageList = LanguageList::getInstance();
    $languageList->setLanguage($defaultLanguage->iso_code);

    /* insert the new structure */
    ProfileCore::resetCacheAccesses();
    LanguageCore::resetCache();
    $install = new Install();
    $install->populateDatabase('tab');

    /* update remaining idParent */
    foreach($moduleParents as $idParent => $className) {
        $idTab = Db::getInstance()->getValue('SELECT id_tab FROM '._DB_PREFIX_.'tab WHERE class_name='.pSQL($className));
        Db::getInstance()->execute('UPDATE '._DB_PREFIX_.'tab SET id_parent='.(int)$idTab.' WHERE id_parent='.(int)$idParent);
    }
}
