<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Currency\QueryHandler;

use PrestaShop\PrestaShop\Core\Domain\Currency\Query\GetReferenceCurrency;
use PrestaShop\PrestaShop\Core\Domain\Currency\QueryResult\ReferenceCurrency;

/**
 * Interface GetReferenceCurrencyHandlerInterface defines contract for GetReferenceCurrencyHandler.
 */
interface GetReferenceCurrencyHandlerInterface
{
    /**
     * @param GetReferenceCurrency $query
     *
     * @return ReferenceCurrency
     */
    public function handle(GetReferenceCurrency $query): ReferenceCurrency;
}
