<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Cart\QueryResult\CartForOrderCreation;

/**
 * Holds cart rule (a.k.a voucher) data for cart information
 */
class CartRule
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
    private $description;

    /**
     * @var string
     */
    private $value;

    /**
     * @param int $cartRuleId
     * @param string $name
     * @param string $description
     * @param string $value
     */
    public function __construct(
        int $cartRuleId,
        string $name,
        string $description,
        string $value
    ) {
        $this->cartRuleId = $cartRuleId;
        $this->name = $name;
        $this->description = $description;
        $this->value = $value;
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
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * @return string
     */
    public function getValue(): string
    {
        return $this->value;
    }
}
