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

namespace Tests\Integration\Behaviour\Features\Context;

use Access;
use Address;
use AddressFormat;
use Alias;
use AppKernel;
use Attachment;
use AttributeGroup;
use Cache;
use Carrier;
use Cart;
use CartRule;
use Category;
use CMS;
use CMSCategory;
use CMSRole;
use Configuration;
use Connection;
use ConnectionsSource;
use Contact;
use Context;
use Currency;
use CustomerMessage;
use CustomerSession;
use CustomerThread;
use CustomizationField;
use DateRange;
use Employee;
use EmployeeSession;
use Exception;
use Feature;
use FeatureValue;
use Gender;
use Group;
use GroupReduction;
use Hook;
use Image;
use ImageType;
use Language;
use LegacyTests\Unit\ContextMocker;
use Mail;
use Manufacturer;
use Message;
use Meta;
use OrderCartRule;
use OrderHistory;
use OrderInvoice;
use OrderMessage;
use OrderPayment;
use OrderReturn;
use OrderReturnState;
use OrderSlip;
use OrderState;
use Pack;
use Page;
use PrestaShop\PrestaShop\Adapter\LegacyContext;
use PrestaShopBundle\Install\DatabaseDump;
use Product;
use ProductAttribute;
use ProductDownload;
use ProductSupplier;
use Profile;
use QuickAccess;
use RangePrice;
use RangeWeight;
use Referrer;
use RequestSql;
use Risk;
use SearchEngine;
use Shop;
use ShopGroup;
use ShopUrl;
use SpecificPrice;
use State;
use Stock;
use StockAvailable;
use StockMvt;
use StockMvtReason;
use StockMvtWS;
use Store;
use Supplier;
use SupplyOrder;
use SupplyOrderDetail;
use SupplyOrderHistory;
use SupplyOrderState;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Tab;
use Tag;
use Tax;
use TaxManagerFactory;
use TaxRule;
use TaxRulesGroup;
use Tests\Resources\ResourceResetter;
use WarehouseProductLocation;
use WebserviceKey;
use Zone;

class CommonFeatureContext extends AbstractPrestaShopFeatureContext
{
    /**
     * PrestaShop Symfony AppKernel
     *
     * Required to access services through the container
     *
     * @var AppKernel
     */
    protected static $kernel;

    /**
     * @var ContextMocker
     */
    protected static $contextMocker;

    /**
     * @BeforeSuite
     */
    public static function prepare($scope)
    {
        require_once __DIR__ . '/../../bootstrap.php';

        self::$kernel = new AppKernel('test', true);
        self::$kernel->boot();

        global $kernel;
        $kernel = self::$kernel;

        $employee = new Employee();
        Context::getContext()->employee = $employee->getByEmail('test@prestashop.com');
    }

    /**
     * This hook can be used to flag a feature for database hard reset
     *
     * @BeforeFeature @reset-database-before-feature
     */
    public static function cleanDatabaseHardPrepareFeature()
    {
        static::restoreTestDB();
        require_once _PS_ROOT_DIR_ . '/config/config.inc.php';
    }

    /**
     * This hook can be used to flag a feature for kernel reboot
     *
     * @BeforeFeature @reboot-kernel-before-feature
     */
    public static function rebootKernelPrepareFeature()
    {
        self::rebootKernel();
    }

    /**
     * This hook can be used to flag a scenario for kernel reboot
     *
     * @BeforeScenario @reboot-kernel-before-scenario
     */
    public static function rebootKernelBeforeScenario()
    {
        self::rebootKernel();
    }

    /**
     * Return PrestaShop Symfony services container
     *
     * @return ContainerInterface
     */
    public static function getContainer()
    {
        return static::$kernel->getContainer();
    }

    /**
     * @AfterFeature @reset-downloads-after-feature
     */
    public static function resetDownloads(): void
    {
        (new ResourceResetter())->resetDownloads();
    }

    /**
     * @AfterFeature @reset-img-after-feature
     */
    public static function resetImgDir(): void
    {
        (new ResourceResetter())->resetImages();
    }

    /**
     * @AfterFeature @clear-cache-after-feature
     */
    public static function clearCacheAfterFeature()
    {
        self::clearCache();
    }

    /**
     * @BeforeScenario @mock-context-on-scenario
     */
    public static function mockContextBeforeScenario()
    {
        self::mockContext();
    }

    /**
     * @AfterScenario @mock-context-on-scenario
     */
    public static function resetContextAfterScenario()
    {
        self::resetContext();
    }

    /**
     * @BeforeFeature @mock-context-on-feature
     */
    public static function mockContextBeforeFeature()
    {
        self::mockContext();
    }

    /**
     * @AfterFeature @mock-context-on-feature
     */
    public static function resetContextAfterFeature()
    {
        self::resetContext();
    }

    /**
     * @BeforeFeature @clear-cache-before-feature
     */
    public static function clearCacheBeforeFeature()
    {
        self::clearCache();
    }

    /**
     * @BeforeScenario @clear-cache-before-scenario
     */
    public static function clearCacheBeforeScenario()
    {
        self::clearCache();
    }

    /**
     * This hook can be used to flag a scenario for database hard reset
     *
     * @BeforeScenario @reset-database-before-scenario
     */
    public static function cleanDatabaseHardPrepareScenario()
    {
        static::restoreTestDB();
        require_once _PS_ROOT_DIR_ . '/config/config.inc.php';
    }

