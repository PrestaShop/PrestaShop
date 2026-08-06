<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Integration\Adapter\Order\Checkout;

use Cache;
use CheckoutProcess;
use CheckoutSession;
use Configuration;
use Hook;
use Module;
use PrestaShop\PrestaShop\Adapter\Order\Checkout\CheckoutProcessProviderResolver;
use PrestaShop\PrestaShop\Core\Addon\Module\ModuleManagerBuilder;
use PrestaShopBundle\Translation\TranslatorComponent;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Tests\Integration\Utility\ContextMocker;
use Tests\Resources\DatabaseDump;

class CheckoutProcessProviderResolverTest extends KernelTestCase
{
    private const MODULE_NAME = 'ps_onepagecheckoutprovider';
    private const CONFLICTING_MODULE_NAME = 'ps_conflictingcheckoutprovider';
    private const OUTPUT_MODE_CONFIG_KEY = 'CHECKOUT_PROCESS_PROVIDER_TEST_OUTPUT';
    private const TABLES_TO_RESTORE = [
        'configuration',
        'configuration_lang',
        'module',
        'module_shop',
        'hook_module',
        'module_group',
        'authorization_role',
        'module_access',
        'log',
    ];

    private ?ContextMocker $contextMocker = null;
    private CheckoutProcessProviderResolver $checkoutProcessProviderResolver;

    protected function setUp(): void
    {
        parent::setUp();
        self::bootKernel();

        DatabaseDump::restoreTables(self::TABLES_TO_RESTORE);

        $this->contextMocker = (new ContextMocker())->mockContext();
        $this->checkoutProcessProviderResolver = self::getContainer()->get(CheckoutProcessProviderResolver::class);
    }

    protected function tearDown(): void
    {
        $moduleManager = ModuleManagerBuilder::getInstance()->build();

        foreach ([self::MODULE_NAME, self::CONFLICTING_MODULE_NAME] as $moduleName) {
            if ($moduleManager->isInstalled($moduleName)) {
                $module = Module::getInstanceByName($moduleName);
                if ($module instanceof Module) {
                    $module->uninstall();
                }
            }
        }

        DatabaseDump::restoreTables(self::TABLES_TO_RESTORE);

        if (null !== $this->contextMocker) {
            $this->contextMocker->resetContext();
        }

        parent::tearDown();
    }

    public function testResolveReturnsNullWhenNoProviderIsInstalled(): void
    {
        $resolvedProcess = $this->checkoutProcessProviderResolver->resolve(
            $this->createMock(CheckoutSession::class),
            $this->createMock(TranslatorComponent::class)
        );

        $this->assertNull($resolvedProcess);
    }

    public function testResolveReturnsCheckoutProcessWhenExactlyOneValidProviderIsInstalled(): void
    {
        $this->installModule(self::MODULE_NAME);

        $session = $this->createMock(CheckoutSession::class);
        $resolvedProcess = $this->checkoutProcessProviderResolver->resolve(
            $session,
            $this->createMock(TranslatorComponent::class)
        );

        $this->assertInstanceOf(CheckoutProcess::class, $resolvedProcess);
        $this->assertSame($session, $resolvedProcess->getCheckoutSession());
    }

    public function testResolveReturnsNullWhenProviderReturnsInvalidHookOutput(): void
    {
        $this->installModule(self::MODULE_NAME);
        Configuration::updateValue(self::OUTPUT_MODE_CONFIG_KEY, 'invalid');

        $resolvedProcess = $this->checkoutProcessProviderResolver->resolve(
            $this->createMock(CheckoutSession::class),
            $this->createMock(TranslatorComponent::class)
        );

        $this->assertNull($resolvedProcess);
    }

    public function testResolveReturnsNullWhenProviderIsDisabled(): void
    {
        $this->installModule(self::MODULE_NAME);
        Configuration::updateValue(self::OUTPUT_MODE_CONFIG_KEY, 'disabled');

        $resolvedProcess = $this->checkoutProcessProviderResolver->resolve(
            $this->createMock(CheckoutSession::class),
            $this->createMock(TranslatorComponent::class)
        );

        $this->assertNull($resolvedProcess);
    }

    public function testResolveReturnsNullWhenSeveralValidProvidersAreInstalled(): void
    {
        $this->installModule(self::MODULE_NAME);
        $this->installModule(self::CONFLICTING_MODULE_NAME);

        $resolvedProcess = $this->checkoutProcessProviderResolver->resolve(
            $this->createMock(CheckoutSession::class),
            $this->createMock(TranslatorComponent::class)
        );

        $this->assertNull($resolvedProcess);
    }

    private function installModule(string $moduleName): void
    {
        $moduleManager = ModuleManagerBuilder::getInstance()->build();

        $this->assertTrue((bool) $moduleManager->install($moduleName));

        Cache::clean(Hook::MODULE_LIST_BY_HOOK_KEY . '*');
    }
}
