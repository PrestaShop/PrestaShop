<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\BusinessEntity\QueryHandler;

use PrestaShop\PrestaShop\Core\Domain\BusinessEntity\Query\GetPendingBusinessEntitiesCount;

interface GetPendingBusinessEntitiesCountHandlerInterface
{
    public function handle(GetPendingBusinessEntitiesCount $query): int;
}
