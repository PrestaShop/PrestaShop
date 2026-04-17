<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Adapter\Manufacturer;

use Manufacturer;
use PrestaShop\PrestaShop\Adapter\Domain\AbstractObjectModelHandler;
use PrestaShop\PrestaShop\Core\Domain\Manufacturer\Exception\ManufacturerException;
use PrestaShop\PrestaShop\Core\Domain\Manufacturer\Exception\ManufacturerNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\Manufacturer\ValueObject\ManufacturerId;
use PrestaShopException;

/**
 * Provides reusable methods for manufacturer command/query handlers
 */
abstract class AbstractManufacturerHandler extends AbstractObjectModelHandler
{
    /**
     * Gets legacy Manufacturer
     *
     * @param ManufacturerId $manufacturerId
     *
     * @return Manufacturer
     *
     * @throws ManufacturerException
     */
    protected function getManufacturer(ManufacturerId $manufacturerId)
    {
        try {
            $manufacturer = new Manufacturer($manufacturerId->getValue());
        } catch (PrestaShopException $e) {
            throw new ManufacturerException('Failed to create new manufacturer', 0, $e);
        }

        if ($manufacturer->id !== $manufacturerId->getValue()) {
            throw new ManufacturerNotFoundException(sprintf('Manufacturer with id "%s" was not found.', $manufacturerId->getValue()));
        }

        return $manufacturer;
    }
}
