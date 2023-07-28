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
use PrestaShop\PrestaShop\Adapter\Configuration;
use PrestaShop\PrestaShop\Adapter\HookManager;
use PrestaShop\PrestaShop\Adapter\LegacyContext;
use PrestaShop\PrestaShop\Adapter\LegacyLogger;
use PrestaShop\PrestaShop\Adapter\Module\AdminModuleDataProvider;
use PrestaShop\PrestaShop\Adapter\Module\ModuleDataProvider;
use PrestaShop\PrestaShop\Adapter\Module\ModuleDataUpdater;
use PrestaShop\PrestaShop\Adapter\SymfonyContainer;
use PrestaShop\PrestaShop\Adapter\Tools;
use PrestaShop\PrestaShop\Core\Addon\Theme\ThemeManagerBuilder;
use PrestaShop\PrestaShop\Core\Module\ModuleManager;
use PrestaShop\PrestaShop\Core\Module\ModuleRepository;
use PrestaShop\PrestaShop\Core\Module\SourceHandler\SourceHandlerFactory;
use PrestaShop\PrestaShop\Core\Util\File\YamlParser;
use PrestaShopBundle\Event\Dispatcher\NullDispatcher;
use PrestaShopBundle\Service\DataProvider\Admin\CategoriesProvider;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Symfony\Component\Cache\DoctrineProvider;
use Symfony\Component\Config\FileLocator;
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
    public static $translator = null;
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
                self::$moduleManager = $sfContainer->get(ModuleManager::class);
            } else {
                self::$moduleManager = new ModuleManager(
                    $this->buildRepository(),
                    self::$moduleDataProvider,
                    self::$adminModuleDataProvider,
                    new SourceHandlerFactory(),
                    self::$translator,
                    new NullDispatcher(),
                    new HookManager()
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
                self::$modulesRepository = $sfContainer->get(ModuleRepository::class);
            } else {
                self::$modulesRepository = new ModuleRepository(
                    self::$moduleDataProvider,
                    self::$adminModuleDataProvider,
                    self::$cacheProvider,
                    new HookManager(),
                    _PS_MODULE_DIR_,
                    Context::getContext()->language->id
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

        $prestashopAddonsConfig = $yamlParser->parse($this->getConfigDir() . '/addons/categories.yml');

        $tools = new Tools();
        $tools->refreshCaCertFile();

        self::$translator = Context::getContext()->getTranslator();

        $kernelDir = realpath($this->getConfigDir() . '/../../var');
        $cacheDir = $kernelDir . ($this->isDebug ? '/cache/dev' : '/cache/prod');
        self::$cacheProvider = new DoctrineProvider(
            new FilesystemAdapter(
                '',
                0,
                $cacheDir . '/doctrine'
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
                self::$categoriesProvider,
                self::$moduleDataProvider,
                Context::getContext()->employee
            );
            self::$adminModuleDataProvider->setRouter($this->getSymfonyRouter());

            self::$translator = Context::getContext()->getTranslator();
            self::$moduleDataUpdater = new ModuleDataUpdater();
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
}
