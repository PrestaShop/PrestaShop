<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Discount\QueryHandler;

use PrestaShop\PrestaShop\Core\Domain\Discount\Query\GetDiscountTypes;
use PrestaShop\PrestaShop\Core\Domain\Discount\QueryResult\DiscountType;

interface GetDiscountTypesHandlerInterface
{
    /**
     * @return DiscountType[]
     */
    public function handle(GetDiscountTypes $query): array;
}
