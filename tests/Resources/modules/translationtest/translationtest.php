<?php
/*
* 2007-2015 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
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
*  @author PrestaShop SA <contact@prestashop.com>
*  @copyright  2007-2015 PrestaShop SA
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

if (!defined('_PS_VERSION_')) {
    exit;
}

if (file_exists(__DIR__ . '/vendor/autoload.php')) {
    require_once __DIR__ . '/vendor/autoload.php';
}

class translationtest extends Module
{
    private $adminControllerName;

    public function __construct()
    {
        $this->name = 'translationtest';
        $this->version = '1.0';
        $this->author = 'PrestaShop';
        $this->need_instance = 0;

        $this->bootstrap = true;
        parent::__construct();

        $this->displayName = 'translation tests';
        $this->description = 'Test the translations accross different templating engines and controllers';

        $this->ps_versions_compliancy = ['min' => '1.7.5.0', 'max' => _PS_VERSION_];

        $this->adminControllerName = 'AdminTranslationtestFoo';
        $this->controllers = ['bar']; // this is a front controller
    }

    /**
     * Content for the configuration page
     *
     * @return string
     *
     * @throws PrestaShopException
     */
    public function getContent()
    {
        $content = $this->trans('This wording belongs to the module file', [], 'Modules.Translationtest.Translationtest');

        return $content;
    }

    /**
     * {@inheritdoc}
     */
    public function install()
    {
        return parent::install() && $this->installTab();
    }

    /**
     * {@inheritdoc}
     */
    public function uninstall()
    {
        return parent::uninstall() && $this->uninstallTab();
    }

    /**
     * Installs the tab for the admin controller
     *
     * @return bool
     *
     * @throws PrestaShopDatabaseException
     * @throws PrestaShopException
     */
    public function installTab()
    {
        $tab = new Tab();
        $tab->class_name = $this->adminControllerName;
        $tab->active = 1;
        $tab->name = [];
        foreach (Language::getLanguages(true) as $lang) {
            $tab->name[$lang['id_lang']] = $this->name;
        }
        $tab->id_parent = -1; // do not show
        $tab->module = $this->name;

        return $tab->add();
    }

    /**
     * Uninstalls the tab for the admin controller
     *
     * @return bool
     *
     * @throws PrestaShopException
     */
    public function uninstallTab()
    {
        $tabId = Tab::getIdFromClassName($this->adminControllerName);
        if ($tabId > 0) {
            $tab = new Tab($tabId);
            if (Validate::isLoadedObject($tab)) {
                return $tab->delete();
            }
        }

        return true;
    }

    /**
     * Needed to access the new BO translations page
     *
     * @return bool
     */
    public function isUsingNewTranslationSystem()
    {
        return true;
    }
}
