<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Core\Context;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\Context\ApiClientContextBuilder;
use PrestaShop\PrestaShop\Core\Domain\Configuration\ShopConfigurationInterface;
use PrestaShopBundle\Entity\ApiClient;
use PrestaShopBundle\Entity\Repository\ApiClientRepository;
use Tests\Unit\Core\Configuration\MockConfigurationTrait;

class ApiClientContextBuilderTest extends TestCase
{
    use MockConfigurationTrait;

    private const SHOP_ID = 42;
    private const API_CLIENT_ID = 51;

    /**
     * @dataProvider getExternalIssuers
     */
    public function testBuild(?string $externalIssuer): void
    {
        $apiClient = $this->getApiClientEntity($externalIssuer);
        $builder = new ApiClientContextBuilder(
            $this->mockRepository($apiClient, $externalIssuer),
            $this->mockConfiguration(['PS_SHOP_DEFAULT' => self::SHOP_ID])
        );

        $builder->setClientId('client_id');
        $builder->setExternalIssuer($externalIssuer);
        $apiClientContext = $builder->build();
        $this->assertNotNull($apiClientContext->getApiClient());
        $this->assertEquals(self::API_CLIENT_ID, $apiClientContext->getApiClient()->getId());
        $this->assertEquals('client_id', $apiClientContext->getApiClient()->getClientId());
        $this->assertEquals(['scope1', 'scope3'], $apiClientContext->getApiClient()->getScopes());
        $this->assertEquals($externalIssuer, $apiClientContext->getApiClient()->getExternalIssuer());
        $this->assertEquals(self::SHOP_ID, $apiClientContext->getApiClient()->getShopId());
    }

    public function testBuildWithStringValue(): void
    {
        $apiClient = $this->getApiClientEntity();
        $builder = new ApiClientContextBuilder(
            $this->mockRepository($apiClient),
            $this->mockConfiguration(['PS_SHOP_DEFAULT' => (string) self::SHOP_ID])
        );

        $builder->setClientId('client_id');
        $apiClientContext = $builder->build();
        $this->assertNotNull($apiClientContext->getApiClient());
        $this->assertEquals($apiClient->getClientId(), $apiClientContext->getApiClient()->getClientId());
        $this->assertEquals($apiClient->getScopes(), $apiClientContext->getApiClient()->getScopes());
        $this->assertEquals(null, $apiClientContext->getApiClient()->getExternalIssuer());
        $this->assertEquals(self::SHOP_ID, $apiClientContext->getApiClient()->getShopId());
    }

    public function testBuildNoApiClient(): void
    {
        $builder = new ApiClientContextBuilder(
            $this->createMock(ApiClientRepository::class),
            $this->createMock(ShopConfigurationInterface::class)
        );

        $apiClientContext = $builder->build();
        $this->assertNull($apiClientContext->getApiClient());
    }

    public function getExternalIssuers(): iterable
    {
        yield 'no external issuer' => [
            null,
        ];

        yield 'external issuer specified' => [
            'http://external_authorization_server',
        ];
    }

    private function mockRepository(ApiClient $apiClient, ?string $externalIssuer = null): ApiClientRepository|MockObject
    {
        $repository = $this->createMock(ApiClientRepository::class);
        $repository
            ->method('getByClientId')
            ->with($apiClient->getClientId(), $externalIssuer)
            ->willReturn($apiClient)
        ;

        return $repository;
    }

    private function getApiClientEntity(?string $externalIssuer = null): ApiClient
    {
        $apiClient = new ApiClient();
        $apiClient
            ->setId(self::API_CLIENT_ID)
            ->setClientId('client_id')
            ->setClientName('client_name')
            ->setScopes(['scope1', 'scope3'])
        ;

        if (!empty($externalIssuer)) {
            $apiClient->setExternalIssuer($externalIssuer);
        }

        return $apiClient;
    }
}
