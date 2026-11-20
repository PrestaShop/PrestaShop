<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\Classes;

use Cache;
use Error;
use Hook;
use HookCore;
use Module;
use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Adapter\Environment;
use PrestaShop\PrestaShop\Adapter\ServiceLocator;
use PrestaShop\PrestaShop\Core\Exception\CoreException;
use PrestaShop\PrestaShop\Core\Foundation\IoC\Container;
use PrestaShop\PrestaShop\Core\Module\Exception\ModuleErrorException;
use PrestaShop\PrestaShop\Core\Module\WidgetInterface;
use Psr\Log\AbstractLogger;
use ReflectionMethod;
use ReflectionProperty;
use RuntimeException;
use Stringable;
use Throwable;
use TypeError;

class FailingModule extends Module implements WidgetInterface
{
    public static Throwable $failure;

    public function hookDisplayHome(array $params): string
    {
        throw self::$failure;
    }

    public function renderWidget($hookName, array $configuration)
    {
        throw self::$failure;
    }

    public function getWidgetVariables($hookName, array $configuration)
    {
        return [];
    }
}

class RecordingLogger extends AbstractLogger
{
    /** @var array<int, array{level: mixed, message: string, context: array}> */
    public array $records = [];

    public ?Throwable $failure = null;

    public function log($level, string|Stringable $message, array $context = []): void
    {
        if ($this->failure !== null) {
            throw $this->failure;
        }
        $this->records[] = ['level' => $level, 'message' => (string) $message, 'context' => $context];
    }
}

class HookModuleFailureTest extends TestCase
{
    private ?Container $savedContainer;

    private RecordingLogger $logger;

    protected function setUp(): void
    {
        if (!defined('_PS_VERSION_')) {
            define('_PS_VERSION_', '9.0.0');
        }
        $this->savedContainer = ServiceLocator::getContainer();
        $this->logger = new RecordingLogger();
        Cache::store('Module::isEnabledfailingmodule', true);
        // Each test stands for a new request
        (new ReflectionProperty(HookCore::class, 'loggedModuleFailures'))->setValue(null, []);
    }

    protected function tearDown(): void
    {
        ServiceLocator::setServiceContainerInstance($this->savedContainer);
        Cache::clean('Module::isEnabledfailingmodule');
    }

    /**
     * @dataProvider provideFailures
     */
    public function testFailingHookRendersNothingAndIsLogged(Throwable $failure): void
    {
        $this->setDebug(false);
        FailingModule::$failure = $failure;

        $this->assertSame('', $this->callHook());
        $this->assertFailureLogged($failure);
    }

    /**
     * @dataProvider provideFailures
     */
    public function testFailingWidgetRendersNothingAndIsLogged(Throwable $failure): void
    {
        $this->setDebug(false);
        FailingModule::$failure = $failure;

        $this->assertSame('', Hook::coreRenderWidget($this->createModule(), 'displayHome', []));
        $this->assertFailureLogged($failure);
    }

    public static function provideFailures(): iterable
    {
        yield 'error, e.g. an undefined method after an upgrade' => [new Error('Something failed')];
        yield 'exception' => [new RuntimeException('Something failed')];
    }

    public function testFailureIsShownInDebugMode(): void
    {
        $this->setDebug(true);
        FailingModule::$failure = new Error('Something failed');

        try {
            $this->callHook();
            $this->fail('The failure should be shown in debug mode');
        } catch (CoreException $e) {
            $this->assertSame(FailingModule::$failure, $e->getPrevious());
        }
        $this->assertSame([], $this->logger->records);
    }

    public function testFailingWidgetWithoutHookNameRendersNothingAndIsLogged(): void
    {
        $this->setDebug(false);
        FailingModule::$failure = new RuntimeException('Something failed');

        // {widget name="..."} without hook= in a template
        $this->assertSame('', Hook::coreRenderWidget($this->createModule(), null, []));
        $this->assertCount(1, $this->logger->records);
        $this->assertStringStartsWith('Module "failingmodule" failed on hook "": ', $this->logger->records[0]['message']);
    }

    public function testSameFailureIsLoggedOncePerRequest(): void
    {
        $this->setDebug(false);
        FailingModule::$failure = new Error('Something failed');

        // e.g. a hook of a product list, called once per product
        for ($i = 0; $i < 3; ++$i) {
            $this->assertSame('', $this->callHook());
        }

        $this->assertCount(1, $this->logger->records);
    }

    public function testDifferentFailuresOnTheSameLineAreBothLogged(): void
    {
        $this->setDebug(false);
        $failures = [new Error('Something failed'), new TypeError('Something else failed')];

        foreach ($failures as $failure) {
            FailingModule::$failure = $failure;
            $this->assertSame('', $this->callHook());
        }

        $this->assertCount(2, $this->logger->records);
    }

    public function testModuleErrorIsStillRethrown(): void
    {
        $this->setDebug(false);
        FailingModule::$failure = new ModuleErrorException('Shown to the merchant');

        try {
            $this->callHook();
            $this->fail('A module error should be rethrown');
        } catch (ModuleErrorException $e) {
            $this->assertSame(FailingModule::$failure, $e);
        }
        $this->assertSame([], $this->logger->records);
    }

    public function testFailureToLogDoesNotTakeThePageDown(): void
    {
        $this->setDebug(false);
        $this->logger->failure = new RuntimeException('MySQL server has gone away');
        FailingModule::$failure = new RuntimeException('Something failed');
        $errorLog = (string) tempnam(sys_get_temp_dir(), 'ps_hook_');
        $savedErrorLog = ini_set('error_log', $errorLog);

        try {
            $this->assertSame('', $this->callHook());
            $this->assertStringContainsString('Module "failingmodule" failed on hook "displayHome"', (string) file_get_contents($errorLog));
        } finally {
            ini_set('error_log', (string) $savedErrorLog);
            @unlink($errorLog);
        }
    }

    private function assertFailureLogged(Throwable $failure): void
    {
        $this->assertCount(1, $this->logger->records);
        $this->assertSame('error', $this->logger->records[0]['level']);
        $this->assertStringStartsWith(
            sprintf('Module "failingmodule" failed on hook "displayHome": %s: Something failed in ', $failure::class),
            $this->logger->records[0]['message']
        );
        $this->assertSame(['object_type' => 'Module', 'object_id' => 42], $this->logger->records[0]['context']);
    }

    private function callHook(): mixed
    {
        return (new ReflectionMethod(Hook::class, 'callHookOn'))->invoke(null, $this->createModule(), 'displayHome', []);
    }

    private function createModule(): FailingModule
    {
        // Named after construction: a named module loads its data from the database
        $module = new FailingModule();
        $module->name = 'failingmodule';
        $module->id = 42;

        return $module;
    }

    private function setDebug(bool $debug): void
    {
        $container = new Container();
        $container->bind('\\PrestaShop\\PrestaShop\\Adapter\\Environment', new Environment($debug));
        $container->bind('\\PrestaShop\\PrestaShop\\Adapter\\LegacyLogger', $this->logger);
        ServiceLocator::setServiceContainerInstance($container);
    }
}
