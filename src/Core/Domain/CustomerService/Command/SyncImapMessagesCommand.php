<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\CustomerService\Command;

/**
 * Triggers a single IMAP synchronisation pass: connect to the configured
 * mailbox, import every new message that hasn't been processed yet, and
 * either create matching customer threads or append the message to an
 * existing one. The handler returns an `ImapSyncResult` describing what
 * happened so the caller can surface errors to the merchant.
 */
final class SyncImapMessagesCommand
{
}
