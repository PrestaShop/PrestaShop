<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Manufacturer\Query;

use PrestaShop\PrestaShop\Core\Domain\Manufacturer\Exception\ManufacturerConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Manufacturer\ValueObject\ManufacturerId;

/**
 * Gets manufacturer for editing in Back Office
 */
class GetManufacturerForEditing
{
    /**
     * @var ManufacturerId
     */
    private $manufacturerId;

    /**
     * @param int $manufacturerId
     *
     * @throws ManufacturerConstraintException
     */
    public function __construct($manufacturerId)
    {
        $this->manufacturerId = new ManufacturerId($manufacturerId);
    }

    /**
     * @return ManufacturerId $manufacturerId
     */
    public function getManufacturerId()
    {
        return $this->manufacturerId;
    }
}
