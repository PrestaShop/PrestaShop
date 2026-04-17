<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\Manufacturer\Repository;

use PrestaShop\PrestaShop\Core\Domain\Manufacturer\Exception\ManufacturerNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\Manufacturer\ValueObject\ManufacturerId;
use PrestaShop\PrestaShop\Core\Repository\AbstractObjectModelRepository;

class ManufacturerRepository extends AbstractObjectModelRepository
{
    /**
     * @param ManufacturerId $manufacturerId
     *
     * @throws ManufacturerNotFoundException
     */
    public function assertManufacturerExists(ManufacturerId $manufacturerId): void
    {
        $this->assertObjectModelExists(
            $manufacturerId->getValue(),
            'manufacturer',
            ManufacturerNotFoundException::class
        );
    }
}
