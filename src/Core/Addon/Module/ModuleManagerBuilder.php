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
use Doctrine\Common\Cache\Psr6\DoctrineProvider;
use Language;
use PrestaShop\Decimal\Operation\Rounding;
use PrestaShop\PrestaShop\Adapter\HookManager;
use PrestaShop\PrestaShop\Adapter\LegacyLogger;
use PrestaShop\PrestaShop\Adapter\Module\AdminModuleDataProvider;
use PrestaShop\PrestaShop\Adapter\Module\ModuleDataProvider;
use PrestaShop\PrestaShop\Adapter\SymfonyContainer;
use PrestaShop\PrestaShop\Adapter\Tools;
use PrestaShop\PrestaShop\Core\Context\ApiClientContext;
use PrestaShop\PrestaShop\Core\Context\LanguageContext;
use PrestaShop\PrestaShop\Core\Localization\Locale;
use PrestaShop\PrestaShop\Core\Localization\Number\Formatter as NumberFormatter;
use PrestaShop\PrestaShop\Core\Localization\Specification\Number as NumberSpecification;
use PrestaShop\PrestaShop\Core\Localization\Specification\NumberCollection;
use PrestaShop\PrestaShop\Core\Localization\Specification\NumberSymbolList;
use PrestaShop\PrestaShop\Core\Localization\Specification\Price as PriceSpecification;
use PrestaShop\PrestaShop\Core\Module\ModuleManager;
use PrestaShop\PrestaShop\Core\Module\ModuleRepository;
use PrestaShop\PrestaShop\Core\Module\SourceHandler\SourceHandlerFactory;
use PrestaShopBundle\Event\Dispatcher\NullDispatcher;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\Routing\Loader\YamlFileLoader;
use Symfony\Component\Routing\Router;
use Symfony\Component\Translation\Loader\XliffFileLoader;

class ModuleManagerBuilder
{
    /**
     * Singleton of ModuleRepository.
     *
     * @var ModuleRepository
     */
    protected static $modulesRepository = null;
    /**
     * Singleton of ModuleManager.
     *
     * @var ModuleManager
     */
    protected static $moduleManager = null;
    protected static $adminModuleDataProvider = null;
    protected static $legacyLogger = null;
    protected static $moduleDataProvider = null;
    protected static $translator = null;
    protected static $instance = null;
    protected static $cacheProvider = null;
    /**
     * @var ApiClientContext
     */
    protected static $apiClientContext;
    /**
     * @var LanguageContext|null
     */
    protected static $languageContext = null;

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
                    new HookManager(),
                    _PS_MODULE_DIR_,
                    new XliffFileLoader(),
                    null
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
                    $this->getLanguageContext(),
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

        $tools = new Tools();
        $tools->refreshCaCertFile();

        self::$translator = Context::getContext()->getTranslator();

        $kernelDir = realpath($this->getConfigDir() . '/../../var');
        $cacheDir = $kernelDir . ($this->isDebug ? '/cache/dev' : '/cache/prod');
        self::$cacheProvider = DoctrineProvider::wrap(
            new FilesystemAdapter(
                '',
                0,
                $cacheDir . '/doctrine'
            )
        );

        self::$legacyLogger = new LegacyLogger();

        if (null === self::$adminModuleDataProvider) {
            self::$moduleDataProvider = new ModuleDataProvider(self::$legacyLogger, self::$translator);
            self::$apiClientContext = new ApiClientContext(null);
            self::$adminModuleDataProvider = new AdminModuleDataProvider(
                self::$moduleDataProvider,
                self::$translator,
                Context::getContext()->employee,
                self::$apiClientContext,
            );
            self::$adminModuleDataProvider->setRouter($this->getSymfonyRouter());
        }
    }

    /**
     * Returns an instance of \Symfony\Component\Routing\Router from Symfony scope into Legacy.
     *
     * @return Router
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

    private function getLanguageContext(): LanguageContext
    {
        if (self::$languageContext) {
            return self::$languageContext;
        }

        /** @var Language $language */
        $language = Context::getContext()->language;

        // If locale is present in context we can use, if not we create a mock one
        // the locale is not used by the ModuleRepository anyway only the language ID is relevant for its internal cache key generation
        if (Context::getContext()->currentLocale) {
            $locale = Context::getContext()->currentLocale;
        } else {
            $numberSymbolList = new NumberSymbolList(',', ' ', ';', '%', '-', '+', 'E', '^', '‰', '∞', 'NaN');
            $priceSpecsCollection = new NumberCollection();
            $priceSpecsCollection->add(
                'EUR',
                new PriceSpecification(
                    '#,##0.## ¤',
                    '-#,##0.## ¤',
                    ['latn' => $numberSymbolList],
                    2,
                    2,
                    true,
                    3,
                    3,
                    'symbol',
                    '€',
                    'EUR'
                )
            );
            $numberSpecification = new NumberSpecification(
                '#,##0.###',
                '-#,##0.###',
                [$numberSymbolList],
                3,
                2,
                true,
                2,
                3
            );
            $locale = new Locale(
                $language->locale,
                $numberSpecification,
                $priceSpecsCollection,
                new NumberFormatter(Rounding::ROUND_HALF_UP, 'latn')
            );
        }

        self::$languageContext = new LanguageContext(
            $language->id,
            $language->name,
            $language->iso_code,
            $language->locale,
            $language->language_code,
            $language->is_rtl,
            $language->date_format_lite,
            $language->date_format_full,
            $locale
        );

        return self::$languageContext;
    }

    protected function getConfigDir()
    {
        return _PS_ROOT_DIR_ . '/app/config';
    }
}
