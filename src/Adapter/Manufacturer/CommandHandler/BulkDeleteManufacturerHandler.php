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
use PrestaShop\PrestaShop\Adapter\Manufacturer\AbstractManufacturerHandler;
use PrestaShop\PrestaShop\Core\Domain\Manufacturer\Command\BulkDeleteManufacturerCommand;
use PrestaShop\PrestaShop\Core\Domain\Manufacturer\CommandHanlder\BulkDeleteManufacturerHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Manufacturer\Exception\DeleteManufacturerException;
use PrestaShop\PrestaShop\Core\Domain\Manufacturer\Exception\ManufacturerException;
use PrestaShopException;

/**
 * Handles command which deletes manufacturers on bulk action
 */
final class BulkDeleteManufacturerHandler extends AbstractManufacturerHandler implements BulkDeleteManufacturerHandlerInterface
{
    /**
     * {@inheritdoc}
     */
    public function handle(BulkDeleteManufacturerCommand $command)
    {
        foreach ($command->getManufacturerIds() as $manufacturerId) {
            $manufacturerIdValue = $manufacturerId->getValue();
            $manufacturer = new Manufacturer($manufacturerIdValue);
            $this->assertManufacturerWasFound($manufacturerId, $manufacturer);

            try {
                if (!$manufacturer->delete()) {
                    throw new DeleteManufacturerException(
                        sprintf('Cannot delete Manufacturer object with id "%s".', $manufacturerIdValue)
                    );
                }
            } catch (PrestaShopException $e) {
                throw new ManufacturerException(sprintf(
                    'An error occurred when deleting Manufacturer object with id "%s".',
                    $manufacturerIdValue
                ));
            }
        }
    }
}
