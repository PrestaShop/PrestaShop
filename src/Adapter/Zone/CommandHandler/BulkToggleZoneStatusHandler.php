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
 * needs please refer to https://devdocs.prestashop.com/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\Zone\CommandHandler;

use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsCommandHandler;
use PrestaShop\PrestaShop\Core\Domain\Zone\Command\BulkToggleZoneStatusCommand;
use PrestaShop\PrestaShop\Core\Domain\Zone\CommandHandler\BulkToggleZoneStatusHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Zone\Exception\CannotToggleZoneStatusException;
use PrestaShop\PrestaShop\Core\Domain\Zone\Exception\ZoneException;
use PrestaShop\PrestaShop\Core\Domain\Zone\Exception\ZoneNotFoundException;
use PrestaShopException;
use Zone;

/**
 * Handles command that toggles zones status in bulk action
 */
#[AsCommandHandler]
final class BulkToggleZoneStatusHandler implements BulkToggleZoneStatusHandlerInterface
{
    /**
     * {@inheritdoc}
     */
    public function handle(BulkToggleZoneStatusCommand $command): void
    {
        foreach ($command->getZoneIds() as $zoneId) {
            $zone = new Zone($zoneId->getValue());

            if (0 >= $zone->id) {
                throw new ZoneNotFoundException(sprintf('Zone object with id "%d" has been not found for status changing', $zoneId->getValue()));
            }

            $zone->active = $command->getExpectedStatus();

            try {
                if (!$zone->save()) {
                    throw new CannotToggleZoneStatusException(sprintf('Unable to toggle status for zone with id "%d"', $zoneId->getValue()));
                }
            } catch (PrestaShopException $e) {
                throw new ZoneException(sprintf('An error occurred while updating zone status with id "%d"', $zoneId->getValue()));
            }
        }
    }
}
