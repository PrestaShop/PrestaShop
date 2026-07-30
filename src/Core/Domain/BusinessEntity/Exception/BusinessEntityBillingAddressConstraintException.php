<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\BusinessEntity\Exception;

class BusinessEntityBillingAddressConstraintException extends BusinessEntityException
{
    public const MISSING_BILLING_ADDRESS = 1;
    public const MISSING_DEFAULT_BILLING_ADDRESS = 2;
    public const MISSING_SHIPPING_ADDRESS = 3;
    public const MISSING_DEFAULT_SHIPPING_ADDRESS = 4;
    public const MULTIPLE_DEFAULT_BILLING_ADDRESSES = 5;
    public const MULTIPLE_DEFAULT_SHIPPING_ADDRESSES = 6;
}
