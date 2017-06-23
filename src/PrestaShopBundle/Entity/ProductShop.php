<?php

namespace PrestaShopBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ProductShop
 *
 * @ORM\Table(indexes={@ORM\Index(name="id_category_default", columns={"id_category_default"}), @ORM\Index(name="date_add", columns={"date_add", "active", "visibility"}), @ORM\Index(name="indexed", columns={"indexed", "active", "id_product"})})
 * @ORM\Entity
 */
class ProductShop
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id_category_default", type="integer", nullable=true)
     */
    private $idCategoryDefault;

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
     * @ORM\Column(name="ecotax", type="decimal", precision=17, scale=6, nullable=false)
     */
    private $ecotax = '0.000000';

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
    private $showCondition = '1';

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
     * @var integer
     *
     * @ORM\Column(name="cache_default_attribute", type="integer", nullable=true)
     */
    private $cacheDefaultAttribute;

    /**
     * @var boolean
     *
     * @ORM\Column(name="advanced_stock_management", type="boolean", nullable=false)
     */
    private $advancedStockManagement = '0';

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
     * @var integer
     *
     * @ORM\Column(name="pack_stock_type", type="integer", nullable=false)
     */
    private $packStockType = '3';

    /**
     * @var integer
     *
     * @ORM\Column(name="id_product", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $idProduct;

    /**
     * @var integer
     *
     * @ORM\Column(name="id_shop", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $idShop;



    /**
     * Set idCategoryDefault
     *
     * @param integer $idCategoryDefault
     *
     * @return ProductShop
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
     * Set idTaxRulesGroup
     *
     * @param integer $idTaxRulesGroup
     *
     * @return ProductShop
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
     * @return ProductShop
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
     * @return ProductShop
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
     * Set ecotax
     *
     * @param string $ecotax
     *
     * @return ProductShop
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
     * Set minimalQuantity
     *
     * @param integer $minimalQuantity
     *
     * @return ProductShop
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
     * @return ProductShop
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
     * @return ProductShop
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
     * @return ProductShop
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
     * @return ProductShop
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
     * @return ProductShop
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
     * Set customizable
     *
     * @param boolean $customizable
     *
     * @return ProductShop
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
     * @return ProductShop
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
     * @return ProductShop
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
     * @return ProductShop
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
     * @return ProductShop
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
     * @return ProductShop
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
     * @return ProductShop
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
     * @return ProductShop
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
     * @return ProductShop
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
     * @return ProductShop
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
     * @return ProductShop
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
     * @return ProductShop
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
     * @return ProductShop
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
     * Set cacheDefaultAttribute
     *
     * @param integer $cacheDefaultAttribute
     *
     * @return ProductShop
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
     * Set advancedStockManagement
     *
     * @param boolean $advancedStockManagement
     *
     * @return ProductShop
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
     * Set dateAdd
     *
     * @param \DateTime $dateAdd
     *
     * @return ProductShop
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
     * @return ProductShop
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
     * Set packStockType
     *
     * @param integer $packStockType
     *
     * @return ProductShop
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
     * Set idProduct
     *
     * @param integer $idProduct
     *
     * @return ProductShop
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
     * Set idShop
     *
     * @param integer $idShop
     *
     * @return ProductShop
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
