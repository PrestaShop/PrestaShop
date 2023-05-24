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
use Behat\Behat\Hook\Scope\AfterStepScope;
use Behat\Behat\Hook\Scope\StepScope;
use Behat\Gherkin\Node\ScenarioInterface;
use Behat\Gherkin\Node\StepNode;
use Behat\Testwork\Hook\Scope\BeforeSuiteScope;
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
use Mail;
use Manufacturer;
use Message;
use Meta;
use ObjectModel;
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
use PHPUnit\Framework\Assert;
use PrestaShop\PrestaShop\Adapter\LegacyContext;
use Product;
use ProductAttribute;
use ProductDownload;
use ProductSupplier;
use Profile;
use QuickAccess;
use RangePrice;
use RangeWeight;
use RequestSql;
use Risk;
use RuntimeException;
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
use Tests\Integration\Utility\ContextMocker;
use Tests\Resources\DatabaseDump;
use Tests\Resources\ResourceResetter;
use WarehouseProductLocation;
use WebserviceKey;
use Zone;

class CommonFeatureContext extends AbstractPrestaShopFeatureContext
{
    use SharedStorageTrait;

    /**
     * Shared storage key for last thrown exception
     */
    public const LAST_EXCEPTION_STORAGE_KEY = 'LAST_EXCEPTION';

    /**
     * Shared storage key for expected thrown exception
     */
    public const EXPECTED_EXCEPTION_STORAGE_KEY = 'EXPECTED_EXCEPTION';

    /**
     * Shared storage key for the step where the expected exception was raised
     */
    private const EXPECTED_EXCEPTION_STEP_STORAGE_KEY = 'EXPECTED_EXCEPTION_STEP';

    /**
     * PrestaShop Symfony AppKernel
     *
     * Required to access services through the container
     *
     * @var AppKernel
     */
    protected static $kernel;

    /**
     * @var ContextMocker|null
     */
    protected static $contextMocker;

    /**
     * @BeforeSuite
     */
    public static function prepare(BeforeSuiteScope $scope)
    {
        require_once __DIR__ . '/../../bootstrap.php';

        self::$kernel = new AppKernel('test', true);
        self::$kernel->boot();

        global $kernel;
        $kernel = self::$kernel;

        $employee = new Employee();
        Context::getContext()->employee = $employee->getByEmail('test@prestashop.com');

        // Disable legacy object model cache to prevent conflicts between scenarios.
        ObjectModel::disableCache();
    }

    /**
     * This hook can be used to flag a feature for database hard reset
     *
     * @BeforeFeature @restore-all-tables-before-feature
     */
    public static function restoreAllTablesBeforeFeature()
    {
        DatabaseDump::restoreAllTables();
        SharedStorage::getStorage()->clean();
        require_once _PS_ROOT_DIR_ . '/config/config.inc.php';
    }

    /**
     * This hook can be used to flag a feature for database hard reset
     *
     * @BeforeFeature @restore-all-tables-after-feature
     */
    public static function restoreAllTablesAfterFeature()
    {
        DatabaseDump::restoreAllTables();
        SharedStorage::getStorage()->clean();
        require_once _PS_ROOT_DIR_ . '/config/config.inc.php';
    }

    /**
     * This hook can be used to flag a feature for kernel reboot
     *
     * @BeforeFeature @reboot-kernel-before-feature
     */
    public static function rebootKernelBeforeFeature()
    {
        self::rebootKernel();
    }

    /**
     * This hook can be used to flag a feature for kernel reboot
     *
     * @AfterFeature @reboot-kernel-after-feature
     */
    public static function rebootKernelAfterFeature()
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
     * @BeforeFeature @clear-cache-before-feature
     */
    public static function clearCacheBeforeFeature()
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
        self::restoreTestDB();
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

    /**
     * @Given I restore tables :tableNames
     *
     * @param string $tableNames
     */
    public function restoreTables(string $tableNames): void
    {
        $tables = explode(',', $tableNames);
        DatabaseDump::restoreTables($tables);
    }

    /**
     * @Then :firstReference and :secondReference have different values
     *
     * @param string $firstReference
     * @param string $secondReference
     */
    public function assertDifferentValues(string $firstReference, string $secondReference): void
    {
        Assert::assertNotEquals(
            SharedStorage::getStorage()->get($firstReference),
            SharedStorage::getStorage()->get($secondReference),
            sprintf(
                '%s and %s are expected to be different but they have same value %s',
                $firstReference,
                $secondReference,
                (string) SharedStorage::getStorage()->get($firstReference)
            )
        );
    }

    /**
     * @Then :firstReference and :secondReference have same value
     *
     * @param string $firstReference
     * @param string $secondReference
     */
    public function assertSameValue(string $firstReference, string $secondReference): void
    {
        Assert::assertEquals(
            SharedStorage::getStorage()->get($firstReference),
            SharedStorage::getStorage()->get($secondReference),
            sprintf(
                '%s and %s are expected to be equals but they have different values %s != %s',
                $firstReference,
                $secondReference,
                (string) SharedStorage::getStorage()->get($firstReference),
                (string) SharedStorage::getStorage()->get($secondReference)
            )
        );
    }

    /**
     * This method shouldn't be public, but it is mandatory to be a behat hook. But you shouldn't call it manually.
     *
     * @AfterStep
     */
    public function checkLastExceptionAfterStep(AfterStepScope $scope): void
    {
        // If no exception nothing to do, if there is already an exception to handle we don't override it
        if (null === $this->getLastException() || null !== $this->getExpectedException()) {
            return;
        }

        $e = $this->getLastException();
        // We clean the last exception so that it doesn't pollute the following steps or scenarios, besides multiple
        // contexts could have this hook, and we only need to handle it once
        $this->cleanLastException();

        // When the last step ends with an exception we throw it because there is no next step to assert it
        $lastStep = $this->getLastStepFromScope($scope);
        if ($lastStep === $scope->getStep()) {
            throw $e;
        }

        // If there are steps left the exception must be checked in the next step, it is stored as the expected exception
        $this->setExpectedException($e, $scope->getStep());
    }

