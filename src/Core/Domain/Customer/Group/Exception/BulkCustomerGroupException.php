<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Customer\Group\Exception;

use PrestaShop\PrestaShop\Core\Domain\Exception\BulkCommandExceptionInterface;
use Throwable;

final class BulkCustomerGroupException extends GroupException implements BulkCommandExceptionInterface
{
    public const FAILED_BULK_DELETE = 10;

    /**
     * @param Throwable[] $exceptions
     */
    public function __construct(
        private readonly array $exceptions,
        string $message = 'Errors occurred during customer group bulk action',
        int $code = 0,
        ?Throwable $previous = null
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
