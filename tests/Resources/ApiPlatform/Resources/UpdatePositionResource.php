<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace Tests\Resources\ApiPlatform\Resources;

use ApiPlatform\Metadata\ApiResource;
use PrestaShopBundle\ApiPlatform\Metadata\PositionCollection;
use PrestaShopBundle\ApiPlatform\Metadata\UpdatePosition;

#[ApiResource(
    operations: [
        new UpdatePosition(
            uriTemplate: '/test/positions',
            scopes: [
                'test_positions_write',
            ],
            positionDefinition: 'prestashop.core.grid.test.position_definition',
        ),
    ]
)]
class UpdatePositionResource
{
    #[PositionCollection(rowIdField: 'testId')]
    public array $positions;
}
