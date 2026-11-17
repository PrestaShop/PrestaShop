<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\WithdrawalRequest\Exception;

/**
 * Thrown when a withdrawal request field holds an invalid value.
 */
final class WithdrawalRequestConstraintException extends WithdrawalRequestException
{
    public const INVALID_ID = 1;
    public const INVALID_FIRSTNAME = 2;
    public const INVALID_LASTNAME = 3;
    public const INVALID_EMAIL = 4;
    public const INVALID_MESSAGE = 5;
    public const INVALID_STATUS = 6;
    public const INVALID_PERIOD = 7;
}
