<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Manufacturer\Command;

use PrestaShop\PrestaShop\Core\Domain\Manufacturer\Exception\ManufacturerConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Manufacturer\ValueObject\ManufacturerId;

/**
 * Deletes manufacturer logo image
 */
class DeleteManufacturerLogoImageCommand
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
    public function __construct(int $manufacturerId)
    {
        $this->manufacturerId = new ManufacturerId($manufacturerId);
    }

    /**
     * @return ManufacturerId
     */
    public function getManufacturerId(): ManufacturerId
    {
        return $this->manufacturerId;
    }
}
