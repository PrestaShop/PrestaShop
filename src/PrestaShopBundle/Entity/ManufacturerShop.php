<?php

namespace PrestaShopBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ManufacturerShop
 *
 * @ORM\Table(indexes={@ORM\Index(name="id_shop", columns={"id_shop"})})
 * @ORM\Entity
 */
class ManufacturerShop
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id_manufacturer", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $idManufacturer;

    /**
     * @var integer
     *
     * @ORM\Column(name="id_shop", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $idShop;



    /**
     * Set idManufacturer
     *
     * @param integer $idManufacturer
     *
     * @return ManufacturerShop
     */
    public function setIdManufacturer($idManufacturer)
    {
        $this->idManufacturer = $idManufacturer;

        return $this;
    }

    /**
     * Get idManufacturer
     *
     * @return integer
     */
    public function getIdManufacturer()
    {
        return $this->idManufacturer;
    }

    /**
     * Set idShop
     *
     * @param integer $idShop
     *
     * @return ManufacturerShop
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
