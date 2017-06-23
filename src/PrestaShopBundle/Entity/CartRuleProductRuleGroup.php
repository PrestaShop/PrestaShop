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


namespace PrestaShopBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * CartRuleProductRuleGroup
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class CartRuleProductRuleGroup
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id_cart_rule", type="integer", nullable=false)
     */
    private $idCartRule;

    /**
     * @var integer
     *
     * @ORM\Column(name="quantity", type="integer", nullable=false)
     */
    private $quantity = '1';

    /**
     * @var integer
     *
     * @ORM\Column(name="id_product_rule_group", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $idProductRuleGroup;



    /**
     * Set idCartRule
     *
     * @param integer $idCartRule
     *
     * @return CartRuleProductRuleGroup
     */
    public function setIdCartRule($idCartRule)
    {
        $this->idCartRule = $idCartRule;

        return $this;
    }

    /**
     * Get idCartRule
     *
     * @return integer
     */
    public function getIdCartRule()
    {
        return $this->idCartRule;
    }

    /**
     * Set quantity
     *
     * @param integer $quantity
     *
     * @return CartRuleProductRuleGroup
     */
    public function setQuantity($quantity)
    {
        $this->quantity = $quantity;

        return $this;
    }

    /**
     * Get quantity
     *
     * @return integer
     */
    public function getQuantity()
    {
        return $this->quantity;
    }

    /**
     * Get idProductRuleGroup
     *
     * @return integer
     */
    public function getIdProductRuleGroup()
    {
        return $this->idProductRuleGroup;
    }
}
