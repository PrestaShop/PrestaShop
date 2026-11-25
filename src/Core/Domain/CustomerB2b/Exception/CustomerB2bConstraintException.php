<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\CustomerB2b\Exception;

class CustomerB2bConstraintException extends CustomerB2bException
{
    public const INVALID_ID = 1;
    public const INVALID_EXTERNAL_REF = 2;
    public const GUEST_CUSTOMER = 3;
    public const DELETED_CUSTOMER = 4;
    public const BUSINESS_ENTITY_SHOP_MISMATCH = 5;
}
