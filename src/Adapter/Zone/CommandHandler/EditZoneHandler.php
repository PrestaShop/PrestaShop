<?php
/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/OSL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to https://devdocs.prestashop-project.org/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\Zone\CommandHandler;

use PrestaShop\PrestaShop\Adapter\Domain\AbstractObjectModelHandler;
use PrestaShop\PrestaShop\Core\Domain\Zone\Command\EditZoneCommand;
use PrestaShop\PrestaShop\Core\Domain\Zone\CommandHandler\EditZoneHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Zone\Exception\CannotEditZoneException;
use PrestaShop\PrestaShop\Core\Domain\Zone\Exception\ZoneException;
use PrestaShop\PrestaShop\Core\Domain\Zone\Exception\ZoneNotFoundException;
use PrestaShopException;
use Zone;

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
        } catch (PrestaShopException $e) {
            throw new CannotEditZoneException(sprintf('Cannot update zone with id "%d"', $zone->id));
        }
    }
}
