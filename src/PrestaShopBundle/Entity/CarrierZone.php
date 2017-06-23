<?php

namespace PrestaShopBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * CarrierZone
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class CarrierZone
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
     * @ORM\Column(name="id_zone", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $idZone;



    /**
     * Set idCarrier
     *
     * @param integer $idCarrier
     *
     * @return CarrierZone
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
     * Set idZone
     *
     * @param integer $idZone
     *
     * @return CarrierZone
     */
    public function setIdZone($idZone)
    {
        $this->idZone = $idZone;

        return $this;
    }

    /**
     * Get idZone
     *
     * @return integer
     */
    public function getIdZone()
    {
        return $this->idZone;
    }
}
