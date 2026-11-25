<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Customer;

enum CustomerAccountOrigin: string
{
    /**
     * An account the customer created by registering in the Front Office, including a guest who set a password.
     */
    case CUSTOMER_REGISTRATION = 'customer_registration';
    /**
     * A new account created on behalf of the customer, e.g. by an employee in the Back Office.
     */
    case BACK_OFFICE = 'back_office';
    /**
     * A guest account converted into a customer account without a password.
     */
    case GUEST_TO_CUSTOMER = 'guest_to_customer';

    public function wasPasswordSetByCustomer(): bool
    {
        return self::CUSTOMER_REGISTRATION === $this;
    }
}
