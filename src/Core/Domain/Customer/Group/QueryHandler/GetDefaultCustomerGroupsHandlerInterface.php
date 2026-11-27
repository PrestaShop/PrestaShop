<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Customer\Group\QueryHandler;

use PrestaShop\PrestaShop\Core\Domain\Customer\Group\Query\GetDefaultCustomerGroups;
use PrestaShop\PrestaShop\Core\Domain\Customer\Group\QueryResult\DefaultCustomerGroups;

interface GetDefaultCustomerGroupsHandlerInterface
{
    public function handle(GetDefaultCustomerGroups $query): DefaultCustomerGroups;
}
