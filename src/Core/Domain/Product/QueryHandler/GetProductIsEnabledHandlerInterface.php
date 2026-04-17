<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Product\QueryHandler;

use PrestaShop\PrestaShop\Core\Domain\Product\Query\GetProductIsEnabled;

/**
 * Interface for service that handles getting product status.
 */
interface GetProductIsEnabledHandlerInterface
{
    /**
     * @param GetProductIsEnabled $query
     *
     * @return bool
     */
    public function handle(GetProductIsEnabled $query);
}
