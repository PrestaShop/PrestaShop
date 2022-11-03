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

use PrestaShop\PrestaShop\Core\Domain\Zone\Command\ToggleZoneStatusCommand;
use PrestaShop\PrestaShop\Core\Domain\Zone\CommandHandler\ToggleZoneStatusHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Zone\Exception\CannotToggleZoneStatusException;
use PrestaShop\PrestaShop\Core\Domain\Zone\Exception\ZoneException;
use PrestaShop\PrestaShop\Core\Domain\Zone\Exception\ZoneNotFoundException;
use PrestaShopException;
use Zone;

final class ToggleZoneStatusHandler implements ToggleZoneStatusHandlerInterface
{
    /**
     * {@inheritdoc}
     *
     * @throws ZoneException
     */
    public function handle(ToggleZoneStatusCommand $command): void
    {
        try {
            $zone = new Zone($command->getZoneId()->getValue());

            if (0 >= $zone->id) {
                throw new ZoneNotFoundException(sprintf('Zone object with id "%d" has been not found for status changing', $command->getZoneId()->getValue()));
            }

            if (false === $zone->toggleStatus()) {
                throw new CannotToggleZoneStatusException(sprintf('Unable to toggle status of zone with id "%d"', $command->getZoneId()->getValue()));
            }
        } catch (PrestaShopException $e) {
            throw new ZoneException(sprintf('An error occurred when toggling status for zone with id "%d"', $command->getZoneId()->getValue()));
        }
    }
}
