<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\Adapter\Order\Checkout;

use CheckoutProcess;
use CheckoutSession;
use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Adapter\Order\Checkout\CheckoutProcessProviderInterface;
use PrestaShop\PrestaShop\Adapter\Order\Checkout\CheckoutProcessProviderResolver;
use PrestaShopBundle\Translation\DataCollectorTranslator;
use PrestaShopBundle\Translation\Translator;
use PrestaShopBundle\Translation\TranslatorComponent;
use PrestaShopBundle\Translation\TranslatorInterface;

class StubCheckoutProcessProvider implements CheckoutProcessProviderInterface
{
    public function __construct(
        private readonly bool $enabled,
        private readonly CheckoutProcess $checkoutProcess
    ) {
    }

    public function isEnabled(): bool
    {
        return $this->enabled;
    }

    public function buildCheckoutProcess(
        $session,
        TranslatorInterface $translator
    ): CheckoutProcess {
        return $this->checkoutProcess;
    }
}

class TestableCheckoutProcessProviderResolver extends CheckoutProcessProviderResolver
{
    public function __construct(
        private readonly array $validProviders = []
    ) {
    }

    protected function getValidProviders(): array
    {
        return $this->validProviders;
    }
}

class CheckoutProcessProviderResolverTest extends TestCase
{
    public function testResolveReturnsNullWhenNoValidProviderExists(): void
    {
        $resolver = new TestableCheckoutProcessProviderResolver();

        $resolvedProcess = $resolver->resolve(
            $this->createMock(CheckoutSession::class),
            $this->createMock(TranslatorComponent::class)
        );

        $this->assertNull($resolvedProcess);
    }

    public function testResolveReturnsCheckoutProcessWhenExactlyOneValidProviderExists(): void
    {
        $checkoutProcess = $this->createMock(CheckoutProcess::class);
        $resolver = new TestableCheckoutProcessProviderResolver([
            'provider' => $this->createProvider(true, $checkoutProcess),
        ]);

        $resolvedProcess = $resolver->resolve(
            $this->createMock(CheckoutSession::class),
            $this->createMock(TranslatorComponent::class)
        );

        $this->assertSame($checkoutProcess, $resolvedProcess);
    }

    public function testResolveReturnsNullWhenSeveralValidProvidersExist(): void
    {
        $resolver = new TestableCheckoutProcessProviderResolver([
            'provider_b' => $this->createProvider(true),
            'provider_a' => $this->createProvider(true),
        ]);

        $resolvedProcess = $resolver->resolve(
            $this->createMock(CheckoutSession::class),
            $this->createMock(TranslatorComponent::class)
        );

        $this->assertNull($resolvedProcess);
    }

    /**
     * The front controller hands over whatever the container registered as the translator: that is
     * Translator in prod and DataCollectorTranslator in dev, and neither of them extends
     * TranslatorComponent. A signature naming that class fails while binding the argument, before
     * the method body runs, which is what broke the order page under the new front container.
     *
     * @dataProvider provideTranslatorsTheContainerCanReturn
     *
     * @param class-string<TranslatorInterface> $translatorClass
     */
    public function testResolveAcceptsEveryTranslatorTheContainerCanReturn(string $translatorClass): void
    {
        $resolver = new TestableCheckoutProcessProviderResolver();

        $resolvedProcess = $resolver->resolve(
            $this->createMock(CheckoutSession::class),
            $this->createMock($translatorClass)
        );

        $this->assertNull($resolvedProcess);
    }

    public function provideTranslatorsTheContainerCanReturn(): iterable
    {
        yield 'prod front container' => [Translator::class];
        yield 'dev front container' => [DataCollectorTranslator::class];
        yield 'the class the signature used to name' => [TranslatorComponent::class];
    }

    private function createProvider(bool $enabled, ?CheckoutProcess $checkoutProcess = null): CheckoutProcessProviderInterface
    {
        return new StubCheckoutProcessProvider(
            $enabled,
            $checkoutProcess ?? $this->createMock(CheckoutProcess::class)
        );
    }
}
