<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */
declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Product\SpecificPrice\ValueObject;

use PrestaShop\PrestaShop\Core\Domain\Product\SpecificPrice\Exception\SpecificPriceConstraintException;

class SpecificPriceId
{
    /**
     * @var int
     */
    private $specificPriceId;

    /**
     * @param int $specificPriceId
     *
     * @throws SpecificPriceConstraintException
     */
    public function __construct(int $specificPriceId)
    {
        $this->assertIsGreaterThanZero($specificPriceId);
        $this->specificPriceId = $specificPriceId;
    }

    /**
     * @return int
     */
    public function getValue(): int
    {
        return $this->specificPriceId;
    }

    /**
     * Validates that the value is greater than zero
     *
     * @param int $value
     *
     * @throws SpecificPriceConstraintException
     */
    private function assertIsGreaterThanZero(int $value): void
    {
        if (0 >= $value) {
            throw new SpecificPriceConstraintException(sprintf('Invalid specific price id "%s".', $value), SpecificPriceConstraintException::INVALID_ID);
        }
    }
}
