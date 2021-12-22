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

namespace PrestaShop\PrestaShop\Core\Addon\Module;

use Context;
use Db;
use GuzzleHttp\Client;
use PrestaShop\PrestaShop\Adapter\Addons\AddonsDataProvider;
use PrestaShop\PrestaShop\Adapter\Cache\Clearer;
use PrestaShop\PrestaShop\Adapter\Configuration;
use PrestaShop\PrestaShop\Adapter\LegacyContext;
use PrestaShop\PrestaShop\Adapter\LegacyLogger;
use PrestaShop\PrestaShop\Adapter\Module\AdminModuleDataProvider;
use PrestaShop\PrestaShop\Adapter\Module\ModuleDataProvider;
use PrestaShop\PrestaShop\Adapter\Module\ModuleDataUpdater;
use PrestaShop\PrestaShop\Adapter\Module\ModuleZipManager;
use PrestaShop\PrestaShop\Adapter\SymfonyContainer;
use PrestaShop\PrestaShop\Adapter\Tools;
use PrestaShop\PrestaShop\Core\Addon\Theme\ThemeManagerBuilder;
use PrestaShop\PrestaShop\Core\Util\File\YamlParser;
use PrestaShopBundle\Event\Dispatcher\NullDispatcher;
use PrestaShopBundle\Service\DataProvider\Admin\CategoriesProvider;
use PrestaShopBundle\Service\DataProvider\Marketplace\ApiClient;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Symfony\Component\Cache\DoctrineProvider;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Routing\Loader\YamlFileLoader;
use Symfony\Component\Routing\Router;

class ModuleManagerBuilder
{
    /**
     * Singleton of ModuleRepository.
     *
     * @var ModuleRepository
     */
    public static $modulesRepository = null;
    /**
     * Singleton of ModuleManager.
     *
     * @var ModuleManager
     */
    public static $moduleManager = null;
    public static $adminModuleDataProvider = null;
    public static $lecacyContext;
    public static $legacyLogger = null;
    public static $moduleDataProvider = null;
    public static $moduleDataUpdater = null;
    public static $moduleZipManager = null;
    public static $translator = null;
    public static $addonsDataProvider = null;
    public static $categoriesProvider = null;
    public static $instance = null;
    public static $cacheProvider = null;

    /**
     * @var bool
     */
    private $isDebug;

