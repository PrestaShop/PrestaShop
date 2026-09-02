<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShopBundle\ApiPlatform\Resources;

use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiResource;
use PrestaShop\PrestaShop\Core\Domain\ApiClient\Query\GetApiClientForEditing;
use PrestaShopBundle\ApiPlatform\Metadata\CQRSGet;

#[ApiResource(
    operations: [
        new CQRSGet(
            uriTemplate: '/api-clients/infos',
            CQRSQuery: GetApiClientForEditing::class,
            scopes: [],
            CQRSQueryMapping: [
                '[_context][apiClientId]' => '[apiClientId]',
            ],
        ),
    ],
    normalizationContext: ['skip_null_values' => false],
)]
class ApiClient
{
    #[ApiProperty(identifier: true)]
    public int $apiClientId;

    public string $clientId;

    public string $clientName;

    public string $description;

    public ?string $externalIssuer;

    public bool $enabled;

    public int $lifetime;

    public array $scopes;
}
