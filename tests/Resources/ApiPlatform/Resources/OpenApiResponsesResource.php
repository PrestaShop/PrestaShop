<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Resources\ApiPlatform\Resources;

use ApiPlatform\Metadata\ApiResource;
use PrestaShop\PrestaShop\Core\Domain\Country\Command\BulkDeleteCountriesCommand;
use PrestaShop\PrestaShop\Core\Domain\Shop\Exception\ShopNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\Zone\Command\AddZoneCommand;
use PrestaShop\PrestaShop\Core\Domain\Zone\Command\BulkDeleteZoneCommand;
use PrestaShop\PrestaShop\Core\Domain\Zone\Command\DeleteZoneCommand;
use PrestaShop\PrestaShop\Core\Domain\Zone\Command\ToggleZoneStatusCommand;
use PrestaShop\PrestaShop\Core\Domain\Zone\Exception\MissingZoneRequiredFieldsException;
use PrestaShop\PrestaShop\Core\Domain\Zone\Exception\ZoneNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\Zone\Query\GetZoneForEditing;
use PrestaShopBundle\ApiPlatform\Metadata\CQRSCreate;
use PrestaShopBundle\ApiPlatform\Metadata\CQRSDelete;
use PrestaShopBundle\ApiPlatform\Metadata\CQRSGet;
use Symfony\Component\HttpFoundation\Response;

/**
 * Operations documenting their responses from exceptionToStatus, and DELETE operations with a body or an output.
 */
#[ApiResource(
    operations: [
        new CQRSGet(
            uriTemplate: '/test/open-api/zones/{zoneId}',
            requirements: ['zoneId' => '\d+'],
            CQRSQuery: GetZoneForEditing::class,
        ),
        new CQRSGet(
            uriTemplate: '/test/open-api/zones/{zoneId}/unmapped',
            requirements: ['zoneId' => '\d+'],
            exceptionToStatus: [],
            CQRSQuery: GetZoneForEditing::class,
        ),
        new CQRSGet(
            uriTemplate: '/test/open-api/zones/{zoneId}/own-mapping',
            requirements: ['zoneId' => '\d+'],
            exceptionToStatus: [ZoneNotFoundException::class => Response::HTTP_NOT_FOUND],
            CQRSQuery: GetZoneForEditing::class,
        ),
        new CQRSCreate(
            uriTemplate: '/test/open-api/zones',
            CQRSCommand: AddZoneCommand::class,
            CQRSQuery: GetZoneForEditing::class,
        ),
        new CQRSDelete(
            uriTemplate: '/test/open-api/zones/{zoneId}',
            requirements: ['zoneId' => '\d+'],
            CQRSCommand: DeleteZoneCommand::class,
        ),
        // Not a real deletion: a DELETE that changes the zone (any command would do, the toggle is harmless) so
        // the zone can be read back by the query, like a bulk delete of the sub content of a parent entity would
        new CQRSDelete(
            uriTemplate: '/test/open-api/zones/{zoneId}/delete-with-query',
            requirements: ['zoneId' => '\d+'],
            CQRSCommand: ToggleZoneStatusCommand::class,
            CQRSQuery: GetZoneForEditing::class,
            CQRSQueryMapping: ['[enabled]' => '[active]'],
        ),
        new CQRSDelete(
            uriTemplate: '/test/open-api/zones/bulk-delete',
            CQRSCommand: BulkDeleteZoneCommand::class,
            allowEmptyBody: false,
        ),
        new CQRSDelete(
            uriTemplate: '/test/open-api/countries/bulk-delete',
            CQRSCommand: BulkDeleteCountriesCommand::class,
            allowEmptyBody: false,
        ),
    ],
    exceptionToStatus: [
        ZoneNotFoundException::class => Response::HTTP_NOT_FOUND,
        ShopNotFoundException::class => Response::HTTP_NOT_FOUND,
        MissingZoneRequiredFieldsException::class => Response::HTTP_UNPROCESSABLE_ENTITY,
    ],
)]
class OpenApiResponsesResource
{
    public int $zoneId;

    public string $name;

    public bool $active;

    /**
     * @var int[]
     */
    public array $zoneIds;

    /**
     * @var int[]
     */
    public array $countryIds;
}
