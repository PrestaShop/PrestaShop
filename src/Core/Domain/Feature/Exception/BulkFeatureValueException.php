<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */
declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Feature\Exception;

use PrestaShop\PrestaShop\Core\Domain\Exception\BulkCommandExceptionInterface;
use Throwable;

class BulkFeatureValueException extends FeatureValueException implements BulkCommandExceptionInterface
{
    public const FAILED_BULK_DELETE = 1;

    /**
     * @param Throwable[] $exceptions
     * @param string $message
     * @param int $code
     * @param Throwable|null $previous
     */
    public function __construct(
        private readonly array $exceptions,
        string $message = 'Errors occurred during Feature value bulk action',
        int $code = 0,
        ?Throwable $previous = null
    ) {
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
