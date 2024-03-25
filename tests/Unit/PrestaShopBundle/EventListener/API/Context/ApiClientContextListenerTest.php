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

declare(strict_types=1);

namespace Tests\Unit\PrestaShopBundle\EventListener\API\Context;

use PHPUnit\Framework\MockObject\MockObject;
use PrestaShop\PrestaShop\Core\Context\ApiClientContextBuilder;
use PrestaShopBundle\Controller\Api\OAuth2\AccessTokenController;
use PrestaShopBundle\Entity\ApiClient;
use PrestaShopBundle\Entity\Repository\ApiClientRepository;
use PrestaShopBundle\EventListener\API\Context\ApiClientContextListener;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\PreAuthenticatedToken;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Security;
use Tests\Unit\PrestaShopBundle\EventListener\ContextEventListenerTestCase;

class ApiClientContextListenerTest extends ContextEventListenerTestCase
{
    /**
     * @dataProvider getExpectedClients
     */
    public function testBuildBasedOnSecurityToken(int $apiId, string $clientId, array $scopes): void
    {
        // Create request that mimic a call to external API
        $event = $this->createRequestEvent(new Request());

        $builder = new ApiClientContextBuilder(
            $this->mockRepository($apiId, $clientId, $scopes),
            $this->mockConfiguration(['PS_SHOP_DEFAULT' => 42])
        );
        $listener = new ApiClientContextListener(
            $builder,
            $this->mockSecurity($this->mockToken($clientId))
        );

        $listener->onKernelRequest($event);

        $apiClientContext = $builder->build();
        $this->assertNotNull($apiClientContext->getApiClient());
        $this->assertEquals($apiId, $apiClientContext->getApiClient()->getId());
        $this->assertEquals($clientId, $apiClientContext->getApiClient()->getClientId());
        $this->assertEquals($scopes, $apiClientContext->getApiClient()->getScopes());
        $this->assertEquals(42, $apiClientContext->getApiClient()->getShopId());
    }

    public function getExpectedClients(): iterable
    {
        yield 'client with scopes' => [
            42,
            'client_id',
            ['scope1', 'scope3'],
        ];

        yield 'client with empty scopes' => [
            51,
            'any_other_client_id',
            [],
        ];
    }

    public function testListenWithEmptySecurityToken(): void
    {
        // Create request that mimic a call to external API
        $event = $this->createRequestEvent(new Request());

        $listener = new ApiClientContextListener(
            $this->mockUnusedBuilder(),
            $this->mockSecurity(null)
        );
        $listener->onKernelRequest($event);
    }

    public function testRequestNotForApiIsIgnored(): void
    {
        $event = $this->createRequestEvent(new Request());

        $listener = new ApiClientContextListener(
            $this->mockUnusedBuilder(),
            $this->mockSecurity(null)
        );
        $listener->onKernelRequest($event);
    }

    public function testTokenApiRequestIsIgnored(): void
    {
        // When token access point is called the context listeners should not be executed
        $event = $this->createRequestEvent(new Request([], [], ['_controller' => AccessTokenController::class]));

        $listener = new ApiClientContextListener(
            $this->mockUnusedBuilder(),
            $this->mockSecurity(null)
        );
        $listener->onKernelRequest($event);
    }

    private function mockUnusedBuilder(): ApiClientContextBuilder|MockObject
    {
        $builder = $this->createMock(ApiClientContextBuilder::class);
        $builder->expects($this->never())->method('setClientId');

        return $builder;
    }

    private function mockToken(string $userIdentifier): TokenInterface|MockObject
    {
        // Note: we mock PreAuthenticatedToken instead of the interface, because getUserIdentifier is really introduced only in SF6,
        // so we can't mock it yet, once Symfony is upgraded we can mock the interface directly
        $token = $this->createMock(PreAuthenticatedToken::class);
        $token->method('getUserIdentifier')->willReturn($userIdentifier);

        return $token;
    }

    private function mockSecurity(?TokenInterface $token): Security|MockObject
    {
        $security = $this->createMock(Security::class);
        $security->method('getToken')->willReturn($token);

        return $security;
    }

    private function mockRepository(int $apiId, string $expectedClientId, array $scopes): ApiClientRepository|MockObject
    {
        $apiClient = new ApiClient();
        $apiClient->setId($apiId);
        $apiClient->setScopes($scopes);
        $apiClient->setClientId($expectedClientId);

        $builder = $this->createMock(ApiClientRepository::class);
        $builder
            ->method('getByClientId')
            ->with($expectedClientId)
            ->willReturn($apiClient)
        ;

        return $builder;
    }
}
