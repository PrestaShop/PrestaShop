<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Customer\Group\Exception;

use Exception;
use PrestaShop\PrestaShop\Core\Domain\Exception\BulkCommandExceptionInterface;
use Throwable;

class BulkDeleteCustomerGroupException extends GroupException implements BulkCommandExceptionInterface
{
    /** @var Throwable[] */
    private array $exceptions;

    /**
     * @param Throwable[] $exceptions
     */
    public function __construct(array $exceptions, string $message = '', int $code = 0, ?Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);

        $this->exceptions = $exceptions;
    }

    /** @return Throwable[] */
    public function getExceptions(): array
    {
        return $this->exceptions;
    }
}
