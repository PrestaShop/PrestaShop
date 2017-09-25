<?php
/**
 * 2007-2017 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/OSL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2017 PrestaShop SA
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShop\PrestaShop\Core\Cart;

class CartRuleCollection implements \Iterator
{

    /**
     * @var CartRuleData[]
     */
    protected $cartRules        = array();
    protected $iteratorPosition = 0;

    public function addCartRule(CartRuleData $cartRule)
    {
        $this->cartRules[] = $cartRule;
    }

    public function rewind()
    {
        $this->iteratorPosition = 0;
    }

    /**
     * @return CartRuleData
     */
    public function current()
    {
        return $this->cartRules[$this->getKey($this->iteratorPosition)];
    }

    public function key()
    {
        return $this->getKey($this->iteratorPosition);
    }

    public function next()
    {
        ++$this->iteratorPosition;
    }

    public function valid()
    {
        return $this->getKey($this->iteratorPosition) !== null
               && array_key_exists(
                   $this->getKey($this->iteratorPosition),
                   $this->cartRules
               );
    }

    protected function getKey($iteratorPosition)
    {
        $keys = array_keys($this->cartRules);
        if (!isset($keys[$iteratorPosition])) {
            return null;
        } else {
            return $keys[$iteratorPosition];
        }
    }

}
