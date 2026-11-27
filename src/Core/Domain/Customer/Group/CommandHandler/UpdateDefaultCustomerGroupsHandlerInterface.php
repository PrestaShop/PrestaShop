<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Customer\Group\CommandHandler;

use PrestaShop\PrestaShop\Core\Domain\Customer\Group\Command\UpdateDefaultCustomerGroupsCommand;

interface UpdateDefaultCustomerGroupsHandlerInterface
{
    public function handle(UpdateDefaultCustomerGroupsCommand $command): void;
}
