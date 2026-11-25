<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Customer\Exception;

class CustomerWelcomeEmailConstraintException extends CustomerConstraintException
{
    public const GUEST_CUSTOMER = 101;
    public const DELETED_CUSTOMER = 102;
    public const INACTIVE_CUSTOMER = 103;
}
