<?php

namespace PrestaShopBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * WarehouseCarrier
 *
 * @ORM\Table(indexes={@ORM\Index(name="id_warehouse", columns={"id_warehouse"}), @ORM\Index(name="id_carrier", columns={"id_carrier"})})
 * @ORM\Entity
 */
class WarehouseCarrier
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
     * @ORM\Column(name="id_carrier", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $idCarrier;



    /**
     * Set idWarehouse
     *
     * @param integer $idWarehouse
     *
     * @return WarehouseCarrier
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
     * Set idCarrier
     *
     * @param integer $idCarrier
     *
     * @return WarehouseCarrier
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
