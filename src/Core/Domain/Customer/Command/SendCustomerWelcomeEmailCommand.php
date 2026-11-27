<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Customer\Command;

use PrestaShop\PrestaShop\Core\Domain\Customer\CommandHandler\SendCustomerWelcomeEmailHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Customer\CustomerAccountOrigin;
use PrestaShop\PrestaShop\Core\Domain\Customer\ValueObject\CustomerId;

/**
 * Sends the welcome email to the customer.
 *
 * @see SendCustomerWelcomeEmailHandlerInterface
 */
final class SendCustomerWelcomeEmailCommand
{
    public readonly CustomerId $customerId;

    public function __construct(
        int $customerId,
        public readonly CustomerAccountOrigin $origin = CustomerAccountOrigin::BACK_OFFICE,
    ) {
        $this->customerId = new CustomerId($customerId);
    }
}
