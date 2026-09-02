<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\Zone\QueryHandler;

use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsQueryHandler;
use PrestaShop\PrestaShop\Core\Domain\Zone\Exception\ZoneNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\Zone\Query\GetZoneForEditing;
use PrestaShop\PrestaShop\Core\Domain\Zone\QueryHandler\GetZoneForEditingHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Zone\QueryResult\EditableZone;
use Zone;

/**
 * Handles command that gets zone for editing
 *
 * @internal
 */
#[AsQueryHandler]
final class GetZoneForEditingHandler implements GetZoneForEditingHandlerInterface
{
    /**
     * {@inheritdoc}
     */
    public function handle(GetZoneForEditing $query): EditableZone
    {
        $zoneId = $query->getZoneId();
        $zone = new Zone($zoneId->getValue());

        if ($zone->id !== $zoneId->getValue()) {
            throw new ZoneNotFoundException(sprintf('Zone with id "%d" not found', $zoneId->getValue()));
        }

        return new EditableZone(
            $zoneId,
            (string) $zone->name,
            (bool) $zone->active,
            $zone->getAssociatedShops()
        );
    }
}
