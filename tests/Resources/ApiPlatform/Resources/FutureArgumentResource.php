<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Resources\ApiPlatform\Resources;

use ApiPlatform\Metadata\ApiResource;
use PrestaShop\PrestaShop\Core\Domain\Zone\Query\GetZoneForEditing;
use PrestaShopBundle\ApiPlatform\Metadata\CQRSGet;

/**
 * Uses an operation argument of a future core: the whole resource is ignored, the rest of the API keeps working.
 */
#[ApiResource(
    operations: [
        new CQRSGet(
            uriTemplate: '/test/future/argument/{zoneId}',
            CQRSQuery: GetZoneForEditing::class,
            scopes: ['future_argument_read'],
        ),
        new CQRSGet(
            uriTemplate: '/test/future/argument/{zoneId}/future',
            CQRSQuery: GetZoneForEditing::class,
            futureArgument: true,
        ),
    ],
)]
class FutureArgumentResource
{
    public int $zoneId;
}
