<?php

namespace PrestaShopBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ProductSupplier
 *
 * @ORM\Table(uniqueConstraints={@ORM\UniqueConstraint(name="id_product", columns={"id_product", "id_product_attribute", "id_supplier"})}, indexes={@ORM\Index(name="id_supplier", columns={"id_supplier", "id_product"})})
 * @ORM\Entity
 */
class ProductSupplier
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
    private $idProductAttribute = '0';

    /**
     * @var integer
     *
     * @ORM\Column(name="id_supplier", type="integer", nullable=false)
     */
    private $idSupplier;

    /**
     * @var string
     *
     * @ORM\Column(name="product_supplier_reference", type="string", length=32, nullable=true)
     */
    private $productSupplierReference;

    /**
     * @var string
     *
     * @ORM\Column(name="product_supplier_price_te", type="decimal", precision=20, scale=6, nullable=false)
     */
    private $productSupplierPriceTe = '0.000000';

    /**
     * @var integer
     *
     * @ORM\Column(name="id_currency", type="integer", nullable=false)
     */
    private $idCurrency;

    /**
     * @var integer
     *
     * @ORM\Column(name="id_product_supplier", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $idProductSupplier;



    /**
     * Set idProduct
     *
     * @param integer $idProduct
     *
     * @return ProductSupplier
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
     * @return ProductSupplier
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
     * Set idSupplier
     *
     * @param integer $idSupplier
     *
     * @return ProductSupplier
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
     * Set productSupplierReference
     *
     * @param string $productSupplierReference
     *
     * @return ProductSupplier
     */
    public function setProductSupplierReference($productSupplierReference)
    {
        $this->productSupplierReference = $productSupplierReference;

        return $this;
    }

    /**
     * Get productSupplierReference
     *
     * @return string
     */
    public function getProductSupplierReference()
    {
        return $this->productSupplierReference;
    }

    /**
     * Set productSupplierPriceTe
     *
     * @param string $productSupplierPriceTe
     *
     * @return ProductSupplier
     */
    public function setProductSupplierPriceTe($productSupplierPriceTe)
    {
        $this->productSupplierPriceTe = $productSupplierPriceTe;

        return $this;
    }

    /**
     * Get productSupplierPriceTe
     *
     * @return string
     */
    public function getProductSupplierPriceTe()
    {
        return $this->productSupplierPriceTe;
    }

    /**
     * Set idCurrency
     *
     * @param integer $idCurrency
     *
     * @return ProductSupplier
     */
    public function setIdCurrency($idCurrency)
    {
        $this->idCurrency = $idCurrency;

        return $this;
    }

    /**
     * Get idCurrency
     *
     * @return integer
     */
    public function getIdCurrency()
    {
        return $this->idCurrency;
    }

    /**
     * Get idProductSupplier
     *
     * @return integer
     */
    public function getIdProductSupplier()
    {
        return $this->idProductSupplier;
    }
}
