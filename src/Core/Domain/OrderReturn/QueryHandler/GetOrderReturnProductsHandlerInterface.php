<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\OrderReturn\QueryHandler;

use PrestaShop\PrestaShop\Core\Domain\OrderReturn\Query\GetOrderReturnProducts;
use PrestaShop\PrestaShop\Core\Domain\OrderReturn\QueryResult\OrderReturnProductForEditing;

/**
 * Returns the list of returned product rows for the merchandise return edit page.
 */
interface GetOrderReturnProductsHandlerInterface
{
    /**
     * @return OrderReturnProductForEditing[]
     */
    public function handle(GetOrderReturnProducts $query): array;
}
