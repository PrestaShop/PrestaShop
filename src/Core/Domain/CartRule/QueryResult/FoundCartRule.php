<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\CartRule\QueryResult;

/**
 * Holds data for cart rule found after search operation
 */
class FoundCartRule
{
    /**
     * @var int
     */
    private $cartRuleId;

    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $code;

    /**
     * @param int $cartRuleId
     * @param string $name
     * @param string $code
     */
    public function __construct(
        int $cartRuleId,
        string $name,
        string $code
    ) {
        $this->cartRuleId = $cartRuleId;
        $this->name = $name;
        $this->code = $code;
    }

    /**
     * @return int
     */
    public function getCartRuleId(): int
    {
        return $this->cartRuleId;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getCode(): string
    {
        return $this->code;
    }
}
