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
