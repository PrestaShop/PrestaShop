<?php

namespace PrestaShopBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * WarehouseShop
 *
 * @ORM\Table(indexes={@ORM\Index(name="id_warehouse", columns={"id_warehouse"}), @ORM\Index(name="id_shop", columns={"id_shop"})})
 * @ORM\Entity
 */
class WarehouseShop
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id_warehouse", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $idWarehouse;

    /**
     * @var integer
     *
     * @ORM\Column(name="id_shop", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $idShop;



    /**
     * Set idWarehouse
     *
     * @param integer $idWarehouse
     *
     * @return WarehouseShop
     */
    public function setIdWarehouse($idWarehouse)
    {
        $this->idWarehouse = $idWarehouse;

        return $this;
    }

    /**
     * Get idWarehouse
     *
     * @return integer
     */
    public function getIdWarehouse()
    {
        return $this->idWarehouse;
    }

    /**
     * Set idShop
     *
     * @param integer $idShop
     *
     * @return WarehouseShop
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
