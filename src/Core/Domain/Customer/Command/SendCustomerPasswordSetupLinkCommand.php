<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Customer\Command;

use PrestaShop\PrestaShop\Core\Domain\Customer\CommandHandler\SendCustomerPasswordSetupLinkHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Customer\ValueObject\CustomerId;

/**
 * Sends an email with a password setup link to the customer.
 *
 * @see SendCustomerPasswordSetupLinkHandlerInterface
 */
final class SendCustomerPasswordSetupLinkCommand
{
    public readonly CustomerId $customerId;

    public function __construct(int $customerId)
    {
        $this->customerId = new CustomerId($customerId);
    }
}
