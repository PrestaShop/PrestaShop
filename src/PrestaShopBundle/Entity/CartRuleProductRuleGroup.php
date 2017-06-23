<?php

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
