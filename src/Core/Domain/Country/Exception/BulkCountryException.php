<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Country\Exception;

use PrestaShop\PrestaShop\Core\Domain\Exception\BulkCommandExceptionInterface;
use Throwable;

final class BulkCountryException extends CountryException implements BulkCommandExceptionInterface
{
    public const FAILED_BULK_UPDATE_STATUS = 1;
    public const FAILED_BULK_UPDATE_ZONE = 2;

    /**
     * @param Throwable[] $exceptions
     * @param string $message
     * @param int $code
     * @param Throwable|null $previous
     */
    public function __construct(
        private readonly array $exceptions,
        string $message = 'Errors occurred during country bulk action',
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
