<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Resources\ApiPlatform\Resources;

use ApiPlatform\Metadata\ApiResource;
use PrestaShop\PrestaShop\Core\Domain\Zone\Query\GetZoneForEditing;
use PrestaShopBundle\ApiPlatform\Metadata\CQRSFutureOperation;
use PrestaShopBundle\ApiPlatform\Metadata\CQRSGet;

/**
 * Uses an operation class of a future core: the whole resource is ignored, the rest of the API keeps working.
 */
#[ApiResource(
    operations: [
        new CQRSGet(
            uriTemplate: '/test/future/operation/{zoneId}',
            CQRSQuery: GetZoneForEditing::class,
            scopes: ['future_operation_read'],
        ),
        new CQRSFutureOperation(
            uriTemplate: '/test/future/operation/{zoneId}/future',
        ),
    ],
)]
class FutureOperationResource
{
    public int $zoneId;
}
