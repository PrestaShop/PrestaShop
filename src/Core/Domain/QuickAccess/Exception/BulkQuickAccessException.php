<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\QuickAccess\Exception;

use PrestaShop\PrestaShop\Core\Domain\Exception\BulkCommandExceptionInterface;
use Throwable;

class BulkQuickAccessException extends QuickAccessException implements BulkCommandExceptionInterface
{
    /** @param Throwable[] $exceptions */
    public function __construct(
        private readonly array $exceptions,
        string $message = 'Errors occurred during Quick Access bulk action',
        int $code = 0,
        ?Throwable $previous = null
    ) {
        parent::__construct($message, $code, $previous);
    }

    public function getExceptions(): array
    {
        return $this->exceptions;
    }
}
