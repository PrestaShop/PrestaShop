<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */
declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Product\Combination\Exception;

use PrestaShop\PrestaShop\Core\Domain\Product\Combination\ValueObject\CombinationId;

/**
 * Base class to use for bulk operations, it stores a list of exception indexed by the product ID that was impacted.
 * It should be used as a base class for all the bulk action exceptions.
 */
class BulkCombinationException extends CombinationException
{
    /**
     * @var array<int, CombinationException>
     */
    protected $bulkExceptions = [];

    /**
     * @param CombinationId $combinationId
     * @param CombinationException $exception
     */
    public function addException(CombinationId $combinationId, CombinationException $exception): void
    {
        $this->bulkExceptions[$combinationId->getValue()] = $exception;
    }

    /**
     * @return CombinationException[]
     */
    public function getBulkExceptions(): array
    {
        return $this->bulkExceptions;
    }

    /**
     * @return bool
     */
    public function isEmpty(): bool
    {
        return empty($this->bulkExceptions);
    }
}
