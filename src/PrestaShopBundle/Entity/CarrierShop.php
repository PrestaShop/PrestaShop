<?php

namespace PrestaShopBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * CarrierShop
 *
 * @ORM\Table(indexes={@ORM\Index(name="id_shop", columns={"id_shop"})})
 * @ORM\Entity
 */
class CarrierShop
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
     * @return CarrierShop
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
     * Set idShop
     *
     * @param integer $idShop
     *
     * @return CarrierShop
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
