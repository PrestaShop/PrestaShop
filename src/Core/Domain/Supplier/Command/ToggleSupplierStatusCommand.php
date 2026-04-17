<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Supplier\Command;

use PrestaShop\PrestaShop\Core\Domain\Supplier\Exception\SupplierException;
use PrestaShop\PrestaShop\Core\Domain\Supplier\ValueObject\SupplierId;

/**
 * Class ToggleSupplierStatusCommand is responsible for toggling supplier status.
 */
class ToggleSupplierStatusCommand
{
    /**
     * @var SupplierId
     */
    private $supplierId;

    /**
     * @param int $supplierId
     *
     * @throws SupplierException
     */
    public function __construct($supplierId)
    {
        $this->supplierId = new SupplierId($supplierId);
    }

    /**
     * @return SupplierId
     */
    public function getSupplierId()
    {
        return $this->supplierId;
    }
}
