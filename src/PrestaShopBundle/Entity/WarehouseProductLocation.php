<?php

namespace PrestaShopBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * WarehouseProductLocation
 *
 * @ORM\Table(uniqueConstraints={@ORM\UniqueConstraint(name="id_product", columns={"id_product", "id_product_attribute", "id_warehouse"})})
 * @ORM\Entity
 */
class WarehouseProductLocation
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id_product", type="integer", nullable=false)
     */
    private $idProduct;

    /**
     * @var integer
     *
     * @ORM\Column(name="id_product_attribute", type="integer", nullable=false)
     */
    private $idProductAttribute;

    /**
     * @var integer
     *
     * @ORM\Column(name="id_warehouse", type="integer", nullable=false)
     */
    private $idWarehouse;

    /**
     * @var string
     *
     * @ORM\Column(name="location", type="string", length=64, nullable=true)
     */
    private $location;

    /**
     * @var integer
     *
     * @ORM\Column(name="id_warehouse_product_location", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $idWarehouseProductLocation;



    /**
     * Set idProduct
     *
     * @param integer $idProduct
     *
     * @return WarehouseProductLocation
     */
    public function setIdProduct($idProduct)
    {
        $this->idProduct = $idProduct;

        return $this;
    }

    /**
     * Get idProduct
     *
     * @return integer
     */
    public function getIdProduct()
    {
        return $this->idProduct;
    }

    /**
     * Set idProductAttribute
     *
     * @param integer $idProductAttribute
     *
     * @return WarehouseProductLocation
     */
    public function setIdProductAttribute($idProductAttribute)
    {
        $this->idProductAttribute = $idProductAttribute;

        return $this;
    }

    /**
     * Get idProductAttribute
     *
     * @return integer
     */
    public function getIdProductAttribute()
    {
        return $this->idProductAttribute;
    }

    /**
     * Set idWarehouse
     *
     * @param integer $idWarehouse
     *
     * @return WarehouseProductLocation
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
     * Set location
     *
     * @param string $location
     *
     * @return WarehouseProductLocation
     */
    public function setLocation($location)
    {
        $this->location = $location;

        return $this;
    }

    /**
     * Get location
     *
     * @return string
     */
    public function getLocation()
    {
        return $this->location;
    }

    /**
     * Get idWarehouseProductLocation
     *
     * @return integer
     */
    public function getIdWarehouseProductLocation()
    {
        return $this->idWarehouseProductLocation;
    }
}
