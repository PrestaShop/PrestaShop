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

use PrestaShop\PrestaShop\Adapter\LegacyLogger;
use PrestaShop\PrestaShop\Adapter\Module\AdminModuleDataProvider;
use PrestaShop\PrestaShop\Adapter\Module\ModuleDataProvider;
use PrestaShop\PrestaShop\Adapter\Module\ModuleDataUpdater;
use PrestaShop\PrestaShop\Adapter\Addons\AddonsDataProvider;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\Routing\Router;
use Symfony\Component\Routing\Loader\YamlFileLoader;

class ModuleManagerBuilder
{
    /**
     * Singleton of ModuleRepository
     * @var \PrestaShop\PrestaShop\Core\Addon\Module\ModuleRepository
     */
    public static $modulesRepository = null;

    /**
    * Returns an instance of \PrestaShop\PrestaShop\Core\Addon\Module\ModuleManager
    * @global type $kernel
    * @return \PrestaShop\PrestaShop\Core\Addon\Module\ModuleManager
    */
    public function build()
    {
        global $kernel;
        if (!is_null($kernel)) {
            return $kernel->getContainer()->get('prestashop.module.manager');
        } else {
            $addonsDataProvider = new AddonsDataProvider();
            $adminModuleDataProvider = new AdminModuleDataProvider($this->getLanguageIso(), $this->getSymfonyRouter(), $addonsDataProvider);
            $legacyLogger = new LegacyLogger();

            return new ModuleManager($adminModuleDataProvider,
                new ModuleDataProvider($legacyLogger),
                new ModuleDataUpdater($addonsDataProvider, $adminModuleDataProvider),
                $this->buildRepository(),
                \Context::getContext()->employee);
        }
    }

    /**
     * Returns an instance of \PrestaShop\PrestaShop\Core\Addon\Module\ModuleRepository
     * @global type $kernel
     * @return \PrestaShop\PrestaShop\Core\Addon\Module\ModuleRepository
     */
    public function buildRepository()
    {
        if (is_null(self::$modulesRepository)) {
            global $kernel;
            if (!is_null($kernel)) {
                self::$modulesRepository = $kernel->getContainer()->get('prestashop.core.admin.module.repository');
            } else {
                $addonsDataProvider = new AddonsDataProvider();
                $adminModuleDataProvider = new AdminModuleDataProvider($this->getLanguageIso(), $this->getSymfonyRouter(), $addonsDataProvider);
                $legacyLogger = new LegacyLogger();

                self::$modulesRepository = new ModuleRepository(
                    $adminModuleDataProvider,
                    new ModuleDataProvider($legacyLogger),
                    new ModuleDataUpdater($addonsDataProvider, $adminModuleDataProvider),
                    $legacyLogger
                );
            }
        }
        return self::$modulesRepository;
    }

    /**
     * Returns an instance of \Symfony\Component\Routing\Router from Symfony scope into Legacy
     *
     * @return \Symfony\Component\Routing\Router
     */
    private function getSymfonyRouter()
    {
        // get the environment to load the good routing file
        $routeFileName = _PS_MODE_DEV_ === true ? 'routing_dev.yml' : 'routing.yml';
        $routesDirectory = _PS_ROOT_DIR_ . DIRECTORY_SEPARATOR . 'app' . DIRECTORY_SEPARATOR . 'config';
        $locator = new FileLocator(array($routesDirectory));
        $loader = new YamlFileLoader($locator);
        return new Router($loader, $routeFileName);
    }

    /**
     * Returns language iso from context
     */
    private function getLanguageIso()
    {
        $langId = \Context::getContext()->employee instanceof \Employee ? \Context::getContext()->employee->id_lang : \Context::getContext()->language->iso_code;

        return \LanguageCore::getIsoById($langId);
    }
}
