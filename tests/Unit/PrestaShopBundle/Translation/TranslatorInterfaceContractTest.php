<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\PrestaShopBundle\Translation;

use PHPUnit\Framework\TestCase;
use PrestaShopBundle\Translation\DataCollectorTranslator;
use PrestaShopBundle\Translation\Translator;
use PrestaShopBundle\Translation\TranslatorComponent;
use PrestaShopBundle\Translation\TranslatorInterface;
use ReflectionClass;

class TranslatorInterfaceContractTest extends TestCase
{
    /**
     * The container is asked for TranslatorInterface and the debug environment decorates the
     * result, so the concrete class a caller receives is not fixed. Anything the core calls on a
     * translator has to be on the interface, or the only way to type a translator is to name one
     * implementation - which then breaks in the environment that returns another.
     *
     * @dataProvider provideMethodsTheCoreCallsOnATranslator
     */
    public function testTheInterfaceDeclaresWhatTheCoreCalls(string $method): void
    {
        $this->assertTrue(
            method_exists(TranslatorInterface::class, $method),
            sprintf('%s is called on translators fetched by interface, so it must be declared on it.', $method)
        );
    }

    public function provideMethodsTheCoreCallsOnATranslator(): iterable
    {
        // trans:        everywhere
        // setLocale:    Context::getTranslator, PaymentModule, OrderHistory, ContextStateManager
        // getCatalogue: Module::isUsingNewTranslationSystem
        yield 'trans' => ['trans'];
        yield 'getLocale' => ['getLocale'];
        yield 'setLocale' => ['setLocale'];
        yield 'getCatalogue' => ['getCatalogue'];
    }

    /**
     * @dataProvider provideTranslatorsTheContainerCanReturn
     */
    public function testEveryTranslatorTheContainerCanReturnSatisfiesTheInterface(string $class): void
    {
        $this->assertTrue(
            is_a($class, TranslatorInterface::class, true),
            sprintf('%s can be returned by the container, so it must satisfy the interface.', $class)
        );
    }

    public function provideTranslatorsTheContainerCanReturn(): iterable
    {
        yield 'component' => [TranslatorComponent::class];
        yield 'debug decorator' => [DataCollectorTranslator::class];
        yield 'translator' => [Translator::class];
    }

    /**
     * The three implementations do not share a parent, which is why naming one of them in a
     * signature is what broke: a DataCollectorTranslator is not a TranslatorComponent.
     */
    public function testTheImplementationsDoNotShareAConcreteParent(): void
    {
        $this->assertNotInstanceOf(TranslatorComponent::class, $this->reflectWithoutConstructor(DataCollectorTranslator::class));
        $this->assertNotInstanceOf(DataCollectorTranslator::class, $this->reflectWithoutConstructor(TranslatorComponent::class));
    }

    private function reflectWithoutConstructor(string $class): object
    {
        return (new ReflectionClass($class))->newInstanceWithoutConstructor();
    }
}