    /**
     * @return ModuleManagerBuilder|null
     */
    public static function getInstance()
    {
        if (self::$instance == null) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    /**
     * Returns an instance of ModuleManager.
     *
     * @return ModuleManager
     */
    public function build()
    {
        if (null === self::$moduleManager) {
            $sfContainer = SymfonyContainer::getInstance();
            if (null !== $sfContainer) {
                self::$moduleManager = $sfContainer->get('prestashop.module.manager');
            } else {
                self::$moduleManager = new ModuleManager(
                    self::$adminModuleDataProvider,
                    self::$moduleDataProvider,
                    self::$moduleDataUpdater,
                    $this->buildRepository(),
                    self::$moduleZipManager,
                    self::$translator,
                    new NullDispatcher(),
                    new Clearer\SymfonyCacheClearer()
                );
            }
        }

        return self::$moduleManager;
    }

    /**
     * Returns an instance of ModuleRepository.
     *
     * @return ModuleRepository
     */
    public function buildRepository()
    {
        if (null === self::$modulesRepository) {
            $sfContainer = SymfonyContainer::getInstance();
            if (null !== $sfContainer) {
                self::$modulesRepository = $sfContainer->get('prestashop.core.admin.module.repository');
            } else {
                self::$modulesRepository = new ModuleRepository(
                    self::$adminModuleDataProvider,
                    self::$moduleDataProvider,
                    self::$moduleDataUpdater,
                    self::$legacyLogger,
                    self::$translator,
                    _PS_MODULE_DIR_,
                    self::$cacheProvider
                );
            }
        }

        return self::$modulesRepository;
    }

    /**
     * @param bool $isDebug
     */
    private function __construct(bool $isDebug = _PS_MODE_DEV_)
    {
        $this->isDebug = $isDebug;
        /**
         * If the Symfony container is available, it will be used for the other methods
         * build & buildRepository. No need to init manually all the dependancies.
         */
        $sfContainer = SymfonyContainer::getInstance();
        if (null !== $sfContainer) {
            return;
        }

        $yamlParser = new YamlParser((new Configuration())->get('_PS_CACHE_DIR_'));

        $config = $yamlParser->parse($this->getConfigDir() . '/config.yml');
        $prestashopAddonsConfig =
            $yamlParser->parse($this->getConfigDir() . '/addons/categories.yml');

        $tools = new Tools();
        $tools->refreshCaCertFile();

        $clientConfig = $config['eight_points_guzzle']['clients']['addons_api'];
        $clientConfig['verify'] = _PS_CACHE_CA_CERT_FILE_;
        if (file_exists($this->getConfigDir() . '/parameters.php')) {
            $parameters = require $this->getConfigDir() . '/parameters.php';
            if (array_key_exists('addons.api_client.verify_ssl', $parameters['parameters'])) {
                $clientConfig['verify'] = $parameters['parameters']['addons.api_client.verify_ssl'];
            }
        }

        self::$translator = Context::getContext()->getTranslator();

        $marketPlaceClient = new ApiClient(
            new Client($clientConfig),
            self::$translator->getLocale(),
            $this->getCountryIso(),
            null,
            (new Configuration())->get('_PS_BASE_URL_'),
            \AppKernel::VERSION
        );

        self::$moduleZipManager = new ModuleZipManager(new Filesystem(), self::$translator, new NullDispatcher());
        self::$addonsDataProvider = new AddonsDataProvider($marketPlaceClient, self::$moduleZipManager);

        $kernelDir = realpath($this->getConfigDir() . '/../../var');
        self::$addonsDataProvider->cacheDir = $kernelDir . ($this->isDebug ? '/cache/dev' : '/cache/prod');

        self::$cacheProvider = new DoctrineProvider(
            new FilesystemAdapter(
                '',
                0,
                self::$addonsDataProvider->cacheDir . '/doctrine'
            )
        );

        $themeManagerBuilder = new ThemeManagerBuilder(Context::getContext(), Db::getInstance());
        $themeName = Context::getContext()->shop->theme_name;
        $themeModules = $themeName ?
                        $themeManagerBuilder->buildRepository()->getInstanceByName($themeName)->getModulesToEnable() :
                        [];

        self::$legacyLogger = new LegacyLogger();
        self::$categoriesProvider = new CategoriesProvider(
            $prestashopAddonsConfig['prestashop']['addons']['categories'],
            $themeModules
        );
        self::$lecacyContext = new LegacyContext();

        if (null === self::$adminModuleDataProvider) {
            self::$moduleDataProvider = new ModuleDataProvider(self::$legacyLogger, self::$translator);
            self::$adminModuleDataProvider = new AdminModuleDataProvider(
                self::$translator,
                self::$legacyLogger,
                self::$addonsDataProvider,
                self::$categoriesProvider,
                self::$moduleDataProvider,
                self::$cacheProvider,
                Context::getContext()->employee
            );
            self::$adminModuleDataProvider->setRouter($this->getSymfonyRouter());

            self::$translator = Context::getContext()->getTranslator();
            self::$moduleDataUpdater = new ModuleDataUpdater(self::$addonsDataProvider, self::$adminModuleDataProvider);
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
        $routeFileName = $this->isDebug === true ? 'routing_dev.yml' : 'routing.yml';
        $routesDirectory = $this->getConfigDir();
        $locator = new FileLocator([$routesDirectory]);
        $loader = new YamlFileLoader($locator);

        return new Router($loader, $routeFileName);
    }

    protected function getConfigDir()
    {
        return _PS_ROOT_DIR_ . '/app/config';
    }

    /**
     * Returns country iso from context.
     */
    private function getCountryIso()
    {
        return \CountryCore::getIsoById((int) \Configuration::get('PS_COUNTRY_DEFAULT'));
    }
}
