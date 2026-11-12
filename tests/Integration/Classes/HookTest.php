<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace Tests\Integration\Classes;

use Hook;
use Module;
use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Adapter\ServiceLocator;
use PrestaShop\PrestaShop\Core\Exception\CoreException;
use ReflectionClass;
use ReflectionMethod;
use RuntimeException;
use Throwable;

class HookTest extends TestCase
{
    public function testIsDisplayHookNameDisplayHooksStartWithDisplay(): void
    {
        $this->assertTrue(Hook::isDisplayHookName('displaySomething'));
    }

    public function testIsDisplayHookNameDisplayHooksCannotStartWithAction(): void
    {
        $this->assertFalse(Hook::isDisplayHookName('actionDoWeirdStuff'));
    }

    public function testIsDisplayHookNameHeaderIsNotADisplayHook(): void
    {
        $this->assertFalse(Hook::isDisplayHookName('header'));
    }

    /**
     * A module throwing inside a hook is wrapped in a CoreException thrown from Hook.php, so the
     * debug page reports Hook.php as the location. The message is the only thing that can say which
     * module and which hook were involved, so it has to name both (see issue #40399).
     */
    public function testAModuleFailingInAHookIsReportedWithTheHookAndModuleNames(): void
    {
        $environment = ServiceLocator::get('\\PrestaShop\\PrestaShop\\Adapter\\Environment');
        $this->assertTrue(
            $environment->isDebug(),
            'This test needs debug mode, which is the only mode where the hook exception propagates.'
        );

        /** @var Module $module */
        $module = (new ReflectionClass(HookTestThrowingModule::class))->newInstanceWithoutConstructor();
        $module->name = 'hooktest_throwing_module';

        $callHookOn = new ReflectionMethod(Hook::class, 'callHookOn');
        $callHookOn->setAccessible(true);

        try {
            $callHookOn->invoke(null, $module, 'actionHookTestThrows', []);
            $this->fail('Expected the module exception to be wrapped and rethrown in debug mode.');
        } catch (Throwable $e) {
            $this->assertInstanceOf(CoreException::class, $e);
            $this->assertStringContainsString('actionHookTestThrows', $e->getMessage());
            $this->assertStringContainsString('hooktest_throwing_module', $e->getMessage());
            $this->assertStringContainsString('boom raised inside the module hook', $e->getMessage());
            $this->assertInstanceOf(RuntimeException::class, $e->getPrevious());
        }
    }
}

class HookTestThrowingModule extends Module
{
    public function hookActionHookTestThrows(array $params): void
    {
        throw new RuntimeException('boom raised inside the module hook');
    }
}
