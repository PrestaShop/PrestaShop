<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\Zone\CommandHandler;

use PrestaShop\PrestaShop\Adapter\Domain\AbstractObjectModelHandler;
use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsCommandHandler;
use PrestaShop\PrestaShop\Core\Domain\Zone\Command\AddZoneCommand;
use PrestaShop\PrestaShop\Core\Domain\Zone\CommandHandler\AddZoneHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Zone\Exception\MissingZoneRequiredFieldsException;
use PrestaShop\PrestaShop\Core\Domain\Zone\Exception\ZoneException;
use PrestaShop\PrestaShop\Core\Domain\Zone\ValueObject\ZoneId;
use PrestaShopException;
use Zone;

/**
 * Handles command that adds new zone.
 */
#[AsCommandHandler]
final class AddZoneHandler extends AbstractObjectModelHandler implements AddZoneHandlerInterface
{
    /**
     * {@inheritdoc}
     */
    public function handle(AddZoneCommand $command): ZoneId
    {
        $zone = new Zone();
        $zone->name = $command->getName();
        $zone->active = $command->isEnabled();

        try {
            $errors = $zone->validateFieldsRequiredDatabase();
            if (!empty($errors)) {
                $missingFields = array_keys($errors);

                throw new MissingZoneRequiredFieldsException($missingFields, sprintf('One or more required fields for zone are missing. Missing fields are: %s', implode(', ', $missingFields)));
            }

            if (!$zone->add()) {
                throw new ZoneException(sprintf('Failed to add new zone "%s"', $command->getName()));
            }

            $this->associateWithShops($zone, $command->getShopAssociation());
        } catch (PrestaShopException) {
            throw new ZoneException(sprintf('Failed to add new zone "%s"', $command->getName()));
        }

        return new ZoneId((int) $zone->id);
    }
}
