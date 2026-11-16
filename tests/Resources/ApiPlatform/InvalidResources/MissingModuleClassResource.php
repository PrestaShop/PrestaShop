<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Resources\ApiPlatform\InvalidResources;

use ApiPlatform\Metadata\ApiResource;
use PrestaShopBundle\ApiPlatform\Metadata\CQRSGet;

/**
 * A class of the resource's own code is missing: a real error, not a resource written for a newer core.
 */
#[ApiResource(
    operations: [
        new CQRSGet(
            uriTemplate: '/test/invalid/missing-module-class',
            openapi: new MissingOpenApiOperation(),
        ),
    ],
)]
class MissingModuleClassResource
{
}
