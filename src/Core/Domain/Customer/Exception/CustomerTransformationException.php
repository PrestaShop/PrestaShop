<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Customer\Exception;

/**
 * Is thrown when customer transformation error occurs
 */
class CustomerTransformationException extends CustomerException
{
    /**
     * @var int Code is used when customer which is not guest is being transformed into customer
     */
    public const CUSTOMER_IS_NOT_GUEST = 1;

    /**
     * @var int Code is used when guest transformation into customer has failed
     */
    public const TRANSFORMATION_FAILED = 2;
}
