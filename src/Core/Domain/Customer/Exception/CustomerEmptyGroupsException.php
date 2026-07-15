<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Customer\Exception;

/**
 * Exception is thrown when a customer is saved without any access group.
 * A customer must belong to at least one group.
 */
class CustomerEmptyGroupsException extends CustomerException
{
}
