<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Cart;

use CartRule;

class CartRuleData
{
    protected $ruleData = [];

    /**
     * @var AmountImmutable
     */
    protected $discountApplied;

    public function __construct($rowData)
    {
        $this->setRuleData($rowData);
        $this->discountApplied = new AmountImmutable();
    }

    /**
     * @param array $ruleData
     *
     * @return static
     */
    public function setRuleData($ruleData)
    {
        $this->ruleData = $ruleData;

        return $this;
    }

    /**
     * @return array
     */
    public function getRuleData()
    {
        return $this->ruleData;
    }

    /**
     * @return CartRule
     */
    public function getCartRule()
    {
        $cartRuleData = $this->getRuleData();

        return $cartRuleData['obj'];
    }

    public function addDiscountApplied(AmountImmutable $amount)
    {
        $this->discountApplied = $this->discountApplied->add($amount);
    }

    /**
     * @return AmountImmutable
     */
    public function getDiscountApplied()
    {
        return $this->discountApplied;
    }
}
