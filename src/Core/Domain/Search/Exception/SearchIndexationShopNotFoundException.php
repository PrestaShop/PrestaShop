<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Search\Exception;

use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopId;
use Throwable;

class SearchIndexationShopNotFoundException extends SearchIndexationInvalidContextException
{
    public ShopId $shopId;

    public function __construct(ShopId $shopId, string $message = '', int $code = 0, ?Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);

        $this->shopId = $shopId;
    }

    public function getShopId(): ShopId
    {
        return $this->shopId;
    }
}
