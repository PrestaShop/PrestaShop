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
namespace PrestaShop\PrestaShop\Core\Addon\Module;

use Context;
use PrestaShop\PrestaShop\Adapter\SymfonyContainer;
use Doctrine\Common\Cache\FilesystemCache;
use PrestaShop\PrestaShop\Adapter\Configuration;
use PrestaShop\PrestaShop\Adapter\LegacyContext;
use PrestaShop\PrestaShop\Adapter\LegacyLogger;
use PrestaShop\PrestaShop\Adapter\Module\AdminModuleDataProvider;
use PrestaShop\PrestaShop\Adapter\Module\ModuleDataProvider;
use PrestaShop\PrestaShop\Adapter\Module\ModuleDataUpdater;
use PrestaShop\PrestaShop\Adapter\Module\ModuleZipManager;
use PrestaShop\PrestaShop\Adapter\Addons\AddonsDataProvider;
use PrestaShop\PrestaShop\Adapter\Tools;
use PrestaShopBundle\Event\Dispatcher\NullDispatcher;
use PrestaShopBundle\Service\DataProvider\Admin\CategoriesProvider;
use PrestaShopBundle\Service\DataProvider\Marketplace\ApiClient;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Routing\Router;
use Symfony\Component\Routing\Loader\YamlFileLoader;
use Symfony\Component\Yaml\Yaml;
use GuzzleHttp\Client;
use Symfony\Component\Filesystem\Exception\IOException;

class ModuleManagerBuilder
{
    /**
     * Singleton of ModuleRepository.
     *
     * @var \PrestaShop\PrestaShop\Core\Addon\Module\ModuleRepository
     */
    public static $modulesRepository = null;
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
     * @return null|ModuleManagerBuilder
     */
    public static function getInstance()
    {
        if (self::$instance == null) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    /**
     * Returns an instance of \PrestaShop\PrestaShop\Core\Addon\Module\ModuleManager.
     *
     * @return \PrestaShop\PrestaShop\Core\Addon\Module\ModuleManager
     */
    public function build()
    {
        $sfContainer = SymfonyContainer::getInstance();
        if (!is_null($sfContainer)) {
            return $sfContainer->get('prestashop.module.manager');
        } else {
            return new ModuleManager(
                self::$adminModuleDataProvider,
                self::$moduleDataProvider,
                self::$moduleDataUpdater,
                $this->buildRepository(),
                self::$moduleZipManager,
                self::$translator,
                new NullDispatcher(),
                Context::getContext()->employee
            );
        }
    }

    /**
     * Returns an instance of \PrestaShop\PrestaShop\Core\Addon\Module\ModuleRepository.
     *
     * @return \PrestaShop\PrestaShop\Core\Addon\Module\ModuleRepository
     */
    public function buildRepository()
    {
        if (is_null(self::$modulesRepository)) {
            $sfContainer = SymfonyContainer::getInstance();
            if (!is_null($sfContainer)) {
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

    private function __construct()
    {
        $phpConfigFile = $this->getConfigDir().'/config.php';
        if (file_exists($phpConfigFile)
            && filemtime($phpConfigFile) >= filemtime(_PS_ROOT_DIR_.DIRECTORY_SEPARATOR.'app'.DIRECTORY_SEPARATOR.'config'.DIRECTORY_SEPARATOR.'config.yml')) {
            $config = require($phpConfigFile);
        } else {
            $config = Yaml::parse(
                file_get_contents(
                    _PS_ROOT_DIR_.DIRECTORY_SEPARATOR.'app'.DIRECTORY_SEPARATOR.'config'.DIRECTORY_SEPARATOR.'config.yml'
                )
            );
            try {
                $filesystem = new Filesystem();
                $filesystem->dumpFile($phpConfigFile, '<?php return '.var_export($config, true).';'."\n");
            } catch (IOException $e) {
                return false;
            }
        }

        $clientConfig = $config['csa_guzzle']['clients']['addons_api']['config'];

        self::$translator = Context::getContext()->getTranslator();

        $marketPlaceClient = new ApiClient(
            new Client($clientConfig),
            self::$translator->getLocale(),
            $this->getCountryIso(),
            new Tools(),
            (new Configuration())->get('_PS_BASE_URL_'),
            \AppKernel::VERSION
        );

        $marketPlaceClient->setSslVerification(_PS_CACHE_CA_CERT_FILE_);
        if (file_exists($this->getConfigDir().'/parameters.php')) {
            $parameters = require($this->getConfigDir().'/parameters.php');
            if (array_key_exists('addons.api_client.verify_ssl', $parameters['parameters'])) {
                $marketPlaceClient->setSslVerification($parameters['parameters']['addons.api_client.verify_ssl']);
            }
        }

        self::$moduleZipManager = new ModuleZipManager(new Filesystem(), self::$translator, new NullDispatcher());
        self::$addonsDataProvider = new AddonsDataProvider($marketPlaceClient, self::$moduleZipManager);

        $kernelDir = dirname(__FILE__) . '/../../../../var';
        self::$addonsDataProvider->cacheDir = $kernelDir . '/cache/prod';
        if (_PS_MODE_DEV_) {
            self::$addonsDataProvider->cacheDir = $kernelDir . '/cache/dev';
        }

        self::$cacheProvider = new FilesystemCache(self::$addonsDataProvider->cacheDir.'/doctrine');

        self::$legacyLogger = new LegacyLogger();
        self::$categoriesProvider = new CategoriesProvider($marketPlaceClient, self::$legacyLogger);
        self::$lecacyContext = new LegacyContext();

        if (is_null(self::$adminModuleDataProvider)) {
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
            self::$moduleDataUpdater = new ModuleDataUpdater(
                self::$addonsDataProvider,
                self::$adminModuleDataProvider,
                self::$lecacyContext,
                self::$legacyLogger,
                self::$translator);
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
        $routesDirectory = $this->getConfigDir();
        $locator = new FileLocator(array($routesDirectory));
        $loader = new YamlFileLoader($locator);

        return new Router($loader, $routeFileName);
    }

    protected function getConfigDir()
    {
        return _PS_ROOT_DIR_.DIRECTORY_SEPARATOR.'app'.DIRECTORY_SEPARATOR.'config';
    }

    /**
     * Returns country iso from context.
     */
    private function getCountryIso()
    {
        return \CountryCore::getIsoById(\Configuration::get('PS_COUNTRY_DEFAULT'));
    }
}
