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
namespace PrestaShop\PrestaShop\Core\Addon\Module;

use PrestaShop\PrestaShop\Adapter\Module\AdminModuleDataProvider;
use PrestaShop\PrestaShop\Adapter\Module\ModuleDataProvider;
use PrestaShop\PrestaShop\Adapter\Module\ModuleDataUpdater;
use PrestaShop\PrestaShop\Core\Addon\Module\ModuleManager;
use PrestaShop\PrestaShop\Adapter\Addons\AddonsDataProvider;
class ModuleManagerBuilder
{
    /**
     * Singleton of ModuleRepository
     * @var \PrestaShop\PrestaShop\Core\Addon\Module\ModuleRepository
     */
    public static $modulesRepository = null;

    /**
    * Return an instance of \PrestaShop\PrestaShop\Core\Addon\Module\ModuleManager
    * @global type $kernel
    * @return \PrestaShop\PrestaShop\Core\Addon\Module\ModuleManager
    */
    public function build()
    {
        global $kernel;
        if (!is_null($kernel)) {
            return $kernel->getContainer()->get('prestashop.module.manager');
        }else {
            $langId = \Context::getContext()->employee instanceof \Employee ? \Context::getContext()->employee->id_lang : \Context::getContext()->language->iso_code;
            $languageISO = \LanguageCore::getIsoById($langId);

            return new ModuleManager(new AdminModuleDataProvider($languageISO),
                new ModuleDataProvider(),
                new ModuleDataUpdater(new AddonsDataProvider(), new AdminModuleDataProvider($languageISO)),
                $this->buildRepository(),
                \Context::getContext()->employee);
        }

    }

    /**
     * Return an instance of \PrestaShop\PrestaShop\Core\Addon\Module\ModuleRepository
     * @global type $kernel
     * @return \PrestaShop\PrestaShop\Core\Addon\Module\ModuleRepository
     */
    public function buildRepository()
    {
        if (is_null(self::$modulesRepository)) {
            global $kernel;
            if (!is_null($kernel)) {
                self::$modulesRepository = $kernel->getContainer()->get('prestashop.core.admin.module.repository');
            }else {
                $langId = \Context::getContext()->employee instanceof \Employee ? \Context::getContext()->employee->id_lang : \Context::getContext()->language->iso_code;
                $languageISO = \LanguageCore::getIsoById($langId);
                self::$modulesRepository = new ModuleRepository(
                    new AdminModuleDataProvider($kernel),
                    new ModuleDataProvider(),
                    new ModuleDataUpdater(new AddonsDataProvider(), new AdminModuleDataProvider($languageISO))
                );
            }

        }
        return self::$modulesRepository;
    }
}
