<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\WithdrawalRequest\ValueObject;

use PrestaShop\PrestaShop\Core\Domain\WithdrawalRequest\Exception\WithdrawalRequestConstraintException;

/**
 * Identity of a withdrawal request.
 */
final class WithdrawalRequestId
{
    private int $value;

    /**
     * @throws WithdrawalRequestConstraintException
     */
    public function __construct(int $value)
    {
        if (0 >= $value) {
            throw new WithdrawalRequestConstraintException(
                sprintf('Invalid withdrawal request id "%d".', $value),
                WithdrawalRequestConstraintException::INVALID_ID
            );
        }

        $this->value = $value;
    }

    public function getValue(): int
    {
        return $this->value;
    }
}
