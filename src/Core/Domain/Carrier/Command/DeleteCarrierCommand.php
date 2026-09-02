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
 * Deletes carrier
 */
class DeleteCarrierCommand
{
    /**
     * @var CarrierId
     */
    private $carrierId;

    /**
     * @param int $carrierId
     *
     * @throws CarrierConstraintException
     */
    public function __construct(int $carrierId)
    {
        $this->carrierId = new CarrierId($carrierId);
    }

    /**
     * @return CarrierId
     */
    public function getCarrierId(): CarrierId
    {
        return $this->carrierId;
    }
}
