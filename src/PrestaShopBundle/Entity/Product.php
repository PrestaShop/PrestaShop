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
 * Product
 *
 * @ORM\Table(indexes={@ORM\Index(name="product_supplier", columns={"id_supplier"}), @ORM\Index(name="product_manufacturer", columns={"id_manufacturer", "id_product"}), @ORM\Index(name="id_category_default", columns={"id_category_default"}), @ORM\Index(name="indexed", columns={"indexed"}), @ORM\Index(name="date_add", columns={"date_add"}), @ORM\Index(name="state", columns={"state", "date_upd"})})
 * @ORM\Entity
 */
class Product
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id_supplier", type="integer", nullable=true)
     */
    private $idSupplier;

    /**
     * @var integer
     *
     * @ORM\Column(name="id_manufacturer", type="integer", nullable=true)
     */
    private $idManufacturer;

    /**
     * @var integer
     *
     * @ORM\Column(name="id_category_default", type="integer", nullable=true)
     */
    private $idCategoryDefault;

    /**
     * @var integer
     *
     * @ORM\Column(name="id_shop_default", type="integer", nullable=false)
     */
    private $idShopDefault = '1';

    /**
     * @var integer
     *
     * @ORM\Column(name="id_tax_rules_group", type="integer", nullable=false)
     */
    private $idTaxRulesGroup;

    /**
     * @var boolean
     *
     * @ORM\Column(name="on_sale", type="boolean", nullable=false)
     */
    private $onSale = '0';

    /**
     * @var boolean
     *
     * @ORM\Column(name="online_only", type="boolean", nullable=false)
     */
    private $onlineOnly = '0';

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
     * @var integer
     *
     * @ORM\Column(name="minimal_quantity", type="integer", nullable=false)
     */
    private $minimalQuantity = '1';

    /**
     * @var string
     *
     * @ORM\Column(name="price", type="decimal", precision=20, scale=6, nullable=false)
     */
    private $price = '0.000000';

    /**
     * @var string
     *
     * @ORM\Column(name="wholesale_price", type="decimal", precision=20, scale=6, nullable=false)
     */
    private $wholesalePrice = '0.000000';

    /**
     * @var string
     *
     * @ORM\Column(name="unity", type="string", length=255, nullable=true)
     */
    private $unity;

    /**
     * @var string
     *
     * @ORM\Column(name="unit_price_ratio", type="decimal", precision=20, scale=6, nullable=false)
     */
    private $unitPriceRatio = '0.000000';

    /**
     * @var string
     *
     * @ORM\Column(name="additional_shipping_cost", type="decimal", precision=20, scale=2, nullable=false)
     */
    private $additionalShippingCost = '0.00';

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
     * @ORM\Column(name="width", type="decimal", precision=20, scale=6, nullable=false)
     */
    private $width = '0.000000';

    /**
     * @var string
     *
     * @ORM\Column(name="height", type="decimal", precision=20, scale=6, nullable=false)
     */
    private $height = '0.000000';

    /**
     * @var string
     *
     * @ORM\Column(name="depth", type="decimal", precision=20, scale=6, nullable=false)
     */
    private $depth = '0.000000';

    /**
     * @var string
     *
     * @ORM\Column(name="weight", type="decimal", precision=20, scale=6, nullable=false)
     */
    private $weight = '0.000000';

    /**
     * @var integer
     *
     * @ORM\Column(name="out_of_stock", type="integer", nullable=false)
     */
    private $outOfStock = '2';

    /**
     * @var boolean
     *
     * @ORM\Column(name="quantity_discount", type="boolean", nullable=true)
     */
    private $quantityDiscount = '0';

    /**
     * @var boolean
     *
     * @ORM\Column(name="customizable", type="boolean", nullable=false)
     */
    private $customizable = '0';

    /**
     * @var boolean
     *
     * @ORM\Column(name="uploadable_files", type="boolean", nullable=false)
     */
    private $uploadableFiles = '0';

    /**
     * @var boolean
     *
     * @ORM\Column(name="text_fields", type="boolean", nullable=false)
     */
    private $textFields = '0';

    /**
     * @var boolean
     *
     * @ORM\Column(name="active", type="boolean", nullable=false)
     */
    private $active = '0';

    /**
     * @var string
     *
     * @ORM\Column(name="redirect_type", type="string", nullable=false)
     */
    private $redirectType = '';

    /**
     * @var integer
     *
     * @ORM\Column(name="id_type_redirected", type="integer", nullable=false)
     */
    private $idTypeRedirected = '0';

    /**
     * @var boolean
     *
     * @ORM\Column(name="available_for_order", type="boolean", nullable=false)
     */
    private $availableForOrder = '1';

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="available_date", type="date", nullable=true)
     */
    private $availableDate;

    /**
     * @var boolean
     *
     * @ORM\Column(name="show_condition", type="boolean", nullable=false)
     */
    private $showCondition = '0';

    /**
     * @var string
     *
     * @ORM\Column(name="condition", type="string", nullable=false)
     */
    private $condition = 'new';

    /**
     * @var boolean
     *
     * @ORM\Column(name="show_price", type="boolean", nullable=false)
     */
    private $showPrice = '1';

    /**
     * @var boolean
     *
     * @ORM\Column(name="indexed", type="boolean", nullable=false)
     */
    private $indexed = '0';

    /**
     * @var string
     *
     * @ORM\Column(name="visibility", type="string", nullable=false)
     */
    private $visibility = 'both';

    /**
     * @var boolean
     *
     * @ORM\Column(name="cache_is_pack", type="boolean", nullable=false)
     */
    private $cacheIsPack = '0';

    /**
     * @var boolean
     *
     * @ORM\Column(name="cache_has_attachments", type="boolean", nullable=false)
     */
    private $cacheHasAttachments = '0';

    /**
     * @var boolean
     *
     * @ORM\Column(name="is_virtual", type="boolean", nullable=false)
     */
    private $isVirtual = '0';

    /**
     * @var integer
     *
     * @ORM\Column(name="cache_default_attribute", type="integer", nullable=true)
     */
    private $cacheDefaultAttribute;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date_add", type="datetime", nullable=false)
     */
    private $dateAdd;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date_upd", type="datetime", nullable=false)
     */
    private $dateUpd;

    /**
     * @var boolean
     *
     * @ORM\Column(name="advanced_stock_management", type="boolean", nullable=false)
     */
    private $advancedStockManagement = '0';

    /**
     * @var integer
     *
     * @ORM\Column(name="pack_stock_type", type="integer", nullable=false)
     */
    private $packStockType = '3';

    /**
     * @var integer
     *
     * @ORM\Column(name="state", type="integer", nullable=false)
     */
    private $state = '1';

    /**
     * @var integer
     *
     * @ORM\Column(name="id_product", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $idProduct;



    /**
     * Set idSupplier
     *
     * @param integer $idSupplier
     *
     * @return Product
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
     * Set idManufacturer
     *
     * @param integer $idManufacturer
     *
     * @return Product
     */
    public function setIdManufacturer($idManufacturer)
    {
        $this->idManufacturer = $idManufacturer;

        return $this;
    }

    /**
     * Get idManufacturer
     *
     * @return integer
     */
    public function getIdManufacturer()
    {
        return $this->idManufacturer;
    }

    /**
     * Set idCategoryDefault
     *
     * @param integer $idCategoryDefault
     *
     * @return Product
     */
    public function setIdCategoryDefault($idCategoryDefault)
    {
        $this->idCategoryDefault = $idCategoryDefault;

        return $this;
    }

    /**
     * Get idCategoryDefault
     *
     * @return integer
     */
    public function getIdCategoryDefault()
    {
        return $this->idCategoryDefault;
    }

    /**
     * Set idShopDefault
     *
     * @param integer $idShopDefault
     *
     * @return Product
     */
    public function setIdShopDefault($idShopDefault)
    {
        $this->idShopDefault = $idShopDefault;

        return $this;
    }

    /**
     * Get idShopDefault
     *
     * @return integer
     */
    public function getIdShopDefault()
    {
        return $this->idShopDefault;
    }

    /**
     * Set idTaxRulesGroup
     *
     * @param integer $idTaxRulesGroup
     *
     * @return Product
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
     * Set onSale
     *
     * @param boolean $onSale
     *
     * @return Product
     */
    public function setOnSale($onSale)
    {
        $this->onSale = $onSale;

        return $this;
    }

    /**
     * Get onSale
     *
     * @return boolean
     */
    public function getOnSale()
    {
        return $this->onSale;
    }

    /**
     * Set onlineOnly
     *
     * @param boolean $onlineOnly
     *
     * @return Product
     */
    public function setOnlineOnly($onlineOnly)
    {
        $this->onlineOnly = $onlineOnly;

        return $this;
    }

    /**
     * Get onlineOnly
     *
     * @return boolean
     */
    public function getOnlineOnly()
    {
        return $this->onlineOnly;
    }

    /**
     * Set ean13
     *
     * @param string $ean13
     *
     * @return Product
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
     * @return Product
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
     * @return Product
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
     * Set ecotax
     *
     * @param string $ecotax
     *
     * @return Product
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
     * @return Product
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
     * Set minimalQuantity
     *
     * @param integer $minimalQuantity
     *
     * @return Product
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
     * Set price
     *
     * @param string $price
     *
     * @return Product
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
     * Set wholesalePrice
     *
     * @param string $wholesalePrice
     *
     * @return Product
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
     * Set unity
     *
     * @param string $unity
     *
     * @return Product
     */
    public function setUnity($unity)
    {
        $this->unity = $unity;

        return $this;
    }

    /**
     * Get unity
     *
     * @return string
     */
    public function getUnity()
    {
        return $this->unity;
    }

    /**
     * Set unitPriceRatio
     *
     * @param string $unitPriceRatio
     *
     * @return Product
     */
    public function setUnitPriceRatio($unitPriceRatio)
    {
        $this->unitPriceRatio = $unitPriceRatio;

        return $this;
    }

    /**
     * Get unitPriceRatio
     *
     * @return string
     */
    public function getUnitPriceRatio()
    {
        return $this->unitPriceRatio;
    }

    /**
     * Set additionalShippingCost
     *
     * @param string $additionalShippingCost
     *
     * @return Product
     */
    public function setAdditionalShippingCost($additionalShippingCost)
    {
        $this->additionalShippingCost = $additionalShippingCost;

        return $this;
    }

    /**
     * Get additionalShippingCost
     *
     * @return string
     */
    public function getAdditionalShippingCost()
    {
        return $this->additionalShippingCost;
    }

    /**
     * Set reference
     *
     * @param string $reference
     *
     * @return Product
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
     * @return Product
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
     * @return Product
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
     * Set width
     *
     * @param string $width
     *
     * @return Product
     */
    public function setWidth($width)
    {
        $this->width = $width;

        return $this;
    }

    /**
     * Get width
     *
     * @return string
     */
    public function getWidth()
    {
        return $this->width;
    }

    /**
     * Set height
     *
     * @param string $height
     *
     * @return Product
     */
    public function setHeight($height)
    {
        $this->height = $height;

        return $this;
    }

    /**
     * Get height
     *
     * @return string
     */
    public function getHeight()
    {
        return $this->height;
    }

    /**
     * Set depth
     *
     * @param string $depth
     *
     * @return Product
     */
    public function setDepth($depth)
    {
        $this->depth = $depth;

        return $this;
    }

    /**
     * Get depth
     *
     * @return string
     */
    public function getDepth()
    {
        return $this->depth;
    }

    /**
     * Set weight
     *
     * @param string $weight
     *
     * @return Product
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
     * Set outOfStock
     *
     * @param integer $outOfStock
     *
     * @return Product
     */
    public function setOutOfStock($outOfStock)
    {
        $this->outOfStock = $outOfStock;

        return $this;
    }

    /**
     * Get outOfStock
     *
     * @return integer
     */
    public function getOutOfStock()
    {
        return $this->outOfStock;
    }

    /**
     * Set quantityDiscount
     *
     * @param boolean $quantityDiscount
     *
     * @return Product
     */
    public function setQuantityDiscount($quantityDiscount)
    {
        $this->quantityDiscount = $quantityDiscount;

        return $this;
    }

    /**
     * Get quantityDiscount
     *
     * @return boolean
     */
    public function getQuantityDiscount()
    {
        return $this->quantityDiscount;
    }

    /**
     * Set customizable
     *
     * @param boolean $customizable
     *
     * @return Product
     */
    public function setCustomizable($customizable)
    {
        $this->customizable = $customizable;

        return $this;
    }

    /**
     * Get customizable
     *
     * @return boolean
     */
    public function getCustomizable()
    {
        return $this->customizable;
    }

    /**
     * Set uploadableFiles
     *
     * @param boolean $uploadableFiles
     *
     * @return Product
     */
    public function setUploadableFiles($uploadableFiles)
    {
        $this->uploadableFiles = $uploadableFiles;

        return $this;
    }

    /**
     * Get uploadableFiles
     *
     * @return boolean
     */
    public function getUploadableFiles()
    {
        return $this->uploadableFiles;
    }

    /**
     * Set textFields
     *
     * @param boolean $textFields
     *
     * @return Product
     */
    public function setTextFields($textFields)
    {
        $this->textFields = $textFields;

        return $this;
    }

    /**
     * Get textFields
     *
     * @return boolean
     */
    public function getTextFields()
    {
        return $this->textFields;
    }

    /**
     * Set active
     *
     * @param boolean $active
     *
     * @return Product
     */
    public function setActive($active)
    {
        $this->active = $active;

        return $this;
    }

    /**
     * Get active
     *
     * @return boolean
     */
    public function getActive()
    {
        return $this->active;
    }

    /**
     * Set redirectType
     *
     * @param string $redirectType
     *
     * @return Product
     */
    public function setRedirectType($redirectType)
    {
        $this->redirectType = $redirectType;

        return $this;
    }

    /**
     * Get redirectType
     *
     * @return string
     */
    public function getRedirectType()
    {
        return $this->redirectType;
    }

    /**
     * Set idTypeRedirected
     *
     * @param integer $idTypeRedirected
     *
     * @return Product
     */
    public function setIdTypeRedirected($idTypeRedirected)
    {
        $this->idTypeRedirected = $idTypeRedirected;

        return $this;
    }

    /**
     * Get idTypeRedirected
     *
     * @return integer
     */
    public function getIdTypeRedirected()
    {
        return $this->idTypeRedirected;
    }

    /**
     * Set availableForOrder
     *
     * @param boolean $availableForOrder
     *
     * @return Product
     */
    public function setAvailableForOrder($availableForOrder)
    {
        $this->availableForOrder = $availableForOrder;

        return $this;
    }

    /**
     * Get availableForOrder
     *
     * @return boolean
     */
    public function getAvailableForOrder()
    {
        return $this->availableForOrder;
    }

    /**
     * Set availableDate
     *
     * @param \DateTime $availableDate
     *
     * @return Product
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
     * Set showCondition
     *
     * @param boolean $showCondition
     *
     * @return Product
     */
    public function setShowCondition($showCondition)
    {
        $this->showCondition = $showCondition;

        return $this;
    }

    /**
     * Get showCondition
     *
     * @return boolean
     */
    public function getShowCondition()
    {
        return $this->showCondition;
    }

    /**
     * Set condition
     *
     * @param string $condition
     *
     * @return Product
     */
    public function setCondition($condition)
    {
        $this->condition = $condition;

        return $this;
    }

    /**
     * Get condition
     *
     * @return string
     */
    public function getCondition()
    {
        return $this->condition;
    }

    /**
     * Set showPrice
     *
     * @param boolean $showPrice
     *
     * @return Product
     */
    public function setShowPrice($showPrice)
    {
        $this->showPrice = $showPrice;

        return $this;
    }

    /**
     * Get showPrice
     *
     * @return boolean
     */
    public function getShowPrice()
    {
        return $this->showPrice;
    }

    /**
     * Set indexed
     *
     * @param boolean $indexed
     *
     * @return Product
     */
    public function setIndexed($indexed)
    {
        $this->indexed = $indexed;

        return $this;
    }

    /**
     * Get indexed
     *
     * @return boolean
     */
    public function getIndexed()
    {
        return $this->indexed;
    }

    /**
     * Set visibility
     *
     * @param string $visibility
     *
     * @return Product
     */
    public function setVisibility($visibility)
    {
        $this->visibility = $visibility;

        return $this;
    }

    /**
     * Get visibility
     *
     * @return string
     */
    public function getVisibility()
    {
        return $this->visibility;
    }

    /**
     * Set cacheIsPack
     *
     * @param boolean $cacheIsPack
     *
     * @return Product
     */
    public function setCacheIsPack($cacheIsPack)
    {
        $this->cacheIsPack = $cacheIsPack;

        return $this;
    }

    /**
     * Get cacheIsPack
     *
     * @return boolean
     */
    public function getCacheIsPack()
    {
        return $this->cacheIsPack;
    }

    /**
     * Set cacheHasAttachments
     *
     * @param boolean $cacheHasAttachments
     *
     * @return Product
     */
    public function setCacheHasAttachments($cacheHasAttachments)
    {
        $this->cacheHasAttachments = $cacheHasAttachments;

        return $this;
    }

    /**
     * Get cacheHasAttachments
     *
     * @return boolean
     */
    public function getCacheHasAttachments()
    {
        return $this->cacheHasAttachments;
    }

    /**
     * Set isVirtual
     *
     * @param boolean $isVirtual
     *
     * @return Product
     */
    public function setIsVirtual($isVirtual)
    {
        $this->isVirtual = $isVirtual;

        return $this;
    }

    /**
     * Get isVirtual
     *
     * @return boolean
     */
    public function getIsVirtual()
    {
        return $this->isVirtual;
    }

    /**
     * Set cacheDefaultAttribute
     *
     * @param integer $cacheDefaultAttribute
     *
     * @return Product
     */
    public function setCacheDefaultAttribute($cacheDefaultAttribute)
    {
        $this->cacheDefaultAttribute = $cacheDefaultAttribute;

        return $this;
    }

    /**
     * Get cacheDefaultAttribute
     *
     * @return integer
     */
    public function getCacheDefaultAttribute()
    {
        return $this->cacheDefaultAttribute;
    }

    /**
     * Set dateAdd
     *
     * @param \DateTime $dateAdd
     *
     * @return Product
     */
    public function setDateAdd($dateAdd)
    {
        $this->dateAdd = $dateAdd;

        return $this;
    }

    /**
     * Get dateAdd
     *
     * @return \DateTime
     */
    public function getDateAdd()
    {
        return $this->dateAdd;
    }

    /**
     * Set dateUpd
     *
     * @param \DateTime $dateUpd
     *
     * @return Product
     */
    public function setDateUpd($dateUpd)
    {
        $this->dateUpd = $dateUpd;

        return $this;
    }

    /**
     * Get dateUpd
     *
     * @return \DateTime
     */
    public function getDateUpd()
    {
        return $this->dateUpd;
    }

    /**
     * Set advancedStockManagement
     *
     * @param boolean $advancedStockManagement
     *
     * @return Product
     */
    public function setAdvancedStockManagement($advancedStockManagement)
    {
        $this->advancedStockManagement = $advancedStockManagement;

        return $this;
    }

    /**
     * Get advancedStockManagement
     *
     * @return boolean
     */
    public function getAdvancedStockManagement()
    {
        return $this->advancedStockManagement;
    }

    /**
     * Set packStockType
     *
     * @param integer $packStockType
     *
     * @return Product
     */
    public function setPackStockType($packStockType)
    {
        $this->packStockType = $packStockType;

        return $this;
    }

    /**
     * Get packStockType
     *
     * @return integer
     */
    public function getPackStockType()
    {
        return $this->packStockType;
    }

    /**
     * Set state
     *
     * @param integer $state
     *
     * @return Product
     */
    public function setState($state)
    {
        $this->state = $state;

        return $this;
    }

    /**
     * Get state
     *
     * @return integer
     */
    public function getState()
    {
        return $this->state;
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
}
