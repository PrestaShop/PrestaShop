<?php

namespace PrestaShopBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * SupplierShop
 *
 * @ORM\Table(indexes={@ORM\Index(name="id_shop", columns={"id_shop"})})
 * @ORM\Entity
 */
class SupplierShop
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id_supplier", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $idSupplier;

    /**
     * @var integer
     *
     * @ORM\Column(name="id_shop", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $idShop;



    /**
     * Set idSupplier
     *
     * @param integer $idSupplier
     *
     * @return SupplierShop
     */
    public function setIdSupplier($idSupplier)
    {
        $this->idSupplier = $idSupplier;

        return $this;
    }

    /**
     * Get idSupplier
     *
     * @return integer
     */
    public function getIdSupplier()
    {
        return $this->idSupplier;
    }

    /**
     * Set idShop
     *
     * @param integer $idShop
     *
     * @return SupplierShop
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
