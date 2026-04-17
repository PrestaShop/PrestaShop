<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Adapter\Manufacturer\CommandHandler;

use Manufacturer;
use PrestaShop\PrestaShop\Adapter\Manufacturer\AbstractManufacturerHandler;
use PrestaShop\PrestaShop\Core\Domain\Manufacturer\Exception\ManufacturerException;
use PrestaShopException;

/**
 * Provides reusable methods for manufacturer command handlers
 */
abstract class AbstractManufacturerCommandHandler extends AbstractManufacturerHandler
{
    /**
     * Deletes legacy Manufacturer
     *
     * @param Manufacturer $manufacturer
     *
     * @return bool
     *
     * @throws ManufacturerException
     */
    protected function deleteManufacturer(Manufacturer $manufacturer)
    {
        try {
            return $manufacturer->delete();
        } catch (PrestaShopException) {
            throw new ManufacturerException(sprintf('An error occurred when deleting Manufacturer object with id "%s".', $manufacturer->id));
        }
    }

    /**
     * Toggles legacy manufacturer status
     *
     * @param Manufacturer $manufacturer
     * @param bool $newStatus
     *
     * @return bool
     *
     * @throws ManufacturerException
     */
    protected function toggleManufacturerStatus(Manufacturer $manufacturer, $newStatus)
    {
        $manufacturer->active = $newStatus;

        try {
            return $manufacturer->save();
        } catch (PrestaShopException) {
            throw new ManufacturerException(sprintf('An error occurred when updating manufacturer status with id "%s"', $manufacturer->id));
        }
    }
}
