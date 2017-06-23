<?php

namespace PrestaShopBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ProductAttribute
 *
 * @ORM\Table(uniqueConstraints={@ORM\UniqueConstraint(name="product_default", columns={"id_product", "default_on"})}, indexes={@ORM\Index(name="product_attribute_product", columns={"id_product"}), @ORM\Index(name="reference", columns={"reference"}), @ORM\Index(name="supplier_reference", columns={"supplier_reference"}), @ORM\Index(name="id_product_id_product_attribute", columns={"id_product_attribute", "id_product"})})
 * @ORM\Entity
 */
class ProductAttribute
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id_product", type="integer", nullable=false)
     */
    private $idProduct;

    /**
     * @var string
     *
     * @ORM\Column(name="reference", type="string", length=32, nullable=true)
     */
    private $reference;

    /**
     * @var string
     *
     * @ORM\Column(name="supplier_reference", type="string", length=32, nullable=true)
     */
    private $supplierReference;

    /**
     * @var string
     *
     * @ORM\Column(name="location", type="string", length=64, nullable=true)
     */
    private $location;

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
     * @var string
     *
     * @ORM\Column(name="wholesale_price", type="decimal", precision=20, scale=6, nullable=false)
     */
    private $wholesalePrice = '0.000000';

    /**
     * @var string
     *
     * @ORM\Column(name="price", type="decimal", precision=20, scale=6, nullable=false)
     */
    private $price = '0.000000';

    /**
     * @var string
     *
     * @ORM\Column(name="ecotax", type="decimal", precision=17, scale=6, nullable=false)
     */
    private $ecotax = '0.000000';

    /**
     * @var integer
     *
     * @ORM\Column(name="quantity", type="integer", nullable=false)
     */
    private $quantity = '0';

    /**
     * @var string
     *
     * @ORM\Column(name="weight", type="decimal", precision=20, scale=6, nullable=false)
     */
    private $weight = '0.000000';

    /**
     * @var string
     *
     * @ORM\Column(name="unit_price_impact", type="decimal", precision=20, scale=6, nullable=false)
     */
    private $unitPriceImpact = '0.000000';

    /**
     * @var boolean
     *
     * @ORM\Column(name="default_on", type="boolean", nullable=true)
     */
    private $defaultOn;

    /**
     * @var integer
     *
     * @ORM\Column(name="minimal_quantity", type="integer", nullable=false)
     */
    private $minimalQuantity = '1';

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="available_date", type="date", nullable=true)
     */
    private $availableDate;

    /**
     * @var integer
     *
     * @ORM\Column(name="id_product_attribute", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $idProductAttribute;



    /**
     * Set idProduct
     *
     * @param integer $idProduct
     *
     * @return ProductAttribute
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
     * Set reference
     *
     * @param string $reference
     *
     * @return ProductAttribute
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
     * Set supplierReference
     *
     * @param string $supplierReference
     *
     * @return ProductAttribute
     */
    public function setSupplierReference($supplierReference)
    {
        $this->supplierReference = $supplierReference;

        return $this;
    }

    /**
     * Get supplierReference
     *
     * @return string
     */
    public function getSupplierReference()
    {
        return $this->supplierReference;
    }

    /**
     * Set location
     *
     * @param string $location
     *
     * @return ProductAttribute
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
     * Set ean13
     *
     * @param string $ean13
     *
     * @return ProductAttribute
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
     * @return ProductAttribute
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
     * @return ProductAttribute
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
     * Set wholesalePrice
     *
     * @param string $wholesalePrice
     *
     * @return ProductAttribute
     */
    public function setWholesalePrice($wholesalePrice)
    {
        $this->wholesalePrice = $wholesalePrice;

        return $this;
    }

    /**
     * Get wholesalePrice
     *
     * @return string
     */
    public function getWholesalePrice()
    {
        return $this->wholesalePrice;
    }

    /**
     * Set price
     *
     * @param string $price
     *
     * @return ProductAttribute
     */
    public function setPrice($price)
    {
        $this->price = $price;

        return $this;
    }

    /**
     * Get price
     *
     * @return string
     */
    public function getPrice()
    {
        return $this->price;
    }

    /**
     * Set ecotax
     *
     * @param string $ecotax
     *
     * @return ProductAttribute
     */
    public function setEcotax($ecotax)
    {
        $this->ecotax = $ecotax;

        return $this;
    }

    /**
     * Get ecotax
     *
     * @return string
     */
    public function getEcotax()
    {
        return $this->ecotax;
    }

    /**
     * Set quantity
     *
     * @param integer $quantity
     *
     * @return ProductAttribute
     */
    public function setQuantity($quantity)
    {
        $this->quantity = $quantity;

        return $this;
    }

    /**
     * Get quantity
     *
     * @return integer
     */
    public function getQuantity()
    {
        return $this->quantity;
    }

    /**
     * Set weight
     *
     * @param string $weight
     *
     * @return ProductAttribute
     */
    public function setWeight($weight)
    {
        $this->weight = $weight;

        return $this;
    }

    /**
     * Get weight
     *
     * @return string
     */
    public function getWeight()
    {
        return $this->weight;
    }

    /**
     * Set unitPriceImpact
     *
     * @param string $unitPriceImpact
     *
     * @return ProductAttribute
     */
    public function setUnitPriceImpact($unitPriceImpact)
    {
        $this->unitPriceImpact = $unitPriceImpact;

        return $this;
    }

    /**
     * Get unitPriceImpact
     *
     * @return string
     */
    public function getUnitPriceImpact()
    {
        return $this->unitPriceImpact;
    }

    /**
     * Set defaultOn
     *
     * @param boolean $defaultOn
     *
     * @return ProductAttribute
     */
    public function setDefaultOn($defaultOn)
    {
        $this->defaultOn = $defaultOn;

        return $this;
    }

    /**
     * Get defaultOn
     *
     * @return boolean
     */
    public function getDefaultOn()
    {
        return $this->defaultOn;
    }

    /**
     * Set minimalQuantity
     *
     * @param integer $minimalQuantity
     *
     * @return ProductAttribute
     */
    public function setMinimalQuantity($minimalQuantity)
    {
        $this->minimalQuantity = $minimalQuantity;

        return $this;
    }

    /**
     * Get minimalQuantity
     *
     * @return integer
     */
    public function getMinimalQuantity()
    {
        return $this->minimalQuantity;
    }

    /**
     * Set availableDate
     *
     * @param \DateTime $availableDate
     *
     * @return ProductAttribute
     */
    public function setAvailableDate($availableDate)
    {
        $this->availableDate = $availableDate;

        return $this;
    }

    /**
     * Get availableDate
     *
     * @return \DateTime
     */
    public function getAvailableDate()
    {
        return $this->availableDate;
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
}
