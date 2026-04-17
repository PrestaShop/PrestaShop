<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Product\Supplier\Exception;

use Throwable;

/**
 * Thrown when bulk deleting product suppliers fails
 */
class CannotBulkDeleteProductSupplierException extends ProductSupplierException
{
    /**
     * @var int[]
     */
    private $productSupplierIds;

    /**
     * @param int[] $productSupplierIds ids of product supplier which cannot be deleted
     * @param string $message
     * @param int $code
     * @param Throwable|null $previous
     */
    public function __construct(array $productSupplierIds, $message = '', $code = 0, ?Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
        $this->productSupplierIds = $productSupplierIds;
    }

    /**
     * @return int[]
     */
    public function getProductSupplierIds(): array
    {
        return $this->productSupplierIds;
    }
}
