<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\CreditSlip\ValueObject;

use PrestaShop\PrestaShop\Core\Domain\CreditSlip\Exception\CreditSlipConstraintException;

/**
 * Provides identification data for Credit slip
 */
final class CreditSlipId
{
    /**
     * @var int
     */
    private $creditSlipId;

    /**
     * @param int $creditSlipId
     *
     * @throws CreditSlipConstraintException
     */
    public function __construct($creditSlipId)
    {
        $this->assertIsIntegerGreaterThanZero($creditSlipId);
        $this->creditSlipId = $creditSlipId;
    }

    /**
     * @return int
     */
    public function getValue()
    {
        return $this->creditSlipId;
    }

    /**
     * Validates that the value is integer and is greater than zero
     *
     * @param int $value
     *
     * @throws CreditSlipConstraintException
     */
    private function assertIsIntegerGreaterThanZero($value)
    {
        if (!is_int($value) || 0 >= $value) {
            throw new CreditSlipConstraintException(sprintf('Invalid credit slip id "%s".', var_export($value, true)));
        }
    }
}
