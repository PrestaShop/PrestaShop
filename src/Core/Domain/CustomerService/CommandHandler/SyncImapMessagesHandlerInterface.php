<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\CustomerService\CommandHandler;

use PrestaShop\PrestaShop\Core\Domain\CustomerService\Command\SyncImapMessagesCommand;
use PrestaShop\PrestaShop\Core\Domain\CustomerService\QueryResult\ImapSyncResult;

interface SyncImapMessagesHandlerInterface
{
    public function handle(SyncImapMessagesCommand $command): ImapSyncResult;
}
