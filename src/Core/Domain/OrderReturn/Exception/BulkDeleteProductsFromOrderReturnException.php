<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\OrderReturn\Exception;

use PrestaShop\PrestaShop\Core\Domain\Exception\BulkCommandExceptionInterface;
use Throwable;

/**
 * Reports the per-row failures collected while running BulkDeleteProductsFromOrderReturnCommand.
 */
class BulkDeleteProductsFromOrderReturnException extends OrderReturnException implements BulkCommandExceptionInterface
{
    /**
     * @var Throwable[]
     */
    private $exceptions;

    /**
     * @param Throwable[] $exceptions
     */
    public function __construct(array $exceptions)
    {
        parent::__construct('Failed to delete some products from the order return.');
        $this->exceptions = $exceptions;
    }

    /**
     * {@inheritdoc}
     */
    public function getExceptions(): array
    {
        return $this->exceptions;
    }
}
