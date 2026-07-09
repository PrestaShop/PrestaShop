<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\CustomerService\Command;

/**
 * Triggers a synchronization of the configured IMAP mailbox: fetches new messages,
 * matches them to existing customer threads via the `#ct<id> #tc<token>` subject
 * markers, and creates new threads for unrecognized senders when enabled.
 */
class SyncCustomerServiceImapMailboxCommand
{
}
