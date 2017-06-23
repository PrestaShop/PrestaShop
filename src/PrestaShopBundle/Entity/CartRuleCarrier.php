<?php

namespace PrestaShopBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * CartRuleCarrier
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class CartRuleCarrier
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id_cart_rule", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $idCartRule;

    /**
     * @var integer
     *
     * @ORM\Column(name="id_carrier", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $idCarrier;



    /**
     * Set idCartRule
     *
     * @param integer $idCartRule
     *
     * @return CartRuleCarrier
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
     * Set idCarrier
     *
     * @param integer $idCarrier
     *
     * @return CartRuleCarrier
     */
    public function setIdCarrier($idCarrier)
    {
        $this->idCarrier = $idCarrier;

        return $this;
    }

    /**
     * Get idCarrier
     *
     * @return integer
     */
    public function getIdCarrier()
    {
        return $this->idCarrier;
    }
}