    /**
     * This method shouldn't be public, but it is mandatory to be a behat hook. But you shouldn't call it manually.
     *
     * @AfterStep
     */
    public function checkExpectedExceptionAfterStep(AfterStepScope $scope): void
    {
        if (null === $this->getExpectedException() || $scope->getStep() === $this->getExpectedExceptionStep()) {
            return;
        }

        // When an expected exception is stored from another step it means it was not checked, so it is unexpected
        $unexpectedException = $this->getExpectedException();
        $exceptionStep = $this->getExpectedExceptionStep();

        // We clean the expected exception so that it doesn't pollute the following scenarios
        $this->cleanExpectedException();

        throw new RuntimeException(implode(PHP_EOL, [
            'An unexpected exception was raised in previous step:',
            sprintf('Line %d: %s', $exceptionStep->getLine(), $exceptionStep->getText()),
            sprintf('%s: %s', get_class($unexpectedException), $unexpectedException->getMessage()),
            'Either it was unexpected and an error occurred or you forgot to add an intermediate step to assert that exception using assertLastErrorIs',
        ]), 0, $unexpectedException);
    }

    /**
     * This method shouldn't be public, but it is mandatory to be a behat hook. But you shouldn't call it manually.
     *
     * @BeforeScenario
     */
    public function cleanStoredExceptionsBeforeScenario(): void
    {
        $this->cleanLastException();
        $this->cleanExpectedException();
    }

    /**
     * This method is private because last exception should only be handled inside this abstract class, you can only
     * use setLastException from inherited classes.
     */
    private function cleanLastException(): void
    {
        $this->getSharedStorage()->clear(self::LAST_EXCEPTION_STORAGE_KEY);
    }

    /**
     * This method is private because expected exception should only be handled inside this abstract class, to clean it
     * you need to assert it using the assertLastError function, this will automatically clean the stored exception.
     */
    private function cleanExpectedException(): void
    {
        $this->getSharedStorage()->clear(self::EXPECTED_EXCEPTION_STORAGE_KEY);
        $this->getSharedStorage()->clear(self::EXPECTED_EXCEPTION_STEP_STORAGE_KEY);
    }

    /**
     * This method is private because expected exception should only be handled inside this abstract class, the expected
     * exception is automatically stored after each step.
     *
     * @param Exception $e
     * @param StepNode $step
     */
    private function setExpectedException(Exception $e, StepNode $step): void
    {
        $this->getSharedStorage()->set(self::EXPECTED_EXCEPTION_STORAGE_KEY, $e);
        $this->getSharedStorage()->set(self::EXPECTED_EXCEPTION_STEP_STORAGE_KEY, $step);
    }

    /**
     * This method is private because last exception should only be accessed inside this abstract class, you can only
     * use setLastException from inherited classes.
     *
     * @return Exception|null
     */
    private function getLastException(): ?Exception
    {
        if (!$this->getSharedStorage()->exists(self::LAST_EXCEPTION_STORAGE_KEY)) {
            return null;
        }

        return $this->getSharedStorage()->get(self::LAST_EXCEPTION_STORAGE_KEY);
    }

    /**
     * This method is private because expected exception should only be handled inside this abstract class, if you need
     * to assert it you should use the assertLastError function which returns the exception if you need more assertions.
     *
     * @return Exception|null
     */
    private function getExpectedException(): ?Exception
    {
        if (!$this->getSharedStorage()->exists(self::EXPECTED_EXCEPTION_STORAGE_KEY)) {
            return null;
        }

        return $this->getSharedStorage()->get(self::EXPECTED_EXCEPTION_STORAGE_KEY);
    }

    /**
     * This method is private because expected exception step should only be handled inside this abstract class, it is
     * only necessary to throw the unexpected exception in the next step only.
     *
     * @return StepNode|null
     */
    private function getExpectedExceptionStep(): ?StepNode
    {
        if (!$this->getSharedStorage()->exists(self::EXPECTED_EXCEPTION_STEP_STORAGE_KEY)) {
            return null;
        }

        return $this->getSharedStorage()->get(self::EXPECTED_EXCEPTION_STEP_STORAGE_KEY);
    }

    private function getScenarioFromScope(StepScope $scope): ?ScenarioInterface
    {
        foreach ($scope->getFeature()->getScenarios() as $scenario) {
            foreach ($scenario->getSteps() as $step) {
                if ($step === $scope->getStep()) {
                    return $scenario;
                }
            }
        }

        return null;
    }

    private function getLastStepFromScope(StepScope $scope): StepNode
    {
        $scenario = $this->getScenarioFromScope($scope);
        if (null !== $scenario) {
            $steps = $scenario->getSteps();
        } else {
            foreach ($scope->getFeature()->getBackground()->getSteps() as $step) {
                if ($step === $scope->getStep()) {
                    $steps = $scope->getFeature()->getBackground()->getSteps();
                    break;
                }
            }
        }

        // The step was not found in any scenario nor the background
        if (!isset($steps)) {
            throw new RuntimeException('Could not find step in the feature');
        }

        return $steps[count($steps) - 1];
    }

    private static function mockContext()
    {
        /** @var LegacyContext $legacyContext */
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
        Language::resetStaticCache();
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
