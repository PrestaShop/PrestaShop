<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Carrier\Command;

use PrestaShop\PrestaShop\Core\Domain\Carrier\Exception\CarrierConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Carrier\ValueObject\CarrierId;

/**
 * Bulk deletes carriers
 */
class BulkDeleteCarrierCommand
{
    /**
     * @var CarrierId[]
     */
    private $carrierIds = [];

    /**
     * @param int[] $carrierIds
     *
     * @throws CarrierConstraintException
     */
    public function __construct(array $carrierIds)
    {
        $this->setCarrierIds($carrierIds);
    }

    /**
     * @return CarrierId[]
     */
    public function getCarrierIds(): array
    {
        return $this->carrierIds;
    }

    /**
     * @param array $carrierIds
     *
     * @throws CarrierConstraintException
     */
    private function setCarrierIds(array $carrierIds): void
    {
        foreach ($carrierIds as $carrierId) {
            $this->carrierIds[] = new CarrierId((int) $carrierId);
        }
    }
}
