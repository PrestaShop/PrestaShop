<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Customer\Exception;

/**
 * Exception is thrown when a customer is assigned a group that is not associated
 * with any of the shops the customer belongs to (multistore).
 */
class CustomerGroupShopAssociationException extends CustomerException
{
}
