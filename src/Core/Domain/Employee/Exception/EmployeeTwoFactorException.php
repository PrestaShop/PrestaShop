<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Employee\Exception;

final class EmployeeTwoFactorException extends EmployeeException
{
    public const NOT_SELF = 1;
    public const DISABLED = 2;
    public const NOT_CONFIGURED = 3;
    public const CONFIRMATION_REQUIRED = 4;
    public const INVALID_CODE = 5;
    public const CONCURRENT_UPDATE = 6;
    public const TOO_MANY_ATTEMPTS = 7;
    public const TOO_MANY_EMAILS = 8;
    public const EMAIL_DELIVERY_FAILED = 9;
}
