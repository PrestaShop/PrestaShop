<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Order\QueryHandler;

use PrestaShop\PrestaShop\Core\Domain\Order\Query\GetOrderPreview;
use PrestaShop\PrestaShop\Core\Domain\Order\QueryResult\OrderPreview;

/**
 * Defines contract for getOrderPreview query handler
 */
interface GetOrderPreviewHandlerInterface
{
    /**
     * @param GetOrderPreview $query
     *
     * @return OrderPreview
     */
    public function handle(GetOrderPreview $query): OrderPreview;
}