    /**
     * This hook can be used to flag a scenario for database hard reset
     *
     * @BeforeScenario @database-scenario
     */
    public function cleanDatabaseHardPrepare()
    {
        static::restoreTestDB();
        require_once _PS_ROOT_DIR_ . '/config/config.inc.php';
    }

    /**
     * @BeforeStep
     *
     * Clear Doctrine entity manager at each step in order to get fresh data
     */
    public function clearEntityManager()
    {
        $this::getContainer()->get('doctrine.orm.entity_manager')->clear();
    }

    /**
     * @Given I reboot kernel
     */
    public function rebootKernelOnDemand()
    {
        self::rebootKernel();
    }

    private static function mockContext()
    {
        /** @var LegacyContext $localeRepository */
        $legacyContext = self::getContainer()->get('prestashop.adapter.legacy.context');
        /*
         * We need to call this before initializing the ContextMocker because this method forcefully init
         * the shop context thus overriding the expected value
         */
        $legacyContext->getContext();

        self::$contextMocker = new ContextMocker();
        self::$contextMocker->mockContext();
    }

    private static function resetContext()
    {
        if (empty(self::$contextMocker)) {
            throw new \Exception('Context was not mocked');
        }
        self::$contextMocker->resetContext();
    }

    /**
     * This method reboots Symfony kernel, this is used to force recreation of services
     * (e.g: when you add some currencies in the database, you may need to reset the CLDR
     * related services to use the new ones)
     */
    private static function rebootKernel(): void
    {
        $realCacheDir = self::$kernel->getContainer()->getParameter('kernel.cache_dir');
        $warmupDir = substr($realCacheDir, 0, -1) . ('_' === substr($realCacheDir, -1) ? '-' : '_');
        self::$kernel->reboot($warmupDir);
    }

    private static function restoreTestDB(): void
    {
        if (!file_exists(sprintf('%s/ps_dump_%s.sql', sys_get_temp_dir(), AppKernel::VERSION))) {
            throw new Exception('You need to run \'composer create-test-db\' to create the initial test database');
        }

        DatabaseDump::restoreDb();
    }

    /**
     * Clears cache
     */
    private static function clearCache(): void
    {
        Address::resetStaticCache();
        Cache::clear();
        Carrier::resetStaticCache();
        Cart::resetStaticCache();
        CartRule::resetStaticCache();
        Category::resetStaticCache();
        Pack::resetStaticCache();
        Product::resetStaticCache();
        Language::resetCache();
        Currency::resetStaticCache();
        TaxManagerFactory::resetStaticCache();
        Group::clearCachedValues();
        Access::resetStaticCache();
        AddressFormat::resetStaticCache();
        Alias::resetStaticCache();
        Attachment::resetStaticCache();
        ProductAttribute::resetStaticCache();
        AttributeGroup::resetStaticCache();
        CMS::resetStaticCache();
        CMSCategory::resetStaticCache();
        CMSRole::resetStaticCache();
        Configuration::resetStaticCache();
        Connection::resetStaticCache();
        ConnectionsSource::resetStaticCache();
        Contact::resetStaticCache();
        CustomerMessage::resetStaticCache();
        CustomerSession::resetStaticCache();
        CustomerThread::resetStaticCache();
        CustomizationField::resetStaticCache();
        DateRange::resetStaticCache();
        EmployeeSession::resetStaticCache();
        Feature::resetStaticCache();
        FeatureValue::resetStaticCache();
        Gender::resetStaticCache();
        GroupReduction::resetStaticCache();
        Hook::resetStaticCache();
        Image::resetStaticCache();
        ImageType::resetStaticCache();
        Mail::resetStaticCache();
        Manufacturer::resetStaticCache();
        Message::resetStaticCache();
        Meta::resetStaticCache();
        Page::resetStaticCache();
        ProductDownload::resetStaticCache();
        ProductSupplier::resetStaticCache();
        Profile::resetStaticCache();
        QuickAccess::resetStaticCache();
        Referrer::resetStaticCache();
        RequestSql::resetStaticCache();
        Risk::resetStaticCache();
        SearchEngine::resetStaticCache();
        State::resetStaticCache();
        Store::resetStaticCache();
        Supplier::resetStaticCache();
        Tab::resetStaticCache();
        Tag::resetStaticCache();
        Zone::resetStaticCache();
        OrderCartRule::resetStaticCache();
        OrderHistory::resetStaticCache();
        OrderInvoice::resetStaticCache();
        OrderMessage::resetStaticCache();
        OrderPayment::resetStaticCache();
        OrderReturn::resetStaticCache();
        OrderReturnState::resetStaticCache();
        OrderSlip::resetStaticCache();
        OrderState::resetStaticCache();
        RangePrice::resetStaticCache();
        RangeWeight::resetStaticCache();
        Shop::resetStaticCache();
        ShopGroup::resetStaticCache();
        ShopUrl::resetStaticCache();
        Stock::resetStaticCache();
        StockAvailable::resetStaticCache();
        StockMvt::resetStaticCache();
        StockMvtReason::resetStaticCache();
        StockMvtWS::resetStaticCache();
        SupplyOrder::resetStaticCache();
        SupplyOrderDetail::resetStaticCache();
        SupplyOrderHistory::resetStaticCache();
        SupplyOrderState::resetStaticCache();
        WarehouseProductLocation::resetStaticCache();
        Tax::resetStaticCache();
        TaxRule::resetStaticCache();
        TaxRulesGroup::resetStaticCache();
        WebserviceKey::resetStaticCache();
        SpecificPrice::flushCache();
        SharedStorage::getStorage()->clean();
    }
}
