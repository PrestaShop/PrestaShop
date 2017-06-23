<?php

namespace PrestaShopBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * CartRuleProductRuleValue
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class CartRuleProductRuleValue
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id_product_rule", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $idProductRule;

    /**
     * @var integer
     *
     * @ORM\Column(name="id_item", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $idItem;



    /**
     * Set idProductRule
     *
     * @param integer $idProductRule
     *
     * @return CartRuleProductRuleValue
     */
    public function setIdProductRule($idProductRule)
    {
        $this->idProductRule = $idProductRule;

        return $this;
    }

    /**
     * Get idProductRule
     *
     * @return integer
     */
    public function getIdProductRule()
    {
        return $this->idProductRule;
    }

    /**
     * Set idItem
     *
     * @param integer $idItem
     *
     * @return CartRuleProductRuleValue
     */
    public function setIdItem($idItem)
    {
        $this->idItem = $idItem;

        return $this;
    }

    /**
     * Get idItem
     *
     * @return integer
     */
    public function getIdItem()
    {
        return $this->idItem;
    }
}
