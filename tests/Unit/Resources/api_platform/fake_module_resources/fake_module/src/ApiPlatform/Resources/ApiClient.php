<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace FakeModule\ApiPlatform\Resources;

use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use PrestaShop\PrestaShop\Core\Domain\ApiClient\Exception\ApiClientNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\ApiClient\Query\GetApiClientForEditing;
use PrestaShopBundle\ApiPlatform\Provider\QueryProvider;

#[ApiResource(
    operations: [
        new Get(
            uriTemplate: '/api-client/{apiClientId}',
            requirements: ['apiClientId' => '\d+'],
            openapiContext: [
                'summary' => 'Get API Client details',
                'description' => 'Get API Client public details only, sensitive information like secrets is not returned',
                'parameters' => [
                    [
                        'name' => 'apiClientId',
                        'in' => 'path',
                        'required' => true,
                        'schema' => [
                            'type' => 'string',
                        ],
                        'description' => 'Id of the API Client you are requesting the details from',
                    ],
                    [
                        'name' => 'Authorization',
                        'in' => 'scopes',
                        'description' => 'api_client_read',
                    ],
                ],
            ],
            exceptionToStatus: [ApiClientNotFoundException::class => 404],
            provider: QueryProvider::class,
            extraProperties: [
                'query' => GetApiClientForEditing::class,
                'scopes' => ['api_client_read'],
            ]
        ),
    ],
)]
class ApiClient
{
    #[ApiProperty(identifier: true)]
    public int $apiClientId;

    public string $clientName;

    public string $clientId;

    public string $description;

    public bool $enabled;
}
