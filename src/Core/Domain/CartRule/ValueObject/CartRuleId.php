<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\CartRule\ValueObject;

use PrestaShop\PrestaShop\Core\Domain\CartRule\Exception\CartRuleConstraintException;

/**
 * Cart rule identity.
 */
class CartRuleId
{
    /**
     * @var int
     */
    private $cartRuleId;

    /**
     * @param int $cartRuleId
     */
    public function __construct(int $cartRuleId)
    {
        $this->assertIsPositiveInt($cartRuleId);
        $this->cartRuleId = $cartRuleId;
    }

    /**
     * @return int
     */
    public function getValue(): int
    {
        return $this->cartRuleId;
    }

    /**
     * @param int $value
     *
     * @throws CartRuleConstraintException
     */
    private function assertIsPositiveInt(int $value): void
    {
        if (0 > $value) {
            throw new CartRuleConstraintException(sprintf('Invalid cart rule id "%s".', $value), CartRuleConstraintException::INVALID_ID);
        }
    }
}
