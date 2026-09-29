<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Csp\ValueObject;

use PrestaShop\PrestaShop\Core\Domain\Csp\Exception\CspConstraintException;

/**
 * Identity of a curated CSP allow-list rule.
 */
final class CspRuleId
{
    public function __construct(private readonly int $value)
    {
        if ($value <= 0) {
            throw new CspConstraintException(sprintf('Invalid CSP rule id "%d".', $value), CspConstraintException::INVALID_ID);
        }
    }

    public function getValue(): int
    {
        return $this->value;
    }
}
