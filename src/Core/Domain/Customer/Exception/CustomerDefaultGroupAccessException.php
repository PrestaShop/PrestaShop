<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Customer\Exception;

/**
 * Exception is thrown when customer's default groups is not configured as access group.
 * This means that default group must also be configured as access group for customer.
 */
class CustomerDefaultGroupAccessException extends CustomerException
{
}
