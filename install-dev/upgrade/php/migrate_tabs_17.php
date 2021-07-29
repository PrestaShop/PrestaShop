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

use PrestaShop\PrestaShop\Adapter\Entity\Language;
use PrestaShopBundle\Install\LanguageList;
use PrestaShopBundle\Install\XmlLoader;

/**
 * Migrate BO tabs for 1.7 (new reorganization of BO)
 */
function migrate_tabs_17()
{
    include_once _PS_INSTALL_PATH_.'upgrade/php/add_new_tab.php';

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
    if (!populateTab()) {
        return false;
    }

    /* update remaining idParent */
    foreach($moduleParents as $idParent => $className) {
        if (!empty($className)) {
            $idTab = Db::getInstance()->getValue('SELECT id_tab FROM '._DB_PREFIX_.'tab WHERE class_name="'.pSQL($className).'"');
            Db::getInstance()->execute('UPDATE '._DB_PREFIX_.'tab SET id_parent='.(int)$idTab.' WHERE id_parent='.(int)$idParent);
        }
    }

    return true;
}

function populateTab()
{
    $languages = [];
    foreach (Language::getLanguages() as $lang) {
        $languages[$lang['id_lang']] = $lang['iso_code'];
    }

    // Because we use 1.7.7+ files but with a not-yet migrated Tab entity, we need to use
    // a custom XmlLoader to remove the `enabled` key before inserting to the DB
    $xml_loader = new \XmlLoader1700();
    $xml_loader->setTranslator(Context::getContext()->getTranslator());
    $xml_loader->setLanguages($languages);

    try {
        $xml_loader->populateEntity('tab');
    } catch (PrestashopInstallerException $e) {
        return false;
    }

    return true;
}

class XmlLoader1700 extends XmlLoader
{
    public function createEntityTab($identifier, array $data, array $data_lang): void
    {
        if (isset($data['enabled'])) {
            unset($data['enabled']);
        }
        if (isset($data['wording'])) {
            unset($data['wording']);
        }
        if (isset($data['wording_domain'])) {
            unset($data['wording_domain']);
        }
        parent::createEntityTab($identifier, $data, $data_lang);
    }
}
