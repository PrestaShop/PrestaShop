<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\BusinessEntity\Exception;

use PrestaShop\PrestaShop\Core\Domain\Exception\BulkCommandExceptionInterface;
use Throwable;

/**
 * Aggregates the per-item failures caught while processing a BulkDeleteBusinessEntityCommand,
 * so a single failing id does not stop the batch.
 */
class BulkDeleteBusinessEntityException extends BusinessEntityException implements BulkCommandExceptionInterface
{
    /**
     * @param Throwable[] $exceptions
     */
    public function __construct(
        private readonly array $exceptions,
        string $message = 'Errors occurred during business entity bulk delete action',
    ) {
        parent::__construct($message);
    }

    /**
     * {@inheritdoc}
     */
    public function getExceptions(): array
    {
        return $this->exceptions;
    }
}
