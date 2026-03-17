<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\Classes\Checkout;

use CheckoutProcessProviderResolver;
use CheckoutSession;
use PHPUnit\Framework\TestCase;
use PrestaShopBundle\Translation\TranslatorComponent;

class TestableCheckoutProcessProviderResolver extends CheckoutProcessProviderResolver
{
    public function __construct(
        private readonly ?string $providerModuleName,
        private readonly ?int $providerModuleId
    ) {
    }

    protected function getProviderModuleName(): ?string
    {
        return $this->providerModuleName;
    }

    protected function getProviderModuleId(string $providerModuleName): ?int
    {
        return $this->providerModuleId;
    }
}

class CheckoutProcessProviderResolverTest extends TestCase
{
    public function testResolveReturnsNullWhenNoProviderModuleIsConfigured(): void
    {
        $resolver = new TestableCheckoutProcessProviderResolver(null, null);

        $resolvedProcess = $resolver->resolve(
            $this->createMock(CheckoutSession::class),
            $this->createMock(TranslatorComponent::class)
        );

        $this->assertNull($resolvedProcess);
    }

    public function testResolveReturnsNullWhenConfiguredModuleCannotBeResolved(): void
    {
        $resolver = new TestableCheckoutProcessProviderResolver('ps_onepagecheckoutprovider', null);

        $resolvedProcess = $resolver->resolve(
            $this->createMock(CheckoutSession::class),
            $this->createMock(TranslatorComponent::class)
        );

        $this->assertNull($resolvedProcess);
    }
}
