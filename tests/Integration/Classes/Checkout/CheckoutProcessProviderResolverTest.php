<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Integration\Classes\Checkout;

use Cache;
use CheckoutProcess;
use CheckoutProcessProviderResolver;
use CheckoutSession;
use Configuration;
use Hook;
use Module;
use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\Addon\Module\ModuleManagerBuilder;
use PrestaShopBundle\Translation\TranslatorComponent;
use Tests\Integration\Utility\ContextMocker;
use Tests\Resources\DatabaseDump;

class CheckoutProcessProviderResolverTest extends TestCase
{
    private const MODULE_NAME = 'ps_onepagecheckoutprovider';
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

    protected function setUp(): void
    {
        parent::setUp();

        DatabaseDump::restoreTables(self::TABLES_TO_RESTORE);

        $this->contextMocker = (new ContextMocker())->mockContext();
    }

    protected function tearDown(): void
    {
        $moduleManager = ModuleManagerBuilder::getInstance()->build();

        if ($moduleManager->isInstalled(self::MODULE_NAME)) {
            $module = Module::getInstanceByName(self::MODULE_NAME);
            if ($module instanceof Module) {
                $module->uninstall();
            }
        }

        DatabaseDump::restoreTables(self::TABLES_TO_RESTORE);

        if (null !== $this->contextMocker) {
            $this->contextMocker->resetContext();
        }

        parent::tearDown();
    }

    public function testResolveReturnsNullWhenConfiguredModuleDoesNotExist(): void
    {
        Configuration::updateValue(CheckoutProcessProviderResolver::PROVIDER_MODULE_CONFIG_KEY, 'missingcheckoutprovider');

        $resolver = new CheckoutProcessProviderResolver();

        $resolvedProcess = $resolver->resolve(
            $this->createMock(CheckoutSession::class),
            $this->createMock(TranslatorComponent::class)
        );

        $this->assertNull($resolvedProcess);
    }

    public function testResolveReturnsCheckoutProcessProvidedByConfiguredModule(): void
    {
        $this->installProviderModule();
        Configuration::updateValue(CheckoutProcessProviderResolver::PROVIDER_MODULE_CONFIG_KEY, self::MODULE_NAME);

        $session = $this->createMock(CheckoutSession::class);
        $resolver = new CheckoutProcessProviderResolver();

        $resolvedProcess = $resolver->resolve(
            $session,
            $this->createMock(TranslatorComponent::class)
        );

        $this->assertInstanceOf(CheckoutProcess::class, $resolvedProcess);
        $this->assertSame($session, $resolvedProcess->getCheckoutSession());
    }

    public function testResolveReturnsNullWhenConfiguredModuleReturnsInvalidHookOutput(): void
    {
        $this->installProviderModule();
        Configuration::updateValue(CheckoutProcessProviderResolver::PROVIDER_MODULE_CONFIG_KEY, self::MODULE_NAME);
        Configuration::updateValue(self::OUTPUT_MODE_CONFIG_KEY, 'invalid');

        $resolver = new CheckoutProcessProviderResolver();

        $resolvedProcess = $resolver->resolve(
            $this->createMock(CheckoutSession::class),
            $this->createMock(TranslatorComponent::class)
        );

        $this->assertNull($resolvedProcess);
    }

    private function installProviderModule(): void
    {
        $moduleManager = ModuleManagerBuilder::getInstance()->build();

        $this->assertTrue((bool) $moduleManager->install(self::MODULE_NAME));

        Cache::clean(Hook::MODULE_LIST_BY_HOOK_KEY . '*');
    }
}
