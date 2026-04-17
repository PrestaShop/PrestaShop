<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Product\Exception;

use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\ProductId;

/**
 * Base class to use for bulk operations, it stores a list of exception indexed by the product ID that was impacted.
 * It should be used as a base class for all the bulk action exceptions.
 */
class BulkProductException extends ProductException
{
    /**
     * @var array<int, ProductException>
     */
    protected $bulkExceptions = [];

    public function addException(ProductId $productId, ProductException $exception): void
    {
        $this->bulkExceptions[$productId->getValue()] = $exception;
    }

    /**
     * @return ProductException[]
     */
    public function getBulkExceptions(): array
    {
        return $this->bulkExceptions;
    }
}
