<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Discount\ValueObject;

use PrestaShop\PrestaShop\Core\Domain\Discount\Exception\DiscountConstraintException;

class DiscountId
{
    private int $discountId;

    public function __construct(int $discountId)
    {
        $this->assertIsPositiveInt($discountId);
        $this->discountId = $discountId;
    }

    public function getValue(): int
    {
        return $this->discountId;
    }

    /**
     * @param int $value
     *
     * @throws DiscountConstraintException
     */
    private function assertIsPositiveInt(int $value): void
    {
        if (0 >= $value) {
            throw new DiscountConstraintException(sprintf('Invalid discount id "%s".', $value), DiscountConstraintException::INVALID_ID);
        }
    }
}
