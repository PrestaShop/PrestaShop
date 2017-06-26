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
 * CartRule
 *
 * @ORM\Table(indexes={@ORM\Index(name="id_customer", columns={"id_customer", "active", "date_to"}), @ORM\Index(name="group_restriction", columns={"group_restriction", "active", "date_to"}), @ORM\Index(name="id_customer_2", columns={"id_customer", "active", "highlight", "date_to"}), @ORM\Index(name="group_restriction_2", columns={"group_restriction", "active", "highlight", "date_to"})})
 * @ORM\Entity
 */
class CartRule
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id_customer", type="integer", nullable=false, options={"default":0})
     */
    private $idCustomer = '0';

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date_from", type="datetime", nullable=false)
     */
    private $dateFrom;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date_to", type="datetime", nullable=false)
     */
    private $dateTo;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="text", length=65535, nullable=true)
     */
    private $description;

    /**
     * @var integer
     *
     * @ORM\Column(name="quantity", type="integer", nullable=false, options={"default":0})
     */
    private $quantity = '0';

    /**
     * @var integer
     *
     * @ORM\Column(name="quantity_per_user", type="integer", nullable=false, options={"default":0})
     */
    private $quantityPerUser = '0';

    /**
     * @var integer
     *
     * @ORM\Column(name="priority", type="integer", nullable=false, options={"default":1})
     */
    private $priority = '1';

    /**
     * @var boolean
     *
     * @ORM\Column(name="partial_use", type="boolean", nullable=false, options={"default":0})
     */
    private $partialUse = '0';

    /**
     * @var string
     *
     * @ORM\Column(name="code", type="string", length=254, nullable=false)
     */
    private $code;

    /**
     * @var string
     *
     * @ORM\Column(name="minimum_amount", type="decimal", precision=17, scale=2, nullable=false, options={"default":0.00})
     */
    private $minimumAmount = '0.00';

    /**
     * @var boolean
     *
     * @ORM\Column(name="minimum_amount_tax", type="boolean", nullable=false, options={"default":0})
     */
    private $minimumAmountTax = '0';

    /**
     * @var integer
     *
     * @ORM\Column(name="minimum_amount_currency", type="integer", nullable=false, options={"default":0})
     */
    private $minimumAmountCurrency = '0';

    /**
     * @var boolean
     *
     * @ORM\Column(name="minimum_amount_shipping", type="boolean", nullable=false, options={"default":0})
     */
    private $minimumAmountShipping = '0';

    /**
     * @var boolean
     *
     * @ORM\Column(name="country_restriction", type="boolean", nullable=false, options={"default":0})
     */
    private $countryRestriction = '0';

    /**
     * @var boolean
     *
     * @ORM\Column(name="carrier_restriction", type="boolean", nullable=false, options={"default":0})
     */
    private $carrierRestriction = '0';

    /**
     * @var boolean
     *
     * @ORM\Column(name="group_restriction", type="boolean", nullable=false, options={"default":0})
     */
    private $groupRestriction = '0';

    /**
     * @var boolean
     *
     * @ORM\Column(name="cart_rule_restriction", type="boolean", nullable=false, options={"default":0})
     */
    private $cartRuleRestriction = '0';

    /**
     * @var boolean
     *
     * @ORM\Column(name="product_restriction", type="boolean", nullable=false, options={"default":0})
     */
    private $productRestriction = '0';

    /**
     * @var boolean
     *
     * @ORM\Column(name="shop_restriction", type="boolean", nullable=false, options={"default":0})
     */
    private $shopRestriction = '0';

    /**
     * @var boolean
     *
     * @ORM\Column(name="free_shipping", type="boolean", nullable=false, options={"default":0})
     */
    private $freeShipping = '0';

    /**
     * @var string
     *
     * @ORM\Column(name="reduction_percent", type="decimal", precision=5, scale=2, nullable=false, options={"default":0.00})
     */
    private $reductionPercent = '0.00';

    /**
     * @var string
     *
     * @ORM\Column(name="reduction_amount", type="decimal", precision=17, scale=2, nullable=false, options={"default":0.00})
     */
    private $reductionAmount = '0.00';

    /**
     * @var boolean
     *
     * @ORM\Column(name="reduction_tax", type="boolean", nullable=false, options={"default":0})
     */
    private $reductionTax = '0';

    /**
     * @var integer
     *
     * @ORM\Column(name="reduction_currency", type="integer", nullable=false, options={"default":0})
     */
    private $reductionCurrency = '0';

    /**
     * @var integer
     *
     * @ORM\Column(name="reduction_product", type="integer", nullable=false, options={"default":0})
     */
    private $reductionProduct = '0';

    /**
     * @var boolean
     *
     * @ORM\Column(name="reduction_exclude_special", type="boolean", nullable=false, options={"default":0})
     */
    private $reductionExcludeSpecial = '0';

    /**
     * @var integer
     *
     * @ORM\Column(name="gift_product", type="integer", nullable=false, options={"default":0})
     */
    private $giftProduct = '0';

    /**
     * @var integer
     *
     * @ORM\Column(name="gift_product_attribute", type="integer", nullable=false, options={"default":0})
     */
    private $giftProductAttribute = '0';

    /**
     * @var boolean
     *
     * @ORM\Column(name="highlight", type="boolean", nullable=false, options={"default":0})
     */
    private $highlight = '0';

    /**
     * @var boolean
     *
     * @ORM\Column(name="active", type="boolean", nullable=false, options={"default":0})
     */
    private $active = '0';

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
     * @ORM\Column(name="id_cart_rule", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $idCartRule;



    /**
     * Set idCustomer
     *
     * @param integer $idCustomer
     *
     * @return CartRule
     */
    public function setIdCustomer($idCustomer)
    {
        $this->idCustomer = $idCustomer;

        return $this;
    }

    /**
     * Get idCustomer
     *
     * @return integer
     */
    public function getIdCustomer()
    {
        return $this->idCustomer;
    }

    /**
     * Set dateFrom
     *
     * @param \DateTime $dateFrom
     *
     * @return CartRule
     */
    public function setDateFrom($dateFrom)
    {
        $this->dateFrom = $dateFrom;

        return $this;
    }

    /**
     * Get dateFrom
     *
     * @return \DateTime
     */
    public function getDateFrom()
    {
        return $this->dateFrom;
    }

    /**
     * Set dateTo
     *
     * @param \DateTime $dateTo
     *
     * @return CartRule
     */
    public function setDateTo($dateTo)
    {
        $this->dateTo = $dateTo;

        return $this;
    }

    /**
     * Get dateTo
     *
     * @return \DateTime
     */
    public function getDateTo()
    {
        return $this->dateTo;
    }

    /**
     * Set description
     *
     * @param string $description
     *
     * @return CartRule
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set quantity
     *
     * @param integer $quantity
     *
     * @return CartRule
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
     * Set quantityPerUser
     *
     * @param integer $quantityPerUser
     *
     * @return CartRule
     */
    public function setQuantityPerUser($quantityPerUser)
    {
        $this->quantityPerUser = $quantityPerUser;

        return $this;
    }

    /**
     * Get quantityPerUser
     *
     * @return integer
     */
    public function getQuantityPerUser()
    {
        return $this->quantityPerUser;
    }

    /**
     * Set priority
     *
     * @param integer $priority
     *
     * @return CartRule
     */
    public function setPriority($priority)
    {
        $this->priority = $priority;

        return $this;
    }

    /**
     * Get priority
     *
     * @return integer
     */
    public function getPriority()
    {
        return $this->priority;
    }

    /**
     * Set partialUse
     *
     * @param boolean $partialUse
     *
     * @return CartRule
     */
    public function setPartialUse($partialUse)
    {
        $this->partialUse = $partialUse;

        return $this;
    }

    /**
     * Get partialUse
     *
     * @return boolean
     */
    public function getPartialUse()
    {
        return $this->partialUse;
    }

    /**
     * Set code
     *
     * @param string $code
     *
     * @return CartRule
     */
    public function setCode($code)
    {
        $this->code = $code;

        return $this;
    }

    /**
     * Get code
     *
     * @return string
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * Set minimumAmount
     *
     * @param string $minimumAmount
     *
     * @return CartRule
     */
    public function setMinimumAmount($minimumAmount)
    {
        $this->minimumAmount = $minimumAmount;

        return $this;
    }

    /**
     * Get minimumAmount
     *
     * @return string
     */
    public function getMinimumAmount()
    {
        return $this->minimumAmount;
    }

    /**
     * Set minimumAmountTax
     *
     * @param boolean $minimumAmountTax
     *
     * @return CartRule
     */
    public function setMinimumAmountTax($minimumAmountTax)
    {
        $this->minimumAmountTax = $minimumAmountTax;

        return $this;
    }

    /**
     * Get minimumAmountTax
     *
     * @return boolean
     */
    public function getMinimumAmountTax()
    {
        return $this->minimumAmountTax;
    }

    /**
     * Set minimumAmountCurrency
     *
     * @param integer $minimumAmountCurrency
     *
     * @return CartRule
     */
    public function setMinimumAmountCurrency($minimumAmountCurrency)
    {
        $this->minimumAmountCurrency = $minimumAmountCurrency;

        return $this;
    }

    /**
     * Get minimumAmountCurrency
     *
     * @return integer
     */
    public function getMinimumAmountCurrency()
    {
        return $this->minimumAmountCurrency;
    }

    /**
     * Set minimumAmountShipping
     *
     * @param boolean $minimumAmountShipping
     *
     * @return CartRule
     */
    public function setMinimumAmountShipping($minimumAmountShipping)
    {
        $this->minimumAmountShipping = $minimumAmountShipping;

        return $this;
    }

    /**
     * Get minimumAmountShipping
     *
     * @return boolean
     */
    public function getMinimumAmountShipping()
    {
        return $this->minimumAmountShipping;
    }

    /**
     * Set countryRestriction
     *
     * @param boolean $countryRestriction
     *
     * @return CartRule
     */
    public function setCountryRestriction($countryRestriction)
    {
        $this->countryRestriction = $countryRestriction;

        return $this;
    }

    /**
     * Get countryRestriction
     *
     * @return boolean
     */
    public function getCountryRestriction()
    {
        return $this->countryRestriction;
    }

    /**
     * Set carrierRestriction
     *
     * @param boolean $carrierRestriction
     *
     * @return CartRule
     */
    public function setCarrierRestriction($carrierRestriction)
    {
        $this->carrierRestriction = $carrierRestriction;

        return $this;
    }

    /**
     * Get carrierRestriction
     *
     * @return boolean
     */
    public function getCarrierRestriction()
    {
        return $this->carrierRestriction;
    }

    /**
     * Set groupRestriction
     *
     * @param boolean $groupRestriction
     *
     * @return CartRule
     */
    public function setGroupRestriction($groupRestriction)
    {
        $this->groupRestriction = $groupRestriction;

        return $this;
    }

    /**
     * Get groupRestriction
     *
     * @return boolean
     */
    public function getGroupRestriction()
    {
        return $this->groupRestriction;
    }

    /**
     * Set cartRuleRestriction
     *
     * @param boolean $cartRuleRestriction
     *
     * @return CartRule
     */
    public function setCartRuleRestriction($cartRuleRestriction)
    {
        $this->cartRuleRestriction = $cartRuleRestriction;

        return $this;
    }

    /**
     * Get cartRuleRestriction
     *
     * @return boolean
     */
    public function getCartRuleRestriction()
    {
        return $this->cartRuleRestriction;
    }

    /**
     * Set productRestriction
     *
     * @param boolean $productRestriction
     *
     * @return CartRule
     */
    public function setProductRestriction($productRestriction)
    {
        $this->productRestriction = $productRestriction;

        return $this;
    }

    /**
     * Get productRestriction
     *
     * @return boolean
     */
    public function getProductRestriction()
    {
        return $this->productRestriction;
    }

    /**
     * Set shopRestriction
     *
     * @param boolean $shopRestriction
     *
     * @return CartRule
     */
    public function setShopRestriction($shopRestriction)
    {
        $this->shopRestriction = $shopRestriction;

        return $this;
    }

    /**
     * Get shopRestriction
     *
     * @return boolean
     */
    public function getShopRestriction()
    {
        return $this->shopRestriction;
    }

    /**
     * Set freeShipping
     *
     * @param boolean $freeShipping
     *
     * @return CartRule
     */
    public function setFreeShipping($freeShipping)
    {
        $this->freeShipping = $freeShipping;

        return $this;
    }

    /**
     * Get freeShipping
     *
     * @return boolean
     */
    public function getFreeShipping()
    {
        return $this->freeShipping;
    }

    /**
     * Set reductionPercent
     *
     * @param string $reductionPercent
     *
     * @return CartRule
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
     * @return CartRule
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
     * Set reductionTax
     *
     * @param boolean $reductionTax
     *
     * @return CartRule
     */
    public function setReductionTax($reductionTax)
    {
        $this->reductionTax = $reductionTax;

        return $this;
    }

    /**
     * Get reductionTax
     *
     * @return boolean
     */
    public function getReductionTax()
    {
        return $this->reductionTax;
    }

    /**
     * Set reductionCurrency
     *
     * @param integer $reductionCurrency
     *
     * @return CartRule
     */
    public function setReductionCurrency($reductionCurrency)
    {
        $this->reductionCurrency = $reductionCurrency;

        return $this;
    }

    /**
     * Get reductionCurrency
     *
     * @return integer
     */
    public function getReductionCurrency()
    {
        return $this->reductionCurrency;
    }

    /**
     * Set reductionProduct
     *
     * @param integer $reductionProduct
     *
     * @return CartRule
     */
    public function setReductionProduct($reductionProduct)
    {
        $this->reductionProduct = $reductionProduct;

        return $this;
    }

    /**
     * Get reductionProduct
     *
     * @return integer
     */
    public function getReductionProduct()
    {
        return $this->reductionProduct;
    }

    /**
     * Set reductionExcludeSpecial
     *
     * @param boolean $reductionExcludeSpecial
     *
     * @return CartRule
     */
    public function setReductionExcludeSpecial($reductionExcludeSpecial)
    {
        $this->reductionExcludeSpecial = $reductionExcludeSpecial;

        return $this;
    }

    /**
     * Get reductionExcludeSpecial
     *
     * @return boolean
     */
    public function getReductionExcludeSpecial()
    {
        return $this->reductionExcludeSpecial;
    }

    /**
     * Set giftProduct
     *
     * @param integer $giftProduct
     *
     * @return CartRule
     */
    public function setGiftProduct($giftProduct)
    {
        $this->giftProduct = $giftProduct;

        return $this;
    }

    /**
     * Get giftProduct
     *
     * @return integer
     */
    public function getGiftProduct()
    {
        return $this->giftProduct;
    }

    /**
     * Set giftProductAttribute
     *
     * @param integer $giftProductAttribute
     *
     * @return CartRule
     */
    public function setGiftProductAttribute($giftProductAttribute)
    {
        $this->giftProductAttribute = $giftProductAttribute;

        return $this;
    }

    /**
     * Get giftProductAttribute
     *
     * @return integer
     */
    public function getGiftProductAttribute()
    {
        return $this->giftProductAttribute;
    }

    /**
     * Set highlight
     *
     * @param boolean $highlight
     *
     * @return CartRule
     */
    public function setHighlight($highlight)
    {
        $this->highlight = $highlight;

        return $this;
    }

    /**
     * Get highlight
     *
     * @return boolean
     */
    public function getHighlight()
    {
        return $this->highlight;
    }

    /**
     * Set active
     *
     * @param boolean $active
     *
     * @return CartRule
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
     * Set dateAdd
     *
     * @param \DateTime $dateAdd
     *
     * @return CartRule
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
     * @return CartRule
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
     * Get idCartRule
     *
     * @return integer
     */
    public function getIdCartRule()
    {
        return $this->idCartRule;
    }
}
