<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Customer\CommandHandler;

use PrestaShop\PrestaShop\Core\Domain\Customer\Command\SendCustomerPasswordSetupLinkCommand;

interface SendCustomerPasswordSetupLinkHandlerInterface
{
    public function handle(SendCustomerPasswordSetupLinkCommand $command): void;
}
