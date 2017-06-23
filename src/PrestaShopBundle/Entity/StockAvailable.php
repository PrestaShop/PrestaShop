<?php

namespace PrestaShopBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * StockAvailable
 *
 * @ORM\Table(uniqueConstraints={@ORM\UniqueConstraint(name="product_sqlstock", columns={"id_product", "id_product_attribute", "id_shop", "id_shop_group"})}, indexes={@ORM\Index(name="id_shop", columns={"id_shop"}), @ORM\Index(name="id_shop_group", columns={"id_shop_group"}), @ORM\Index(name="id_product", columns={"id_product"}), @ORM\Index(name="id_product_attribute", columns={"id_product_attribute"})})
 * @ORM\Entity
 */
class StockAvailable
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
     * @ORM\Column(name="id_shop", type="integer", nullable=false)
     */
    private $idShop;

    /**
     * @var integer
     *
     * @ORM\Column(name="id_shop_group", type="integer", nullable=false)
     */
    private $idShopGroup;

    /**
     * @var integer
     *
     * @ORM\Column(name="quantity", type="integer", nullable=false)
     */
    private $quantity = '0';

    /**
     * @var integer
     *
     * @ORM\Column(name="physical_quantity", type="integer", nullable=false)
     */
    private $physicalQuantity = '0';

    /**
     * @var integer
     *
     * @ORM\Column(name="reserved_quantity", type="integer", nullable=false)
     */
    private $reservedQuantity = '0';

    /**
     * @var boolean
     *
     * @ORM\Column(name="depends_on_stock", type="boolean", nullable=false)
     */
    private $dependsOnStock = '0';

    /**
     * @var boolean
     *
     * @ORM\Column(name="out_of_stock", type="boolean", nullable=false)
     */
    private $outOfStock = '0';

    /**
     * @var integer
     *
     * @ORM\Column(name="id_stock_available", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $idStockAvailable;



    /**
     * Set idProduct
     *
     * @param integer $idProduct
     *
     * @return StockAvailable
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
     * @return StockAvailable
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
     * @return StockAvailable
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

    /**
     * Set idShopGroup
     *
     * @param integer $idShopGroup
     *
     * @return StockAvailable
     */
    public function setIdShopGroup($idShopGroup)
    {
        $this->idShopGroup = $idShopGroup;

        return $this;
    }

    /**
     * Get idShopGroup
     *
     * @return integer
     */
    public function getIdShopGroup()
    {
        return $this->idShopGroup;
    }

    /**
     * Set quantity
     *
     * @param integer $quantity
     *
     * @return StockAvailable
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
     * Set physicalQuantity
     *
     * @param integer $physicalQuantity
     *
     * @return StockAvailable
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
     * Set reservedQuantity
     *
     * @param integer $reservedQuantity
     *
     * @return StockAvailable
     */
    public function setReservedQuantity($reservedQuantity)
    {
        $this->reservedQuantity = $reservedQuantity;

        return $this;
    }

    /**
     * Get reservedQuantity
     *
     * @return integer
     */
    public function getReservedQuantity()
    {
        return $this->reservedQuantity;
    }

    /**
     * Set dependsOnStock
     *
     * @param boolean $dependsOnStock
     *
     * @return StockAvailable
     */
    public function setDependsOnStock($dependsOnStock)
    {
        $this->dependsOnStock = $dependsOnStock;

        return $this;
    }

    /**
     * Get dependsOnStock
     *
     * @return boolean
     */
    public function getDependsOnStock()
    {
        return $this->dependsOnStock;
    }

    /**
     * Set outOfStock
     *
     * @param boolean $outOfStock
     *
     * @return StockAvailable
     */
    public function setOutOfStock($outOfStock)
    {
        $this->outOfStock = $outOfStock;

        return $this;
    }

    /**
     * Get outOfStock
     *
     * @return boolean
     */
    public function getOutOfStock()
    {
        return $this->outOfStock;
    }

    /**
     * Get idStockAvailable
     *
     * @return integer
     */
    public function getIdStockAvailable()
    {
        return $this->idStockAvailable;
    }
}
