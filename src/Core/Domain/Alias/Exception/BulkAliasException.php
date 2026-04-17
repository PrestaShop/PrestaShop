<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Alias\Exception;

use PrestaShop\PrestaShop\Core\Domain\Exception\BulkCommandExceptionInterface;
use Throwable;

/**
 * Base class to use for bulk operations, it stores a list of exception indexed by the alias ID that was impacted.
 * It should be used as a base class for all the bulk action exceptions.
 */
class BulkAliasException extends AliasException implements BulkCommandExceptionInterface
{
    /**
     * @param Throwable[] $exceptions
     * @param string $message
     * @param int $code
     * @param Throwable|null $previous
     */
    public function __construct(
        private readonly array $exceptions,
        string $message = 'Errors occurred during Alias bulk action',
        int $code = 0,
        ?Throwable $previous = null
    ) {
        parent::__construct($message, $code, $previous);
    }

    /**
     * {@inheritdoc}
     */
    public function getExceptions(): array
    {
        return $this->exceptions;
    }
}
