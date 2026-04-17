<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Supplier\Command;

use PrestaShop\PrestaShop\Core\Domain\Supplier\Exception\SupplierConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Supplier\Exception\SupplierException;
use PrestaShop\PrestaShop\Core\Domain\Supplier\ValueObject\SupplierId;

/**
 * Class BulkDisableSupplierCommand is responsible for disabling multiple suppliers.
 */
class BulkDisableSupplierCommand extends AbstractBulkSupplierCommand
{
    /**
     * @var SupplierId[]
     */
    private $supplierIds;

    /**
     * @param int[] $supplierIds
     *
     * @throws SupplierException
     * @throws SupplierConstraintException
     */
    public function __construct(array $supplierIds)
    {
        if ($this->assertIsEmptyOrContainsNonIntegerValues($supplierIds)) {
            throw new SupplierConstraintException(sprintf('Missing supplier data or array %s contains non integer values for bulk disable', var_export($supplierIds, true)), SupplierConstraintException::INVALID_BULK_DATA);
        }

        $this->setSupplierIds($supplierIds);
    }

    /**
     * @return SupplierId[]
     */
    public function getSupplierIds()
    {
        return $this->supplierIds;
    }

    /**
     * @param array $supplierIds
     *
     * @throws SupplierException
     */
    private function setSupplierIds(array $supplierIds)
    {
        foreach ($supplierIds as $id) {
            $this->supplierIds[] = new SupplierId($id);
        }
    }
}
