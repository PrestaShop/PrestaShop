<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\CustomerService\CommandHandler;

use PrestaShop\PrestaShop\Core\Domain\CustomerService\Command\SyncCustomerServiceImapMailboxCommand;
use PrestaShop\PrestaShop\Core\Domain\CustomerService\QueryResult\ImapSyncResult;

/**
 * Interface for service that handles synchronizing the customer service IMAP mailbox
 */
interface SyncCustomerServiceImapMailboxHandlerInterface
{
    public function handle(SyncCustomerServiceImapMailboxCommand $command): ImapSyncResult;
}
