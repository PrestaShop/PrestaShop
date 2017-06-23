<?php

namespace PrestaShopBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Stock
 *
 * @ORM\Table(indexes={@ORM\Index(name="id_warehouse", columns={"id_warehouse"}), @ORM\Index(name="id_product", columns={"id_product"}), @ORM\Index(name="id_product_attribute", columns={"id_product_attribute"})})
 * @ORM\Entity
 */
class Stock
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id_warehouse", type="integer", nullable=false)
     */
    private $idWarehouse;

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
     * @var string
     *
     * @ORM\Column(name="reference", type="string", length=32, nullable=false)
     */
    private $reference;

    /**
     * @var string
     *
     * @ORM\Column(name="ean13", type="string", length=13, nullable=true)
     */
    private $ean13;

    /**
     * @var string
     *
     * @ORM\Column(name="isbn", type="string", length=32, nullable=true)
     */
    private $isbn;

    /**
     * @var string
     *
     * @ORM\Column(name="upc", type="string", length=12, nullable=true)
     */
    private $upc;

    /**
     * @var integer
     *
     * @ORM\Column(name="physical_quantity", type="integer", nullable=false)
     */
    private $physicalQuantity;

    /**
     * @var integer
     *
     * @ORM\Column(name="usable_quantity", type="integer", nullable=false)
     */
    private $usableQuantity;

    /**
     * @var string
     *
     * @ORM\Column(name="price_te", type="decimal", precision=20, scale=6, nullable=true)
     */
    private $priceTe = '0.000000';

    /**
     * @var integer
     *
     * @ORM\Column(name="id_stock", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $idStock;



    /**
     * Set idWarehouse
     *
     * @param integer $idWarehouse
     *
     * @return Stock
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
     * Set idProduct
     *
     * @param integer $idProduct
     *
     * @return Stock
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
     * @return Stock
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
     * Set reference
     *
     * @param string $reference
     *
     * @return Stock
     */
    public function setReference($reference)
    {
        $this->reference = $reference;

        return $this;
    }

    /**
     * Get reference
     *
     * @return string
     */
    public function getReference()
    {
        return $this->reference;
    }

    /**
     * Set ean13
     *
     * @param string $ean13
     *
     * @return Stock
     */
    public function setEan13($ean13)
    {
        $this->ean13 = $ean13;

        return $this;
    }

    /**
     * Get ean13
     *
     * @return string
     */
    public function getEan13()
    {
        return $this->ean13;
    }

    /**
     * Set isbn
     *
     * @param string $isbn
     *
     * @return Stock
     */
    public function setIsbn($isbn)
    {
        $this->isbn = $isbn;

        return $this;
    }

    /**
     * Get isbn
     *
     * @return string
     */
    public function getIsbn()
    {
        return $this->isbn;
    }

    /**
     * Set upc
     *
     * @param string $upc
     *
     * @return Stock
     */
    public function setUpc($upc)
    {
        $this->upc = $upc;

        return $this;
    }

    /**
     * Get upc
     *
     * @return string
     */
    public function getUpc()
    {
        return $this->upc;
    }

    /**
     * Set physicalQuantity
     *
     * @param integer $physicalQuantity
     *
     * @return Stock
     */
    public function setPhysicalQuantity($physicalQuantity)
    {
        $this->physicalQuantity = $physicalQuantity;

        return $this;
    }

    /**
     * Get physicalQuantity
     *
     * @return integer
     */
    public function getPhysicalQuantity()
    {
        return $this->physicalQuantity;
    }

    /**
     * Set usableQuantity
     *
     * @param integer $usableQuantity
     *
     * @return Stock
     */
    public function setUsableQuantity($usableQuantity)
    {
        $this->usableQuantity = $usableQuantity;

        return $this;
    }

    /**
     * Get usableQuantity
     *
     * @return integer
     */
    public function getUsableQuantity()
    {
        return $this->usableQuantity;
    }

    /**
     * Set priceTe
     *
     * @param string $priceTe
     *
     * @return Stock
     */
    public function setPriceTe($priceTe)
    {
        $this->priceTe = $priceTe;

        return $this;
    }

    /**
     * Get priceTe
     *
     * @return string
     */
    public function getPriceTe()
    {
        return $this->priceTe;
    }

    /**
     * Get idStock
     *
     * @return integer
     */
    public function getIdStock()
    {
        return $this->idStock;
    }
}
