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

use PrestaShop\PrestaShop\Core\Domain\Zone\Command\BulkDeleteZoneCommand;
use PrestaShop\PrestaShop\Core\Domain\Zone\CommandHandler\BulkDeleteZoneHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Zone\Exception\DeleteZoneException;
use PrestaShop\PrestaShop\Core\Domain\Zone\Exception\ZoneNotFoundException;
use Zone;

/**
 * Handles command that bulk delete zones
 */
final class BulkDeleteZoneHandler implements BulkDeleteZoneHandlerInterface
{
    /**
     * {@inheritdoc}
     */
    public function handle(BulkDeleteZoneCommand $command): void
    {
        foreach ($command->getZoneIds() as $zoneId) {
            $zone = new Zone($zoneId->getValue());

            if (0 >= $zone->id) {
                throw new ZoneNotFoundException(sprintf('Unable to find zone with id "%d" for deletion', $zoneId->getValue()));
            }

            if (!$zone->delete()) {
                throw new DeleteZoneException(sprintf('An error occurred when deleting zone with id "%d"', $zoneId->getValue()), DeleteZoneException::FAILED_BULK_DELETE);
            }
        }
    }
}
