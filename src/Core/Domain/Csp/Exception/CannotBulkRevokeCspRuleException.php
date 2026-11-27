<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Csp\Exception;

use PrestaShop\PrestaShop\Core\Domain\Exception\BulkCommandExceptionInterface;
use Throwable;

/** Thrown when a bulk revoke could not revoke every rule; carries the per-rule exceptions that were caught. */
final class CannotBulkRevokeCspRuleException extends CspException implements BulkCommandExceptionInterface
{
    /**
     * @param Throwable[] $exceptions
     */
    public function __construct(
        private readonly array $exceptions,
        string $message = '',
        int $code = 0,
        ?Throwable $previous = null,
    ) {
        parent::__construct($message, $code, $previous);
    }

    /**
     * @return Throwable[]
     */
    public function getExceptions(): array
    {
        return $this->exceptions;
    }
}
