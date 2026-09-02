<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Order\QueryHandler;

use PrestaShop\PrestaShop\Core\Domain\Order\Query\GetOrderForViewing;
use PrestaShop\PrestaShop\Core\Domain\Order\QueryResult\OrderForViewing;

interface GetOrderForViewingHandlerInterface
{
    /**
     * @param GetOrderForViewing $query
     *
     * @return OrderForViewing
     */
    public function handle(GetOrderForViewing $query): OrderForViewing;
}
