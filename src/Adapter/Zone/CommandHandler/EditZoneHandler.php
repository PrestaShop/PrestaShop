<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\Zone\CommandHandler;

use PrestaShop\PrestaShop\Adapter\Domain\AbstractObjectModelHandler;
use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsCommandHandler;
use PrestaShop\PrestaShop\Core\Domain\Zone\Command\EditZoneCommand;
use PrestaShop\PrestaShop\Core\Domain\Zone\CommandHandler\EditZoneHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Zone\Exception\CannotEditZoneException;
use PrestaShop\PrestaShop\Core\Domain\Zone\Exception\ZoneException;
use PrestaShop\PrestaShop\Core\Domain\Zone\Exception\ZoneNotFoundException;
use PrestaShopException;
use Zone;

#[AsCommandHandler]
final class EditZoneHandler extends AbstractObjectModelHandler implements EditZoneHandlerInterface
{
    /**
     * {@inheritdoc}
     *
     * @throws ZoneException
     */
    public function handle(EditZoneCommand $command): void
    {
        try {
            $zone = new Zone($command->getZoneId()->getValue());
        } catch (PrestaShopException $e) {
            throw new ZoneException(sprintf('Failed to get zone with id "%d"', $command->getZoneId()->getValue()), 0, $e);
        }

        if ($zone->id !== $command->getZoneId()->getValue()) {
            throw new ZoneNotFoundException(sprintf('Zone with id "%d" was not found', $command->getZoneId()->getValue()));
        }

        if (null !== $command->getName()) {
            $zone->name = $command->getName();
        }

        if (null !== $command->isEnabled()) {
            $zone->active = $command->isEnabled();
        }

        try {
            if (!$zone->update()) {
                throw new CannotEditZoneException(sprintf('Cannot update zone with id "%d"', $zone->id));
            }

            if (null !== $command->getShopAssociation()) {
                $this->associateWithShops($zone, $command->getShopAssociation());
            }
        } catch (PrestaShopException) {
            throw new CannotEditZoneException(sprintf('Cannot update zone with id "%d"', $zone->id));
        }
    }
}
