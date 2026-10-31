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
 * An operation rejects one of its arguments: a real error, not a resource written for a newer core.
 */
#[ApiResource(
    operations: [
        new CQRSGet(
            scopes: 'not_a_list',
        ),
    ],
)]
class ConstructorErrorResource
{
}
