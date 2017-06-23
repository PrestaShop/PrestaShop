<?php

namespace PrestaShopBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ProductAttributeShop
 *
 * @ORM\Table(uniqueConstraints={@ORM\UniqueConstraint(name="id_product", columns={"id_product", "id_shop", "default_on"})})
 * @ORM\Entity
 */
class ProductAttributeShop
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
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $idProductAttribute;

    /**
     * @var integer
     *
     * @ORM\Column(name="id_shop", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $idShop;



    /**
     * Set idProduct
     *
     * @param integer $idProduct
     *
     * @return ProductAttributeShop
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
     * Set wholesalePrice
     *
     * @param string $wholesalePrice
     *
     * @return ProductAttributeShop
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
     * @return ProductAttributeShop
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
     * @return ProductAttributeShop
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
     * Set weight
     *
     * @param string $weight
     *
     * @return ProductAttributeShop
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
     * @return ProductAttributeShop
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
     * @return ProductAttributeShop
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
     * @return ProductAttributeShop
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
     * @return ProductAttributeShop
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
     * Set idProductAttribute
     *
     * @param integer $idProductAttribute
     *
     * @return ProductAttributeShop
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
     * Set idShop
     *
     * @param integer $idShop
     *
     * @return ProductAttributeShop
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
