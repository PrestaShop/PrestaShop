<?php
/**
 * 2007-2015 PrestaShop.
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

use Context;
use PrestaShop\PrestaShop\Adapter\LegacyLogger;
use PrestaShop\PrestaShop\Adapter\Module\AdminModuleDataProvider;
use PrestaShop\PrestaShop\Adapter\Module\ModuleDataProvider;
use PrestaShop\PrestaShop\Adapter\Module\ModuleDataUpdater;
use PrestaShop\PrestaShop\Adapter\Module\ModuleZipManager;
use PrestaShop\PrestaShop\Adapter\Addons\AddonsDataProvider;
use PrestaShopBundle\Service\DataProvider\Admin\CategoriesProvider;
use PrestaShopBundle\Service\DataProvider\Marketplace\ApiClient;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Routing\Router;
use Symfony\Component\Routing\Loader\YamlFileLoader;
use GuzzleHttp\Client;

class ModuleManagerBuilder
{
    /**
     * Singleton of ModuleRepository.
     *
     * @var \PrestaShop\PrestaShop\Core\Addon\Module\ModuleRepository
     */
    public static $modulesRepository = null;
    public static $adminModuleDataProvider = null;
    public static $legacyLogger = null;
    public static $moduleDataProvider = null;
    public static $moduleDataUpdater = null;
    public static $moduleZipManager = null;
    public static $translator = null;
    public static $addonsDataProvider = null;
    public static $categoriesProvider = null;
    public static $instance = null;

    /**
     * @return ModuleManagerBuilder
     */
    static public function getInstance() {
        if (self::$instance == null) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    /**
     * Returns an instance of \PrestaShop\PrestaShop\Core\Addon\Module\ModuleManager.
     *
     * @global type $kernel
     *
     * @return \PrestaShop\PrestaShop\Core\Addon\Module\ModuleManager
     */
    public function build()
    {
        global $kernel;
        if (!is_null($kernel)) {
            return $kernel->getContainer()->get('prestashop.module.manager');
        } else {
            return new ModuleManager(
                self::$adminModuleDataProvider,
                self::$moduleDataProvider,
                self::$moduleDataUpdater,
                $this->buildRepository(),
                self::$moduleZipManager,
                self::$translator,
                Context::getContext()->employee
            );
        }
    }

    /**
     * Returns an instance of \PrestaShop\PrestaShop\Core\Addon\Module\ModuleRepository.
     *
     * @global type $kernel
     *
     * @return \PrestaShop\PrestaShop\Core\Addon\Module\ModuleRepository
     */
    public function buildRepository()
    {
        if (is_null(self::$modulesRepository)) {
            global $kernel;
            if (!is_null($kernel)) {
                self::$modulesRepository = $kernel->getContainer()->get('prestashop.core.admin.module.repository');
            } else {
                self::$modulesRepository = new ModuleRepository(
                    self::$adminModuleDataProvider,
                    self::$moduleDataProvider,
                    self::$moduleDataUpdater,
                    self::$legacyLogger,
                    self::$translator
                );
            }
        }

        return self::$modulesRepository;
    }

    private function __construct()
    {
        $marketPlaceClient = new ApiClient(
            new Client(array()),
            $this->getLanguageIso(),
            $this->getCountryIso(),
            _PS_VERSION_)
        ;

        self::$addonsDataProvider = new AddonsDataProvider($marketPlaceClient);
        self::$categoriesProvider = new CategoriesProvider($marketPlaceClient);

        if (is_null(self::$adminModuleDataProvider)) {
            self::$adminModuleDataProvider = new AdminModuleDataProvider(
                $this->getLanguageIso(),
                $this->getSymfonyRouter(),
                self::$addonsDataProvider,
                self::$categoriesProvider
            );

            self::$translator = Context::getContext()->getTranslator();
            self::$moduleDataUpdater = new ModuleDataUpdater(self::$addonsDataProvider, self::$adminModuleDataProvider);
            self::$legacyLogger = new LegacyLogger();
            self::$moduleDataProvider = new ModuleDataProvider(self::$legacyLogger, self::$translator);
            self::$moduleZipManager = new ModuleZipManager(new Filesystem(), new Finder(), self::$translator);
        }
    }

    /**
     * Returns an instance of \Symfony\Component\Routing\Router from Symfony scope into Legacy.
     *
     * @return \Symfony\Component\Routing\Router
     */
    private function getSymfonyRouter()
    {
        // get the environment to load the good routing file
        $routeFileName = _PS_MODE_DEV_ === true ? 'routing_dev.yml' : 'routing.yml';
        $routesDirectory = _PS_ROOT_DIR_.DIRECTORY_SEPARATOR.'app'.DIRECTORY_SEPARATOR.'config';
        $locator = new FileLocator(array($routesDirectory));
        $loader = new YamlFileLoader($locator);

        return new Router($loader, $routeFileName);
    }

    /**
     * Returns language iso from context.
     */
    private function getLanguageIso()
    {
        $langId = Context::getContext()->employee instanceof \Employee ? Context::getContext()->employee->id_lang : Context::getContext()->language->iso_code;

        return \LanguageCore::getIsoById($langId);
    }

    /**
     * Returns country iso from context.
     */
    private function getCountryIso()
    {
        return \CountryCore::getIsoById(\Configuration::get('PS_COUNTRY_DEFAULT'));
    }
}
