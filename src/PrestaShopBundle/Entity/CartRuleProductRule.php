<?php

namespace PrestaShopBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * CartRuleProductRule
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class CartRuleProductRule
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id_product_rule_group", type="integer", nullable=false)
     */
    private $idProductRuleGroup;

    /**
     * @var string
     *
     * @ORM\Column(name="type", type="string", nullable=false)
     */
    private $type;

    /**
     * @var integer
     *
     * @ORM\Column(name="id_product_rule", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $idProductRule;



    /**
     * Set idProductRuleGroup
     *
     * @param integer $idProductRuleGroup
     *
     * @return CartRuleProductRule
     */
    public function setIdProductRuleGroup($idProductRuleGroup)
    {
        $this->idProductRuleGroup = $idProductRuleGroup;

        return $this;
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

    /**
     * Set type
     *
     * @param string $type
     *
     * @return CartRuleProductRule
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
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
}
