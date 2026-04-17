<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Core\Form\IdentifiableObject\DataProvider;

use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\CommandBus\CommandBusInterface;
use PrestaShop\PrestaShop\Core\Domain\ApiClient\Query\GetApiClientForEditing;
use PrestaShop\PrestaShop\Core\Domain\ApiClient\QueryResult\EditableApiClient;
use PrestaShop\PrestaShop\Core\Form\IdentifiableObject\DataProvider\ApiClientFormDataProvider;

class ApiClientFormDataProviderTest extends TestCase
{
    public function testGetDefaultData(): void
    {
        $queryBusMock = $this->createMock(CommandBusInterface::class);
        $provider = new ApiClientFormDataProvider($queryBusMock);

        $this->assertEquals(['lifetime' => 3600], $provider->getDefaultData());
    }

    /**
     * @dataProvider provideApiAccessData
     */
    public function testGetData(EditableApiClient $apiAccess, array $expectedData): void
    {
        $queryBusMock = $this->createMock(CommandBusInterface::class);
        $queryBusMock
            ->method('handle')
            ->with($this->isInstanceOf(GetApiClientForEditing::class))
            ->willReturn($apiAccess)
        ;

        $provider = new ApiClientFormDataProvider($queryBusMock);
        $this->assertEquals($expectedData, $provider->getData(42));
    }

    public function provideApiAccessData(): iterable
    {
        yield 'simple case with basic fields' => [
            new EditableApiClient(
                42,
                'client-id',
                'client-name',
                true,
                'short description',
                ['api_client_read', 'hook_read'],
                3600,
                null,
            ),
            [
                'client_id' => 'client-id',
                'client_name' => 'client-name',
                'description' => 'short description',
                'enabled' => true,
                'scopes' => ['api_client_read', 'hook_read'],
                'lifetime' => 3600,
                'external_issuer' => null,
            ],
        ];

        yield 'simple case with external issuer' => [
            new EditableApiClient(
                42,
                'client-id',
                'client-name',
                true,
                'short description',
                ['api_client_read', 'hook_read'],
                3600,
                'http://localhost/authorization_server',
            ),
            [
                'client_id' => 'client-id',
                'client_name' => 'client-name',
                'description' => 'short description',
                'enabled' => true,
                'scopes' => ['api_client_read', 'hook_read'],
                'lifetime' => 3600,
                'external_issuer' => 'http://localhost/authorization_server',
            ],
        ];
    }
}
