<?php

namespace PrestaShopBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * CartRuleCombination
 *
 * @ORM\Table(indexes={@ORM\Index(name="id_cart_rule_1", columns={"id_cart_rule_1"}), @ORM\Index(name="id_cart_rule_2", columns={"id_cart_rule_2"})})
 * @ORM\Entity
 */
class CartRuleCombination
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id_cart_rule_1", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $idCartRule1;

    /**
     * @var integer
     *
     * @ORM\Column(name="id_cart_rule_2", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $idCartRule2;



    /**
     * Set idCartRule1
     *
     * @param integer $idCartRule1
     *
     * @return CartRuleCombination
     */
    public function setIdCartRule1($idCartRule1)
    {
        $this->idCartRule1 = $idCartRule1;

        return $this;
    }

    /**
     * Get idCartRule1
     *
     * @return integer
     */
    public function getIdCartRule1()
    {
        return $this->idCartRule1;
    }

    /**
     * Set idCartRule2
     *
     * @param integer $idCartRule2
     *
     * @return CartRuleCombination
     */
    public function setIdCartRule2($idCartRule2)
    {
        $this->idCartRule2 = $idCartRule2;

        return $this;
    }

    /**
     * Get idCartRule2
     *
     * @return integer
     */
    public function getIdCartRule2()
    {
        return $this->idCartRule2;
    }
}
