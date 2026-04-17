<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */
declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Customer\Group\CommandHandler;

use PrestaShop\PrestaShop\Core\Domain\Customer\Group\Command\AddCustomerGroupCommand;
use PrestaShop\PrestaShop\Core\Domain\Customer\Group\ValueObject\GroupId;

interface AddCustomerGroupHandlerInterface
{
    /**
     * @param AddCustomerGroupCommand $command
     *
     * @return GroupId
     */
    public function handle(AddCustomerGroupCommand $command): GroupId;
}
