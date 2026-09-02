<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Hook\QueryHandler;

use PrestaShop\PrestaShop\Core\Domain\Hook\Query\GetHookStatus;
use PrestaShop\PrestaShop\Core\Domain\Hook\QueryResult\HookStatus;

/**
 * Interface for service that handles getting hook status.
 */
interface GetHookStatusHandlerInterface
{
    /**
     * @param GetHookStatus $query
     *
     * @return HookStatus
     */
    public function handle(GetHookStatus $query);
}
