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
if (!defined('_PS_VERSION_')) {
    exit;
}

if (file_exists(__DIR__ . '/vendor/autoload.php')) {
    require_once __DIR__ . '/vendor/autoload.php';
}

class xlftranslatedmodule extends Module
{
    private $adminControllerName;

    public function __construct()
    {
        $this->name = 'xlftranslatedmodule';
        $this->version = '1.0';
        $this->author = 'PrestaShop';
        $this->need_instance = 0;

        $this->bootstrap = true;
        parent::__construct();

        $this->displayName = 'xlftranslatedmodule';
        $this->description = 'Test the translations accross different templating engines and controllers';

        $this->ps_versions_compliancy = ['min' => '1.7.5.0', 'max' => _PS_VERSION_];

        $this->adminControllerName = 'AdminXlftranslatedmoduleFoo';
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
        $content = $this->trans('This wording belongs to the module file', [], 'Modules.Xlftranslatedmodule.Xlftranslatedmodule');

        return $content;
    }

    /**
     * {@inheritdoc}
     */
    public function install()
    {
        return parent::install();
    }

    /**
     * {@inheritdoc}
     */
    public function uninstall()
    {
        return parent::uninstall();
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
