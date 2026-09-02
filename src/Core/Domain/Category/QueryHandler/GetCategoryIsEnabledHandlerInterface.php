<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Category\QueryHandler;

use PrestaShop\PrestaShop\Core\Domain\Category\Query\GetCategoryIsEnabled;

/**
 * Interface for service that handles getting category status.
 */
interface GetCategoryIsEnabledHandlerInterface
{
    /**
     * @param GetCategoryIsEnabled $query
     *
     * @return bool
     */
    public function handle(GetCategoryIsEnabled $query);
}
