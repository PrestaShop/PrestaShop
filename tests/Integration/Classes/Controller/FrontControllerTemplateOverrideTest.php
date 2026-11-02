<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Integration\Classes\Controller;

use Cache;
use Configuration;
use Context;
use FrontController;
use FrontControllerCore;
use Hook;
use Module;
use PrestaShop\PrestaShop\Core\Addon\Module\ModuleManagerBuilder;
use ReflectionClass;
use ReflectionProperty;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use TemplateFinder;
use Tests\Integration\Utility\ContextMocker;
use Tests\Resources\DatabaseDump;

/**
 * The tests install and uninstall modules, which clears the cache, so they run isolated.
 *
 * @group isolatedProcess
 */
class FrontControllerTemplateOverrideTest extends KernelTestCase
{
    private const FIRST_MODULE = 'ps_templateoverridefirst';
    private const SECOND_MODULE = 'ps_templateoverridesecond';
    private const FIRST_OUTPUT_KEY = 'TEMPLATE_OVERRIDE_FIRST_OUTPUT';
    private const SECOND_OUTPUT_KEY = 'TEMPLATE_OVERRIDE_SECOND_OUTPUT';
    private const FIRST_TEMPLATE = 'module:ps_templateoverridefirst/views/templates/front/page.tpl';
    private const SECOND_TEMPLATE = 'module:ps_templateoverridesecond/views/templates/front/page.tpl';
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
        self::bootKernel();

        DatabaseDump::restoreTables(self::TABLES_TO_RESTORE);

        $this->contextMocker = (new ContextMocker())->mockContext();
    }

    protected function tearDown(): void
    {
        $moduleManager = ModuleManagerBuilder::getInstance()->build();

        foreach ([self::FIRST_MODULE, self::SECOND_MODULE] as $moduleName) {
            if ($moduleManager->isInstalled($moduleName)) {
                $module = Module::getInstanceByName($moduleName);
                if ($module instanceof Module) {
                    $module->uninstall();
                }
            }
        }

        DatabaseDump::restoreTables(self::TABLES_TO_RESTORE);
        Cache::clean(Hook::MODULE_LIST_BY_HOOK_KEY . '*');

        if (null !== $this->contextMocker) {
            $this->contextMocker->resetContext();
        }

        parent::tearDown();
    }

    public function testTheThemeTemplateIsUsedWhenNoModuleOverridesIt(): void
    {
        $this->installModules([self::FIRST_MODULE => null]);
        $controller = $this->createFrontController();
        $templateFinder = $this->createMock(TemplateFinder::class);
        $templateFinder->method('getTemplate')->with('checkout/checkout')->willReturn('checkout/checkout.tpl');
        (new ReflectionProperty(FrontControllerCore::class, 'templateFinder'))->setValue($controller, $templateFinder);

        self::assertSame('checkout/checkout.tpl', $controller->getTemplateFile('checkout/checkout'));
    }

    public function testASingleModuleOverridesTheTemplate(): void
    {
        $this->installModules([self::FIRST_MODULE => self::FIRST_TEMPLATE]);

        self::assertSame(self::FIRST_TEMPLATE, $this->createFrontController()->getTemplateFile('checkout/checkout'));
    }

    public function testTheFirstModuleInHookOrderOverridesTheTemplateWhenSeveralDo(): void
    {
        $this->installModules([self::FIRST_MODULE => self::FIRST_TEMPLATE, self::SECOND_MODULE => self::SECOND_TEMPLATE]);

        self::assertSame(self::FIRST_TEMPLATE, $this->createFrontController()->getTemplateFile('checkout/checkout'));
    }

    public function testAModuleReturningNothingLeavesTheTemplateToTheNextOne(): void
    {
        $this->installModules([self::FIRST_MODULE => null, self::SECOND_MODULE => self::SECOND_TEMPLATE]);

        self::assertSame(self::SECOND_TEMPLATE, $this->createFrontController()->getTemplateFile('checkout/checkout'));
    }

    public function testTheFirstModuleInHookOrderOverridesTheLayoutWhenSeveralDo(): void
    {
        $this->installModules([self::FIRST_MODULE => self::FIRST_TEMPLATE, self::SECOND_MODULE => self::SECOND_TEMPLATE]);

        self::assertSame(self::FIRST_TEMPLATE, $this->createFrontController()->getLayout());
    }

    /**
     * Installs the modules in the given order, which is their position on both hooks.
     *
     * @param array<string, string|null> $outputByModule template path each module returns, null for none
     */
    private function installModules(array $outputByModule): void
    {
        $moduleManager = ModuleManagerBuilder::getInstance()->build();
        $outputKeys = [self::FIRST_MODULE => self::FIRST_OUTPUT_KEY, self::SECOND_MODULE => self::SECOND_OUTPUT_KEY];

        foreach ($outputByModule as $moduleName => $output) {
            self::assertTrue((bool) $moduleManager->install($moduleName));
            if (null !== $output) {
                Configuration::updateValue($outputKeys[$moduleName], $output);
            }
        }

        Cache::clean(Hook::MODULE_LIST_BY_HOOK_KEY . '*');
    }

    private function createFrontController(): FrontController
    {
        // The constructor boots a whole page context; both methods only read the context.
        $controller = (new ReflectionClass(FrontController::class))->newInstanceWithoutConstructor();
        $context = new ReflectionProperty($controller, 'context');
        $context->setValue($controller, Context::getContext());
        $controller->php_self = 'order';

        return $controller;
    }
}
