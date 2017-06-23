<?php

namespace PrestaShopBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * CarrierTaxRulesGroupShop
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class CarrierTaxRulesGroupShop
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id_carrier", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $idCarrier;

    /**
     * @var integer
     *
     * @ORM\Column(name="id_tax_rules_group", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $idTaxRulesGroup;

    /**
     * @var integer
     *
     * @ORM\Column(name="id_shop", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $idShop;



    /**
     * Set idCarrier
     *
     * @param integer $idCarrier
     *
     * @return CarrierTaxRulesGroupShop
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

    /**
     * Set idTaxRulesGroup
     *
     * @param integer $idTaxRulesGroup
     *
     * @return CarrierTaxRulesGroupShop
     */
    public function setIdTaxRulesGroup($idTaxRulesGroup)
    {
        $this->idTaxRulesGroup = $idTaxRulesGroup;

        return $this;
    }

    /**
     * Get idTaxRulesGroup
     *
     * @return integer
     */
    public function getIdTaxRulesGroup()
    {
        return $this->idTaxRulesGroup;
    }

    /**
     * Set idShop
     *
     * @param integer $idShop
     *
     * @return CarrierTaxRulesGroupShop
     */
    public function setIdShop($idShop)
    {
        $this->idShop = $idShop;

        return $this;
    }

    /**
     * Get idShop
     *
     * @return integer
     */
    public function getIdShop()
    {
        return $this->idShop;
    }
}
