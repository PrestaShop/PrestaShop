<?php
/**
 * 2007-2017 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/OSL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2017 PrestaShop SA
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */


namespace PrestaShopBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * SupplyOrderDetail
 *
 * @ORM\Table(indexes={@ORM\Index(name="id_supply_order", columns={"id_supply_order", "id_product"}), @ORM\Index(name="id_product_attribute", columns={"id_product_attribute"}), @ORM\Index(name="id_product_product_attribute", columns={"id_product", "id_product_attribute"})})
 * @ORM\Entity
 */
class SupplyOrderDetail
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id_supply_order", type="integer", nullable=false)
     */
    private $idSupplyOrder;

    /**
     * @var integer
     *
     * @ORM\Column(name="id_currency", type="integer", nullable=false)
     */
    private $idCurrency;

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
     * @ORM\Column(name="supplier_reference", type="string", length=32, nullable=false)
     */
    private $supplierReference;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=128, nullable=false)
     */
    private $name;

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
     * @ORM\Column(name="exchange_rate", type="decimal", precision=20, scale=6, nullable=true, options={"default":0.000000})
     */
    private $exchangeRate = '0.000000';

    /**
     * @var string
     *
     * @ORM\Column(name="unit_price_te", type="decimal", precision=20, scale=6, nullable=true, options={"default":0.000000})
     */
    private $unitPriceTe = '0.000000';

    /**
     * @var integer
     *
     * @ORM\Column(name="quantity_expected", type="integer", nullable=false)
     */
    private $quantityExpected;

    /**
     * @var integer
     *
     * @ORM\Column(name="quantity_received", type="integer", nullable=false)
     */
    private $quantityReceived;

    /**
     * @var string
     *
     * @ORM\Column(name="price_te", type="decimal", precision=20, scale=6, nullable=true, options={"default":0.000000})
     */
    private $priceTe = '0.000000';

    /**
     * @var string
     *
     * @ORM\Column(name="discount_rate", type="decimal", precision=20, scale=6, nullable=true, options={"default":0.000000})
     */
    private $discountRate = '0.000000';

    /**
     * @var string
     *
     * @ORM\Column(name="discount_value_te", type="decimal", precision=20, scale=6, nullable=true, options={"default":0.000000})
     */
    private $discountValueTe = '0.000000';

    /**
     * @var string
     *
     * @ORM\Column(name="price_with_discount_te", type="decimal", precision=20, scale=6, nullable=true, options={"default":0.000000})
     */
    private $priceWithDiscountTe = '0.000000';

    /**
     * @var string
     *
     * @ORM\Column(name="tax_rate", type="decimal", precision=20, scale=6, nullable=true, options={"default":0.000000})
     */
    private $taxRate = '0.000000';

    /**
     * @var string
     *
     * @ORM\Column(name="tax_value", type="decimal", precision=20, scale=6, nullable=true, options={"default":0.000000})
     */
    private $taxValue = '0.000000';

    /**
     * @var string
     *
     * @ORM\Column(name="price_ti", type="decimal", precision=20, scale=6, nullable=true, options={"default":0.000000})
     */
    private $priceTi = '0.000000';

    /**
     * @var string
     *
     * @ORM\Column(name="tax_value_with_order_discount", type="decimal", precision=20, scale=6, nullable=true, options={"default":0.000000})
     */
    private $taxValueWithOrderDiscount = '0.000000';

    /**
     * @var string
     *
     * @ORM\Column(name="price_with_order_discount_te", type="decimal", precision=20, scale=6, nullable=true, options={"default":0.000000})
     */
    private $priceWithOrderDiscountTe = '0.000000';

    /**
     * @var integer
     *
     * @ORM\Column(name="id_supply_order_detail", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $idSupplyOrderDetail;



    /**
     * Set idSupplyOrder
     *
     * @param integer $idSupplyOrder
     *
     * @return SupplyOrderDetail
     */
    public function setIdSupplyOrder($idSupplyOrder)
    {
        $this->idSupplyOrder = $idSupplyOrder;

        return $this;
    }

    /**
     * Get idSupplyOrder
     *
     * @return integer
     */
    public function getIdSupplyOrder()
    {
        return $this->idSupplyOrder;
    }

    /**
     * Set idCurrency
     *
     * @param integer $idCurrency
     *
     * @return SupplyOrderDetail
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
     * Set idProduct
     *
     * @param integer $idProduct
     *
     * @return SupplyOrderDetail
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
     * @return SupplyOrderDetail
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
     * @return SupplyOrderDetail
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
     * @return SupplyOrderDetail
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
     * Set name
     *
     * @param string $name
     *
     * @return SupplyOrderDetail
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set ean13
     *
     * @param string $ean13
     *
     * @return SupplyOrderDetail
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
     * @return SupplyOrderDetail
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
     * @return SupplyOrderDetail
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
     * Set exchangeRate
     *
     * @param string $exchangeRate
     *
     * @return SupplyOrderDetail
     */
    public function setExchangeRate($exchangeRate)
    {
        $this->exchangeRate = $exchangeRate;

        return $this;
    }

    /**
     * Get exchangeRate
     *
     * @return string
     */
    public function getExchangeRate()
    {
        return $this->exchangeRate;
    }

    /**
     * Set unitPriceTe
     *
     * @param string $unitPriceTe
     *
     * @return SupplyOrderDetail
     */
    public function setUnitPriceTe($unitPriceTe)
    {
        $this->unitPriceTe = $unitPriceTe;

        return $this;
    }

    /**
     * Get unitPriceTe
     *
     * @return string
     */
    public function getUnitPriceTe()
    {
        return $this->unitPriceTe;
    }

    /**
     * Set quantityExpected
     *
     * @param integer $quantityExpected
     *
     * @return SupplyOrderDetail
     */
    public function setQuantityExpected($quantityExpected)
    {
        $this->quantityExpected = $quantityExpected;

        return $this;
    }

    /**
     * Get quantityExpected
     *
     * @return integer
     */
    public function getQuantityExpected()
    {
        return $this->quantityExpected;
    }

    /**
     * Set quantityReceived
     *
     * @param integer $quantityReceived
     *
     * @return SupplyOrderDetail
     */
    public function setQuantityReceived($quantityReceived)
    {
        $this->quantityReceived = $quantityReceived;

        return $this;
    }

    /**
     * Get quantityReceived
     *
     * @return integer
     */
    public function getQuantityReceived()
    {
        return $this->quantityReceived;
    }

    /**
     * Set priceTe
     *
     * @param string $priceTe
     *
     * @return SupplyOrderDetail
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
     * Set discountRate
     *
     * @param string $discountRate
     *
     * @return SupplyOrderDetail
     */
    public function setDiscountRate($discountRate)
    {
        $this->discountRate = $discountRate;

        return $this;
    }

    /**
     * Get discountRate
     *
     * @return string
     */
    public function getDiscountRate()
    {
        return $this->discountRate;
    }

    /**
     * Set discountValueTe
     *
     * @param string $discountValueTe
     *
     * @return SupplyOrderDetail
     */
    public function setDiscountValueTe($discountValueTe)
    {
        $this->discountValueTe = $discountValueTe;

        return $this;
    }

    /**
     * Get discountValueTe
     *
     * @return string
     */
    public function getDiscountValueTe()
    {
        return $this->discountValueTe;
    }

    /**
     * Set priceWithDiscountTe
     *
     * @param string $priceWithDiscountTe
     *
     * @return SupplyOrderDetail
     */
    public function setPriceWithDiscountTe($priceWithDiscountTe)
    {
        $this->priceWithDiscountTe = $priceWithDiscountTe;

        return $this;
    }

    /**
     * Get priceWithDiscountTe
     *
     * @return string
     */
    public function getPriceWithDiscountTe()
    {
        return $this->priceWithDiscountTe;
    }

    /**
     * Set taxRate
     *
     * @param string $taxRate
     *
     * @return SupplyOrderDetail
     */
    public function setTaxRate($taxRate)
    {
        $this->taxRate = $taxRate;

        return $this;
    }

    /**
     * Get taxRate
     *
     * @return string
     */
    public function getTaxRate()
    {
        return $this->taxRate;
    }

    /**
     * Set taxValue
     *
     * @param string $taxValue
     *
     * @return SupplyOrderDetail
     */
    public function setTaxValue($taxValue)
    {
        $this->taxValue = $taxValue;

        return $this;
    }

    /**
     * Get taxValue
     *
     * @return string
     */
    public function getTaxValue()
    {
        return $this->taxValue;
    }

    /**
     * Set priceTi
     *
     * @param string $priceTi
     *
     * @return SupplyOrderDetail
     */
    public function setPriceTi($priceTi)
    {
        $this->priceTi = $priceTi;

        return $this;
    }

    /**
     * Get priceTi
     *
     * @return string
     */
    public function getPriceTi()
    {
        return $this->priceTi;
    }

    /**
     * Set taxValueWithOrderDiscount
     *
     * @param string $taxValueWithOrderDiscount
     *
     * @return SupplyOrderDetail
     */
    public function setTaxValueWithOrderDiscount($taxValueWithOrderDiscount)
    {
        $this->taxValueWithOrderDiscount = $taxValueWithOrderDiscount;

        return $this;
    }

    /**
     * Get taxValueWithOrderDiscount
     *
     * @return string
     */
    public function getTaxValueWithOrderDiscount()
    {
        return $this->taxValueWithOrderDiscount;
    }

    /**
     * Set priceWithOrderDiscountTe
     *
     * @param string $priceWithOrderDiscountTe
     *
     * @return SupplyOrderDetail
     */
    public function setPriceWithOrderDiscountTe($priceWithOrderDiscountTe)
    {
        $this->priceWithOrderDiscountTe = $priceWithOrderDiscountTe;

        return $this;
    }

    /**
     * Get priceWithOrderDiscountTe
     *
     * @return string
     */
    public function getPriceWithOrderDiscountTe()
    {
        return $this->priceWithOrderDiscountTe;
    }

    /**
     * Get idSupplyOrderDetail
     *
     * @return integer
     */
    public function getIdSupplyOrderDetail()
    {
        return $this->idSupplyOrderDetail;
    }
}
