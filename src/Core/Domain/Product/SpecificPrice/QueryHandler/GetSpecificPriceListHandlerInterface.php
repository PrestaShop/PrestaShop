<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Product\SpecificPrice\QueryHandler;

use PrestaShop\PrestaShop\Core\Domain\Product\SpecificPrice\Query\GetSpecificPriceList;
use PrestaShop\PrestaShop\Core\Domain\Product\SpecificPrice\QueryResult\SpecificPriceList;

interface GetSpecificPriceListHandlerInterface
{
    /**
     * @param GetSpecificPriceList $query
     *
     * @return SpecificPriceList
     */
    public function handle(GetSpecificPriceList $query): SpecificPriceList;
}
