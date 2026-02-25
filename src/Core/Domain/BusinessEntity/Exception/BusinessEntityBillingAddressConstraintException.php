<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\BusinessEntity\Exception;

use Exception;

class BusinessEntityBillingAddressConstraintException extends Exception
{
    public const MISSING_BILLING_ADDRESS = 1;
    public const MISSING_DEFAULT_BILLING_ADDRESS = 2;
    public const MISSING_SHIPPING_ADDRESS = 3;
    public const MISSING_DEFAULT_SHIPPING_ADDRESS = 4;
}
