<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Product\Supplier\QueryResult;

/**
 * Transfers product associated suppliers minimal data, only the list of suppliers IDs and the default one.
 */
class AssociatedSuppliers
{
    /**
     * @var int
     */
    private $defaultSupplierId;

    /**
     * @var int[]
     */
    private $supplierIds;

    /**
     * @param int $defaultSupplierId
     * @param int[] $supplierIds
     */
    public function __construct(int $defaultSupplierId, array $supplierIds)
    {
        $this->defaultSupplierId = $defaultSupplierId;
        $this->supplierIds = $supplierIds;
    }

    /**
     * @return int
     */
    public function getDefaultSupplierId(): int
    {
        return $this->defaultSupplierId;
    }

    /**
     * @return int[]
     */
    public function getSupplierIds(): array
    {
        return $this->supplierIds;
    }
}
