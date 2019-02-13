<?php
/**
 * 2007-2019 PrestaShop and Contributors
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
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
 * needs please refer to https://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2019 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShop\PrestaShop\Adapter\Manufacturer\CommandHandler;

use Manufacturer;
use PrestaShop\PrestaShop\Adapter\Entity\PrestaShopException;
use PrestaShop\PrestaShop\Adapter\Manufacturer\AbstractManufacturerHandler;
use PrestaShop\PrestaShop\Core\Domain\Manufacturer\Command\BulkToggleManufacturerStatusCommand;
use PrestaShop\PrestaShop\Core\Domain\Manufacturer\CommandHanlder\BulkToggleManufacturerStatusHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Manufacturer\Exception\ManufacturerException;
use PrestaShop\PrestaShop\Core\Domain\Manufacturer\Exception\UpdateManufacturerException;

/**
 * Handles command which toggles manufacturer status on bulk action
 */
final class BulkToggleManufacturerStatusHandler extends AbstractManufacturerHandler implements BulkToggleManufacturerStatusHandlerInterface
{
    /**
     * {@inheritdoc}
     */
    public function handle(BulkToggleManufacturerStatusCommand $command)
    {
        foreach ($command->getManufacturerIds() as $manufacturerId) {
            $manufacturerIdValue = $manufacturerId->getValue();
            $manufacturer = new Manufacturer($manufacturerIdValue);
            $this->assertManufacturerWasFound($manufacturerId, $manufacturer);
            $manufacturer->active = $command->getStatus()->isEnabled();

            try {
                if (!$manufacturer->save()) {
                    throw new UpdateManufacturerException(
                        sprintf('Unable to toggle manufacturer status with id "%s"', $manufacturerIdValue),
                        UpdateManufacturerException::FAILED_BULK_UPDATE_STATUS
                    );
                }
            } catch (PrestaShopException $e) {
                throw new ManufacturerException(sprintf(
                    'An error occurred when updating manufacturer status with id "%s"',
                    $manufacturerIdValue
                ));
            }
        }
    }
}
