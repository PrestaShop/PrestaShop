<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\Core\Security\OAuth2;

use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\Context\ApiClient;
use PrestaShop\PrestaShop\Core\Context\ApiClientContext;
use PrestaShop\PrestaShop\Core\Domain\ApiClient\Exception\ApiClientConstraintException;
use PrestaShop\PrestaShop\Core\Security\OAuth2\ApiClientScopeDelegationValidator;

class ApiClientScopeDelegationValidatorTest extends TestCase
{
    public function testItThrowsWhenCallerGrantsAScopeItDoesNotHold(): void
    {
        $validator = new ApiClientScopeDelegationValidator($this->mockContext(['api_client_write']));

        $this->expectException(ApiClientConstraintException::class);
        $this->expectExceptionCode(ApiClientConstraintException::INVALID_SCOPES);

        // A client cannot grant a scope it does not hold.
        $validator->assertCanGrant(['api_client_write', 'module_write']);
    }

    public function testItAllowsScopesTheCallerAlreadyHolds(): void
    {
        $validator = new ApiClientScopeDelegationValidator(
            $this->mockContext(['api_client_write', 'module_write', 'product_read'])
        );

        $validator->assertCanGrant(['module_write', 'product_read']);

        // No exception thrown.
        $this->addToAssertionCount(1);
    }

    public function testItAlwaysAllowsTheImplicitAuthenticatedScope(): void
    {
        $validator = new ApiClientScopeDelegationValidator($this->mockContext(['api_client_write']));

        $validator->assertCanGrant(['api_client_write', 'is_authenticated']);

        $this->addToAssertionCount(1);
    }

    public function testItSkipsValidationWhenCallerIsNotAnApiClient(): void
    {
        // Null caller = employee acting through the Back Office, or bootstrap of the very first
        // API client: no delegation restriction applies there.
        $validator = new ApiClientScopeDelegationValidator($this->mockContext(null));

        $validator->assertCanGrant(['module_write', 'customer_write']);

        $this->addToAssertionCount(1);
    }

    public function testItReportsEveryScopeThatCannotBeGranted(): void
    {
        $validator = new ApiClientScopeDelegationValidator($this->mockContext(['api_client_write']));

        try {
            $validator->assertCanGrant(['api_client_write', 'module_write', 'customer_write']);
            $this->fail('Expected ApiClientConstraintException was not thrown.');
        } catch (ApiClientConstraintException $e) {
            $this->assertStringContainsString('module_write', $e->getMessage());
            $this->assertStringContainsString('customer_write', $e->getMessage());
            $this->assertStringNotContainsString('api_client_write', $e->getMessage());
        }
    }

    private function mockContext(?array $callerScopes): ApiClientContext
    {
        $context = $this->createMock(ApiClientContext::class);
        $context
            ->method('getApiClient')
            ->willReturn(null === $callerScopes ? null : new ApiClient(1, 'caller', $callerScopes, null, 1));

        return $context;
    }
}
