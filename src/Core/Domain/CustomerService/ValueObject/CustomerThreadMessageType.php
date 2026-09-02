<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\CustomerService\ValueObject;

/**
 * Defines types of thread messages
 */
final class CustomerThreadMessageType
{
    /**
     * When message is written by employee
     */
    public const EMPLOYEE = 'employee';

    /**
     * When message is written by customer
     */
    public const CUSTOMER = 'customer';
}
