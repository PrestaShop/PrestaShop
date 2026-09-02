<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Exception;

use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopConstraint;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Throwable;

/**
 * Exception thrown when trying to perform a multi shop action that doesn't fit
 * with the authorized shops.
 */
class MultiShopAccessDeniedException extends CoreException implements HttpExceptionInterface
{
    public function __construct(
        private readonly ?ShopConstraint $shopConstraint = null,
        string $message = '',
        int $code = 0,
        ?Throwable $previous = null
    ) {
        parent::__construct($message, $code, $previous);
    }

    public function getShopConstraint(): ?ShopConstraint
    {
        return $this->shopConstraint;
    }

    public function getStatusCode(): int
    {
        return 403;
    }

    public function getHeaders(): array
    {
        return [];
    }
}
