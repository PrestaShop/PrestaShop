<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Search\Exception;

use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\ProductId;
use Throwable;

class SearchIndexationProductNotFoundException extends SearchIndexationInvalidContextException
{
    protected ProductId $productId;

    public function __construct(ProductId $productId, string $message = '', int $code = 0, ?Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);

        $this->productId = $productId;
    }

    public function getProductId(): ProductId
    {
        return $this->productId;
    }
}
