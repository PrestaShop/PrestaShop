<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\CustomerService\Exception;

use PrestaShop\PrestaShop\Core\Domain\Exception\DomainException;

/**
 * Base exception for customer service subdomain
 */
class CustomerServiceException extends DomainException
{
    /**
     * Code is used when there is an issue with creating customer message
     */
    public const FAILED_TO_ADD_CUSTOMER_MESSAGE = 10;

    /**
     * Code is used when there is an issue when updating customers thread status
     */
    public const FAILED_TO_UPDATE_STATUS = 20;

    /**
     * Code is used when comment is invalid
     */
    public const INVALID_COMMENT = 30;
}
