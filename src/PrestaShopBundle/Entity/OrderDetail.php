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
 * OrderDetail
 *
 * @ORM\Table(indexes={@ORM\Index(name="order_detail_order", columns={"id_order"}), @ORM\Index(name="product_id", columns={"product_id"}), @ORM\Index(name="product_attribute_id", columns={"product_attribute_id"}), @ORM\Index(name="id_tax_rules_group", columns={"id_tax_rules_group"}), @ORM\Index(name="id_order_id_order_detail", columns={"id_order", "id_order_detail"})})
 * @ORM\Entity
 */
class OrderDetail
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id_order", type="integer", nullable=false)
     */
    private $idOrder;

    /**
     * @var integer
     *
     * @ORM\Column(name="id_order_invoice", type="integer", nullable=true)
     */
    private $idOrderInvoice;

    /**
     * @var integer
     *
     * @ORM\Column(name="id_warehouse", type="integer", nullable=true, options={"default":0})
     */
    private $idWarehouse = '0';

    /**
     * @var integer
     *
     * @ORM\Column(name="id_shop", type="integer", nullable=false)
     */
    private $idShop;

    /**
     * @var integer
     *
     * @ORM\Column(name="product_id", type="integer", nullable=false)
     */
    private $productId;

    /**
     * @var integer
     *
     * @ORM\Column(name="product_attribute_id", type="integer", nullable=true)
     */
    private $productAttributeId;

    /**
     * @var integer
     *
     * @ORM\Column(name="id_customization", type="integer", nullable=true, options={"default":0})
     */
    private $idCustomization = '0';

    /**
     * @var string
     *
     * @ORM\Column(name="product_name", type="string", length=255, nullable=false)
     */
    private $productName;

    /**
     * @var integer
     *
     * @ORM\Column(name="product_quantity", type="integer", nullable=false, options={"default":0})
     */
    private $productQuantity = '0';

    /**
     * @var integer
     *
     * @ORM\Column(name="product_quantity_in_stock", type="integer", nullable=false, options={"default":0})
     */
    private $productQuantityInStock = '0';

    /**
     * @var integer
     *
     * @ORM\Column(name="product_quantity_refunded", type="integer", nullable=false, options={"default":0})
     */
    private $productQuantityRefunded = '0';

    /**
     * @var integer
     *
     * @ORM\Column(name="product_quantity_return", type="integer", nullable=false, options={"default":0})
     */
    private $productQuantityReturn = '0';

    /**
     * @var integer
     *
     * @ORM\Column(name="product_quantity_reinjected", type="integer", nullable=false, options={"default":0})
     */
    private $productQuantityReinjected = '0';

    /**
     * @var string
     *
     * @ORM\Column(name="product_price", type="decimal", precision=20, scale=6, nullable=false, options={"default":0.000000})
     */
    private $productPrice = '0.000000';

    /**
     * @var string
     *
     * @ORM\Column(name="reduction_percent", type="decimal", precision=10, scale=2, nullable=false, options={"default":0.00})
     */
    private $reductionPercent = '0.00';

    /**
     * @var string
     *
     * @ORM\Column(name="reduction_amount", type="decimal", precision=20, scale=6, nullable=false, options={"default":0.000000})
     */
    private $reductionAmount = '0.000000';

    /**
     * @var string
     *
     * @ORM\Column(name="reduction_amount_tax_incl", type="decimal", precision=20, scale=6, nullable=false, options={"default":0.000000})
     */
    private $reductionAmountTaxIncl = '0.000000';

    /**
     * @var string
     *
     * @ORM\Column(name="reduction_amount_tax_excl", type="decimal", precision=20, scale=6, nullable=false, options={"default":0.000000})
     */
    private $reductionAmountTaxExcl = '0.000000';

    /**
     * @var string
     *
     * @ORM\Column(name="group_reduction", type="decimal", precision=10, scale=2, nullable=false, options={"default":0.00})
     */
    private $groupReduction = '0.00';

    /**
     * @var string
     *
     * @ORM\Column(name="product_quantity_discount", type="decimal", precision=20, scale=6, nullable=false, options={"default":0.000000})
     */
    private $productQuantityDiscount = '0.000000';

    /**
     * @var string
     *
     * @ORM\Column(name="product_ean13", type="string", length=13, nullable=true)
     */
    private $productEan13;

    /**
     * @var string
     *
     * @ORM\Column(name="product_isbn", type="string", length=32, nullable=true)
     */
    private $productIsbn;

    /**
     * @var string
     *
     * @ORM\Column(name="product_upc", type="string", length=12, nullable=true)
     */
    private $productUpc;

    /**
     * @var string
     *
     * @ORM\Column(name="product_reference", type="string", length=32, nullable=true)
     */
    private $productReference;

    /**
     * @var string
     *
     * @ORM\Column(name="product_supplier_reference", type="string", length=32, nullable=true)
     */
    private $productSupplierReference;

    /**
     * @var string
     *
     * @ORM\Column(name="product_weight", type="decimal", precision=20, scale=6, nullable=false)
     */
    private $productWeight;

    /**
     * @var integer
     *
     * @ORM\Column(name="id_tax_rules_group", type="integer", nullable=true, options={"default":0})
     */
    private $idTaxRulesGroup = '0';

    /**
     * @var boolean
     *
     * @ORM\Column(name="tax_computation_method", type="boolean", nullable=false, options={"default":0})
     */
    private $taxComputationMethod = '0';

    /**
     * @var string
     *
     * @ORM\Column(name="tax_name", type="string", length=16, nullable=false)
     */
    private $taxName;

    /**
     * @var string
     *
     * @ORM\Column(name="tax_rate", type="decimal", precision=10, scale=3, nullable=false, options={"default":0.000})
     */
    private $taxRate = '0.000';

    /**
     * @var string
     *
     * @ORM\Column(name="ecotax", type="decimal", precision=21, scale=6, nullable=false, options={"default":0.000000})
     */
    private $ecotax = '0.000000';

    /**
     * @var string
     *
     * @ORM\Column(name="ecotax_tax_rate", type="decimal", precision=5, scale=3, nullable=false, options={"default":0.000})
     */
    private $ecotaxTaxRate = '0.000';

    /**
     * @var boolean
     *
     * @ORM\Column(name="discount_quantity_applied", type="boolean", nullable=false, options={"default":0})
     */
    private $discountQuantityApplied = '0';

    /**
     * @var string
     *
     * @ORM\Column(name="download_hash", type="string", length=255, nullable=true)
     */
    private $downloadHash;

    /**
     * @var integer
     *
     * @ORM\Column(name="download_nb", type="integer", nullable=true, options={"default":0})
     */
    private $downloadNb = '0';

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="download_deadline", type="datetime", nullable=true)
     */
    private $downloadDeadline;

    /**
     * @var string
     *
     * @ORM\Column(name="total_price_tax_incl", type="decimal", precision=20, scale=6, nullable=false, options={"default":0.000000})
     */
    private $totalPriceTaxIncl = '0.000000';

    /**
     * @var string
     *
     * @ORM\Column(name="total_price_tax_excl", type="decimal", precision=20, scale=6, nullable=false, options={"default":0.000000})
     */
    private $totalPriceTaxExcl = '0.000000';

    /**
     * @var string
     *
     * @ORM\Column(name="unit_price_tax_incl", type="decimal", precision=20, scale=6, nullable=false, options={"default":0.000000})
     */
    private $unitPriceTaxIncl = '0.000000';

    /**
     * @var string
     *
     * @ORM\Column(name="unit_price_tax_excl", type="decimal", precision=20, scale=6, nullable=false, options={"default":0.000000})
     */
    private $unitPriceTaxExcl = '0.000000';

    /**
     * @var string
     *
     * @ORM\Column(name="total_shipping_price_tax_incl", type="decimal", precision=20, scale=6, nullable=false, options={"default":0.000000})
     */
    private $totalShippingPriceTaxIncl = '0.000000';

    /**
     * @var string
     *
     * @ORM\Column(name="total_shipping_price_tax_excl", type="decimal", precision=20, scale=6, nullable=false, options={"default":0.000000})
     */
    private $totalShippingPriceTaxExcl = '0.000000';

    /**
     * @var string
     *
     * @ORM\Column(name="purchase_supplier_price", type="decimal", precision=20, scale=6, nullable=false, options={"default":0.000000})
     */
    private $purchaseSupplierPrice = '0.000000';

    /**
     * @var string
     *
     * @ORM\Column(name="original_product_price", type="decimal", precision=20, scale=6, nullable=false, options={"default":0.000000})
     */
    private $originalProductPrice = '0.000000';

    /**
     * @var string
     *
     * @ORM\Column(name="original_wholesale_price", type="decimal", precision=20, scale=6, nullable=false, options={"default":0.000000})
     */
    private $originalWholesalePrice = '0.000000';

    /**
     * @var integer
     *
     * @ORM\Column(name="id_order_detail", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $idOrderDetail;



    /**
     * Set idOrder
     *
     * @param integer $idOrder
     *
     * @return OrderDetail
     */
    public function setIdOrder($idOrder)
    {
        $this->idOrder = $idOrder;

        return $this;
    }

    /**
     * Get idOrder
     *
     * @return integer
     */
    public function getIdOrder()
    {
        return $this->idOrder;
    }

    /**
     * Set idOrderInvoice
     *
     * @param integer $idOrderInvoice
     *
     * @return OrderDetail
     */
    public function setIdOrderInvoice($idOrderInvoice)
    {
        $this->idOrderInvoice = $idOrderInvoice;

        return $this;
    }

    /**
     * Get idOrderInvoice
     *
     * @return integer
     */
    public function getIdOrderInvoice()
    {
        return $this->idOrderInvoice;
    }

    /**
     * Set idWarehouse
     *
     * @param integer $idWarehouse
     *
     * @return OrderDetail
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
     * @return OrderDetail
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
     * Set productId
     *
     * @param integer $productId
     *
     * @return OrderDetail
     */
    public function setProductId($productId)
    {
        $this->productId = $productId;

        return $this;
    }

    /**
     * Get productId
     *
     * @return integer
     */
    public function getProductId()
    {
        return $this->productId;
    }

    /**
     * Set productAttributeId
     *
     * @param integer $productAttributeId
     *
     * @return OrderDetail
     */
    public function setProductAttributeId($productAttributeId)
    {
        $this->productAttributeId = $productAttributeId;

        return $this;
    }

    /**
     * Get productAttributeId
     *
     * @return integer
     */
    public function getProductAttributeId()
    {
        return $this->productAttributeId;
    }

    /**
     * Set idCustomization
     *
     * @param integer $idCustomization
     *
     * @return OrderDetail
     */
    public function setIdCustomization($idCustomization)
    {
        $this->idCustomization = $idCustomization;

        return $this;
    }

    /**
     * Get idCustomization
     *
     * @return integer
     */
    public function getIdCustomization()
    {
        return $this->idCustomization;
    }

    /**
     * Set productName
     *
     * @param string $productName
     *
     * @return OrderDetail
     */
    public function setProductName($productName)
    {
        $this->productName = $productName;

        return $this;
    }

    /**
     * Get productName
     *
     * @return string
     */
    public function getProductName()
    {
        return $this->productName;
    }

    /**
     * Set productQuantity
     *
     * @param integer $productQuantity
     *
     * @return OrderDetail
     */
    public function setProductQuantity($productQuantity)
    {
        $this->productQuantity = $productQuantity;

        return $this;
    }

    /**
     * Get productQuantity
     *
     * @return integer
     */
    public function getProductQuantity()
    {
        return $this->productQuantity;
    }

    /**
     * Set productQuantityInStock
     *
     * @param integer $productQuantityInStock
     *
     * @return OrderDetail
     */
    public function setProductQuantityInStock($productQuantityInStock)
    {
        $this->productQuantityInStock = $productQuantityInStock;

        return $this;
    }

    /**
     * Get productQuantityInStock
     *
     * @return integer
     */
    public function getProductQuantityInStock()
    {
        return $this->productQuantityInStock;
    }

    /**
     * Set productQuantityRefunded
     *
     * @param integer $productQuantityRefunded
     *
     * @return OrderDetail
     */
    public function setProductQuantityRefunded($productQuantityRefunded)
    {
        $this->productQuantityRefunded = $productQuantityRefunded;

        return $this;
    }

    /**
     * Get productQuantityRefunded
     *
     * @return integer
     */
    public function getProductQuantityRefunded()
    {
        return $this->productQuantityRefunded;
    }

    /**
     * Set productQuantityReturn
     *
     * @param integer $productQuantityReturn
     *
     * @return OrderDetail
     */
    public function setProductQuantityReturn($productQuantityReturn)
    {
        $this->productQuantityReturn = $productQuantityReturn;

        return $this;
    }

    /**
     * Get productQuantityReturn
     *
     * @return integer
     */
    public function getProductQuantityReturn()
    {
        return $this->productQuantityReturn;
    }

    /**
     * Set productQuantityReinjected
     *
     * @param integer $productQuantityReinjected
     *
     * @return OrderDetail
     */
    public function setProductQuantityReinjected($productQuantityReinjected)
    {
        $this->productQuantityReinjected = $productQuantityReinjected;

        return $this;
    }

    /**
     * Get productQuantityReinjected
     *
     * @return integer
     */
    public function getProductQuantityReinjected()
    {
        return $this->productQuantityReinjected;
    }

    /**
     * Set productPrice
     *
     * @param string $productPrice
     *
     * @return OrderDetail
     */
    public function setProductPrice($productPrice)
    {
        $this->productPrice = $productPrice;

        return $this;
    }

    /**
     * Get productPrice
     *
     * @return string
     */
    public function getProductPrice()
    {
        return $this->productPrice;
    }

    /**
     * Set reductionPercent
     *
     * @param string $reductionPercent
     *
     * @return OrderDetail
     */
    public function setReductionPercent($reductionPercent)
    {
        $this->reductionPercent = $reductionPercent;

        return $this;
    }

    /**
     * Get reductionPercent
     *
     * @return string
     */
    public function getReductionPercent()
    {
        return $this->reductionPercent;
    }

    /**
     * Set reductionAmount
     *
     * @param string $reductionAmount
     *
     * @return OrderDetail
     */
    public function setReductionAmount($reductionAmount)
    {
        $this->reductionAmount = $reductionAmount;

        return $this;
    }

    /**
     * Get reductionAmount
     *
     * @return string
     */
    public function getReductionAmount()
    {
        return $this->reductionAmount;
    }

    /**
     * Set reductionAmountTaxIncl
     *
     * @param string $reductionAmountTaxIncl
     *
     * @return OrderDetail
     */
    public function setReductionAmountTaxIncl($reductionAmountTaxIncl)
    {
        $this->reductionAmountTaxIncl = $reductionAmountTaxIncl;

        return $this;
    }

    /**
     * Get reductionAmountTaxIncl
     *
     * @return string
     */
    public function getReductionAmountTaxIncl()
    {
        return $this->reductionAmountTaxIncl;
    }

    /**
     * Set reductionAmountTaxExcl
     *
     * @param string $reductionAmountTaxExcl
     *
     * @return OrderDetail
     */
    public function setReductionAmountTaxExcl($reductionAmountTaxExcl)
    {
        $this->reductionAmountTaxExcl = $reductionAmountTaxExcl;

        return $this;
    }

    /**
     * Get reductionAmountTaxExcl
     *
     * @return string
     */
    public function getReductionAmountTaxExcl()
    {
        return $this->reductionAmountTaxExcl;
    }

    /**
     * Set groupReduction
     *
     * @param string $groupReduction
     *
     * @return OrderDetail
     */
    public function setGroupReduction($groupReduction)
    {
        $this->groupReduction = $groupReduction;

        return $this;
    }

    /**
     * Get groupReduction
     *
     * @return string
     */
    public function getGroupReduction()
    {
        return $this->groupReduction;
    }

    /**
     * Set productQuantityDiscount
     *
     * @param string $productQuantityDiscount
     *
     * @return OrderDetail
     */
    public function setProductQuantityDiscount($productQuantityDiscount)
    {
        $this->productQuantityDiscount = $productQuantityDiscount;

        return $this;
    }

    /**
     * Get productQuantityDiscount
     *
     * @return string
     */
    public function getProductQuantityDiscount()
    {
        return $this->productQuantityDiscount;
    }

    /**
     * Set productEan13
     *
     * @param string $productEan13
     *
     * @return OrderDetail
     */
    public function setProductEan13($productEan13)
    {
        $this->productEan13 = $productEan13;

        return $this;
    }

    /**
     * Get productEan13
     *
     * @return string
     */
    public function getProductEan13()
    {
        return $this->productEan13;
    }

    /**
     * Set productIsbn
     *
     * @param string $productIsbn
     *
     * @return OrderDetail
     */
    public function setProductIsbn($productIsbn)
    {
        $this->productIsbn = $productIsbn;

        return $this;
    }

    /**
     * Get productIsbn
     *
     * @return string
     */
    public function getProductIsbn()
    {
        return $this->productIsbn;
    }

    /**
     * Set productUpc
     *
     * @param string $productUpc
     *
     * @return OrderDetail
     */
    public function setProductUpc($productUpc)
    {
        $this->productUpc = $productUpc;

        return $this;
    }

    /**
     * Get productUpc
     *
     * @return string
     */
    public function getProductUpc()
    {
        return $this->productUpc;
    }

    /**
     * Set productReference
     *
     * @param string $productReference
     *
     * @return OrderDetail
     */
    public function setProductReference($productReference)
    {
        $this->productReference = $productReference;

        return $this;
    }

    /**
     * Get productReference
     *
     * @return string
     */
    public function getProductReference()
    {
        return $this->productReference;
    }

    /**
     * Set productSupplierReference
     *
     * @param string $productSupplierReference
     *
     * @return OrderDetail
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
     * Set productWeight
     *
     * @param string $productWeight
     *
     * @return OrderDetail
     */
    public function setProductWeight($productWeight)
    {
        $this->productWeight = $productWeight;

        return $this;
    }

    /**
     * Get productWeight
     *
     * @return string
     */
    public function getProductWeight()
    {
        return $this->productWeight;
    }

    /**
     * Set idTaxRulesGroup
     *
     * @param integer $idTaxRulesGroup
     *
     * @return OrderDetail
     */
    public function setIdTaxRulesGroup($idTaxRulesGroup)
    {
        $this->idTaxRulesGroup = $idTaxRulesGroup;

        return $this;
    }

    /**
     * Get idTaxRulesGroup
     *
     * @return integer
     */
    public function getIdTaxRulesGroup()
    {
        return $this->idTaxRulesGroup;
    }

    /**
     * Set taxComputationMethod
     *
     * @param boolean $taxComputationMethod
     *
     * @return OrderDetail
     */
    public function setTaxComputationMethod($taxComputationMethod)
    {
        $this->taxComputationMethod = $taxComputationMethod;

        return $this;
    }

    /**
     * Get taxComputationMethod
     *
     * @return boolean
     */
    public function getTaxComputationMethod()
    {
        return $this->taxComputationMethod;
    }

    /**
     * Set taxName
     *
     * @param string $taxName
     *
     * @return OrderDetail
     */
    public function setTaxName($taxName)
    {
        $this->taxName = $taxName;

        return $this;
    }

    /**
     * Get taxName
     *
     * @return string
     */
    public function getTaxName()
    {
        return $this->taxName;
    }

    /**
     * Set taxRate
     *
     * @param string $taxRate
     *
     * @return OrderDetail
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
     * Set ecotax
     *
     * @param string $ecotax
     *
     * @return OrderDetail
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
     * Set ecotaxTaxRate
     *
     * @param string $ecotaxTaxRate
     *
     * @return OrderDetail
     */
    public function setEcotaxTaxRate($ecotaxTaxRate)
    {
        $this->ecotaxTaxRate = $ecotaxTaxRate;

        return $this;
    }

    /**
     * Get ecotaxTaxRate
     *
     * @return string
     */
    public function getEcotaxTaxRate()
    {
        return $this->ecotaxTaxRate;
    }

    /**
     * Set discountQuantityApplied
     *
     * @param boolean $discountQuantityApplied
     *
     * @return OrderDetail
     */
    public function setDiscountQuantityApplied($discountQuantityApplied)
    {
        $this->discountQuantityApplied = $discountQuantityApplied;

        return $this;
    }

    /**
     * Get discountQuantityApplied
     *
     * @return boolean
     */
    public function getDiscountQuantityApplied()
    {
        return $this->discountQuantityApplied;
    }

    /**
     * Set downloadHash
     *
     * @param string $downloadHash
     *
     * @return OrderDetail
     */
    public function setDownloadHash($downloadHash)
    {
        $this->downloadHash = $downloadHash;

        return $this;
    }

    /**
     * Get downloadHash
     *
     * @return string
     */
    public function getDownloadHash()
    {
        return $this->downloadHash;
    }

    /**
     * Set downloadNb
     *
     * @param integer $downloadNb
     *
     * @return OrderDetail
     */
    public function setDownloadNb($downloadNb)
    {
        $this->downloadNb = $downloadNb;

        return $this;
    }

    /**
     * Get downloadNb
     *
     * @return integer
     */
    public function getDownloadNb()
    {
        return $this->downloadNb;
    }

    /**
     * Set downloadDeadline
     *
     * @param \DateTime $downloadDeadline
     *
     * @return OrderDetail
     */
    public function setDownloadDeadline($downloadDeadline)
    {
        $this->downloadDeadline = $downloadDeadline;

        return $this;
    }

    /**
     * Get downloadDeadline
     *
     * @return \DateTime
     */
    public function getDownloadDeadline()
    {
        return $this->downloadDeadline;
    }

    /**
     * Set totalPriceTaxIncl
     *
     * @param string $totalPriceTaxIncl
     *
     * @return OrderDetail
     */
    public function setTotalPriceTaxIncl($totalPriceTaxIncl)
    {
        $this->totalPriceTaxIncl = $totalPriceTaxIncl;

        return $this;
    }

    /**
     * Get totalPriceTaxIncl
     *
     * @return string
     */
    public function getTotalPriceTaxIncl()
    {
        return $this->totalPriceTaxIncl;
    }

    /**
     * Set totalPriceTaxExcl
     *
     * @param string $totalPriceTaxExcl
     *
     * @return OrderDetail
     */
    public function setTotalPriceTaxExcl($totalPriceTaxExcl)
    {
        $this->totalPriceTaxExcl = $totalPriceTaxExcl;

        return $this;
    }

    /**
     * Get totalPriceTaxExcl
     *
     * @return string
     */
    public function getTotalPriceTaxExcl()
    {
        return $this->totalPriceTaxExcl;
    }

    /**
     * Set unitPriceTaxIncl
     *
     * @param string $unitPriceTaxIncl
     *
     * @return OrderDetail
     */
    public function setUnitPriceTaxIncl($unitPriceTaxIncl)
    {
        $this->unitPriceTaxIncl = $unitPriceTaxIncl;

        return $this;
    }

    /**
     * Get unitPriceTaxIncl
     *
     * @return string
     */
    public function getUnitPriceTaxIncl()
    {
        return $this->unitPriceTaxIncl;
    }

    /**
     * Set unitPriceTaxExcl
     *
     * @param string $unitPriceTaxExcl
     *
     * @return OrderDetail
     */
    public function setUnitPriceTaxExcl($unitPriceTaxExcl)
    {
        $this->unitPriceTaxExcl = $unitPriceTaxExcl;

        return $this;
    }

    /**
     * Get unitPriceTaxExcl
     *
     * @return string
     */
    public function getUnitPriceTaxExcl()
    {
        return $this->unitPriceTaxExcl;
    }

    /**
     * Set totalShippingPriceTaxIncl
     *
     * @param string $totalShippingPriceTaxIncl
     *
     * @return OrderDetail
     */
    public function setTotalShippingPriceTaxIncl($totalShippingPriceTaxIncl)
    {
        $this->totalShippingPriceTaxIncl = $totalShippingPriceTaxIncl;

        return $this;
    }

    /**
     * Get totalShippingPriceTaxIncl
     *
     * @return string
     */
    public function getTotalShippingPriceTaxIncl()
    {
        return $this->totalShippingPriceTaxIncl;
    }

    /**
     * Set totalShippingPriceTaxExcl
     *
     * @param string $totalShippingPriceTaxExcl
     *
     * @return OrderDetail
     */
    public function setTotalShippingPriceTaxExcl($totalShippingPriceTaxExcl)
    {
        $this->totalShippingPriceTaxExcl = $totalShippingPriceTaxExcl;

        return $this;
    }

    /**
     * Get totalShippingPriceTaxExcl
     *
     * @return string
     */
    public function getTotalShippingPriceTaxExcl()
    {
        return $this->totalShippingPriceTaxExcl;
    }

    /**
     * Set purchaseSupplierPrice
     *
     * @param string $purchaseSupplierPrice
     *
     * @return OrderDetail
     */
    public function setPurchaseSupplierPrice($purchaseSupplierPrice)
    {
        $this->purchaseSupplierPrice = $purchaseSupplierPrice;

        return $this;
    }

    /**
     * Get purchaseSupplierPrice
     *
     * @return string
     */
    public function getPurchaseSupplierPrice()
    {
        return $this->purchaseSupplierPrice;
    }

    /**
     * Set originalProductPrice
     *
     * @param string $originalProductPrice
     *
     * @return OrderDetail
     */
    public function setOriginalProductPrice($originalProductPrice)
    {
        $this->originalProductPrice = $originalProductPrice;

        return $this;
    }

    /**
     * Get originalProductPrice
     *
     * @return string
     */
    public function getOriginalProductPrice()
    {
        return $this->originalProductPrice;
    }

    /**
     * Set originalWholesalePrice
     *
     * @param string $originalWholesalePrice
     *
     * @return OrderDetail
     */
    public function setOriginalWholesalePrice($originalWholesalePrice)
    {
        $this->originalWholesalePrice = $originalWholesalePrice;

        return $this;
    }

    /**
     * Get originalWholesalePrice
     *
     * @return string
     */
    public function getOriginalWholesalePrice()
    {
        return $this->originalWholesalePrice;
    }

    /**
     * Get idOrderDetail
     *
     * @return integer
     */
    public function getIdOrderDetail()
    {
        return $this->idOrderDetail;
    }
}
