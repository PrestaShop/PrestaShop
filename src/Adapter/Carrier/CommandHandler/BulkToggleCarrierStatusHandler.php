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

namespace PrestaShop\PrestaShop\Adapter\Carrier\CommandHandler;

use PrestaShop\PrestaShop\Adapter\Carrier\AbstractCarrierHandler;
use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsCommandHandler;
use PrestaShop\PrestaShop\Core\Domain\Carrier\Command\BulkToggleCarrierStatusCommand;
use PrestaShop\PrestaShop\Core\Domain\Carrier\CommandHandler\BulkToggleCarrierStatusHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Carrier\Exception\CannotToggleCarrierStatusException;
use PrestaShop\PrestaShop\Core\Domain\Carrier\Exception\CarrierException;
use PrestaShopException;

/**
 * Bulk toggles carriers status
 */
#[AsCommandHandler]
class BulkToggleCarrierStatusHandler extends AbstractCarrierHandler implements BulkToggleCarrierStatusHandlerInterface
{
    /**
     * {@inheritdoc}
     */
    public function handle(BulkToggleCarrierStatusCommand $command)
    {
        foreach ($command->getCarrierIds() as $carrierId) {
            $carrier = $this->getCarrier($carrierId);
            $carrier->active = $command->getExpectedStatus();

            try {
                if (!$carrier->save()) {
                    throw new CannotToggleCarrierStatusException(sprintf('Cannot toggle status of carrier with id "%d"', $carrierId->getValue()), CannotToggleCarrierStatusException::BULK_TOGGLE);
                }
            } catch (PrestaShopException $e) {
                throw new CarrierException(sprintf('An error occurred when updating status of carrier with id "%d"', $carrierId->getValue()));
            }
        }
    }
}
