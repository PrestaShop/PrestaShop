<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */
declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Discount\Exception;

use PrestaShop\PrestaShop\Core\Domain\Exception\BulkCommandExceptionInterface;
use Throwable;

class BulkDiscountException extends DiscountException implements BulkCommandExceptionInterface
{
    public const FAILED_BULK_DELETE = 1;
    public const FAILED_BULK_UPDATE_STATUS = 2;

    /**
     * @var Throwable[]
     */
    private array $exceptions;

    /**
     * @param Throwable[] $exceptions
     * @param string $message
     * @param int $code
     * @param Throwable|null $previous
     */
    public function __construct(
        array $exceptions,
        string $message = 'Errors occurred during discount bulk action',
        int $code = 0,
        ?Throwable $previous = null
    ) {
        $this->exceptions = $exceptions;
        parent::__construct($message, $code, $previous);
    }

    /**
     * {@inheritDoc}
     */
    public function getExceptions(): array
    {
        return $this->exceptions;
    }
}
