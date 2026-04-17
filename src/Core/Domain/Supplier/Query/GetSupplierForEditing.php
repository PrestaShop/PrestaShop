<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */
declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Supplier\Query;

use PrestaShop\PrestaShop\Core\Domain\Supplier\Exception\SupplierException;
use PrestaShop\PrestaShop\Core\Domain\Supplier\ValueObject\SupplierId;

/**
 * Gets supplier for editing in Back Office
 */
class GetSupplierForEditing
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
    public function __construct(int $supplierId)
    {
        $this->supplierId = new SupplierId($supplierId);
    }

    /**
     * @return SupplierId $supplierId
     */
    public function getSupplierId(): SupplierId
    {
        return $this->supplierId;
    }
}
