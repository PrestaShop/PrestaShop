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
 * SpecificPrice
 *
 * @ORM\Table(uniqueConstraints={@ORM\UniqueConstraint(name="id_product_2", columns={"id_product", "id_product_attribute", "id_customer", "id_cart", "from", "to", "id_shop", "id_shop_group", "id_currency", "id_country", "id_group", "from_quantity", "id_specific_price_rule"})}, indexes={@ORM\Index(name="id_product", columns={"id_product", "id_shop", "id_currency", "id_country", "id_group", "id_customer", "from_quantity", "from", "to"}), @ORM\Index(name="from_quantity", columns={"from_quantity"}), @ORM\Index(name="id_specific_price_rule", columns={"id_specific_price_rule"}), @ORM\Index(name="id_cart", columns={"id_cart"}), @ORM\Index(name="id_product_attribute", columns={"id_product_attribute"}), @ORM\Index(name="id_shop", columns={"id_shop"}), @ORM\Index(name="id_customer", columns={"id_customer"}), @ORM\Index(name="from", columns={"from"}), @ORM\Index(name="to", columns={"to"})})
 * @ORM\Entity
 */
class SpecificPrice
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id_specific_price_rule", type="integer", nullable=false)
     */
    private $idSpecificPriceRule;

    /**
     * @var integer
     *
     * @ORM\Column(name="id_cart", type="integer", nullable=false)
     */
    private $idCart;

    /**
     * @var integer
     *
     * @ORM\Column(name="id_product", type="integer", nullable=false)
     */
    private $idProduct;

    /**
     * @var integer
     *
     * @ORM\Column(name="id_shop", type="integer", nullable=false, options={"default":1})
     */
    private $idShop = '1';

    /**
     * @var integer
     *
     * @ORM\Column(name="id_shop_group", type="integer", nullable=false)
     */
    private $idShopGroup;

    /**
     * @var integer
     *
     * @ORM\Column(name="id_currency", type="integer", nullable=false)
     */
    private $idCurrency;

    /**
     * @var integer
     *
     * @ORM\Column(name="id_country", type="integer", nullable=false)
     */
    private $idCountry;

    /**
     * @var integer
     *
     * @ORM\Column(name="id_group", type="integer", nullable=false)
     */
    private $idGroup;

    /**
     * @var integer
     *
     * @ORM\Column(name="id_customer", type="integer", nullable=false)
     */
    private $idCustomer;

    /**
     * @var integer
     *
     * @ORM\Column(name="id_product_attribute", type="integer", nullable=false)
     */
    private $idProductAttribute;

    /**
     * @var string
     *
     * @ORM\Column(name="price", type="decimal", precision=20, scale=6, nullable=false)
     */
    private $price;

    /**
     * @var integer
     *
     * @ORM\Column(name="from_quantity", type="integer", nullable=false)
     */
    private $fromQuantity;

    /**
     * @var string
     *
     * @ORM\Column(name="reduction", type="decimal", precision=20, scale=6, nullable=false)
     */
    private $reduction;

    /**
     * @var boolean
     *
     * @ORM\Column(name="reduction_tax", type="boolean", nullable=false, options={"default":1})
     */
    private $reductionTax = '1';

    /**
     * @var string
     *
     * @ORM\Column(name="reduction_type", type="string", nullable=false, columnDefinition="ENUM('amount','percentage')")
     */
    private $reductionType;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="from", type="datetime", nullable=false)
     */
    private $from;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="to", type="datetime", nullable=false)
     */
    private $to;

    /**
     * @var integer
     *
     * @ORM\Column(name="id_specific_price", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $idSpecificPrice;



    /**
     * Set idSpecificPriceRule
     *
     * @param integer $idSpecificPriceRule
     *
     * @return SpecificPrice
     */
    public function setIdSpecificPriceRule($idSpecificPriceRule)
    {
        $this->idSpecificPriceRule = $idSpecificPriceRule;

        return $this;
    }

    /**
     * Get idSpecificPriceRule
     *
     * @return integer
     */
    public function getIdSpecificPriceRule()
    {
        return $this->idSpecificPriceRule;
    }

    /**
     * Set idCart
     *
     * @param integer $idCart
     *
     * @return SpecificPrice
     */
    public function setIdCart($idCart)
    {
        $this->idCart = $idCart;

        return $this;
    }

    /**
     * Get idCart
     *
     * @return integer
     */
    public function getIdCart()
    {
        return $this->idCart;
    }

    /**
     * Set idProduct
     *
     * @param integer $idProduct
     *
     * @return SpecificPrice
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
     * @return SpecificPrice
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
     * @return SpecificPrice
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
     * Set idCurrency
     *
     * @param integer $idCurrency
     *
     * @return SpecificPrice
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
     * Set idCountry
     *
     * @param integer $idCountry
     *
     * @return SpecificPrice
     */
    public function setIdCountry($idCountry)
    {
        $this->idCountry = $idCountry;

        return $this;
    }

    /**
     * Get idCountry
     *
     * @return integer
     */
    public function getIdCountry()
    {
        return $this->idCountry;
    }

    /**
     * Set idGroup
     *
     * @param integer $idGroup
     *
     * @return SpecificPrice
     */
    public function setIdGroup($idGroup)
    {
        $this->idGroup = $idGroup;

        return $this;
    }

    /**
     * Get idGroup
     *
     * @return integer
     */
    public function getIdGroup()
    {
        return $this->idGroup;
    }

    /**
     * Set idCustomer
     *
     * @param integer $idCustomer
     *
     * @return SpecificPrice
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
     * Set idProductAttribute
     *
     * @param integer $idProductAttribute
     *
     * @return SpecificPrice
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
     * Set price
     *
     * @param string $price
     *
     * @return SpecificPrice
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
     * Set fromQuantity
     *
     * @param integer $fromQuantity
     *
     * @return SpecificPrice
     */
    public function setFromQuantity($fromQuantity)
    {
        $this->fromQuantity = $fromQuantity;

        return $this;
    }

    /**
     * Get fromQuantity
     *
     * @return integer
     */
    public function getFromQuantity()
    {
        return $this->fromQuantity;
    }

    /**
     * Set reduction
     *
     * @param string $reduction
     *
     * @return SpecificPrice
     */
    public function setReduction($reduction)
    {
        $this->reduction = $reduction;

        return $this;
    }

    /**
     * Get reduction
     *
     * @return string
     */
    public function getReduction()
    {
        return $this->reduction;
    }

    /**
     * Set reductionTax
     *
     * @param boolean $reductionTax
     *
     * @return SpecificPrice
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
     * Set reductionType
     *
     * @param string $reductionType
     *
     * @return SpecificPrice
     */
    public function setReductionType($reductionType)
    {
        $this->reductionType = $reductionType;

        return $this;
    }

    /**
     * Get reductionType
     *
     * @return string
     */
    public function getReductionType()
    {
        return $this->reductionType;
    }

    /**
     * Set from
     *
     * @param \DateTime $from
     *
     * @return SpecificPrice
     */
    public function setFrom($from)
    {
        $this->from = $from;

        return $this;
    }

    /**
     * Get from
     *
     * @return \DateTime
     */
    public function getFrom()
    {
        return $this->from;
    }

    /**
     * Set to
     *
     * @param \DateTime $to
     *
     * @return SpecificPrice
     */
    public function setTo($to)
    {
        $this->to = $to;

        return $this;
    }

    /**
     * Get to
     *
     * @return \DateTime
     */
    public function getTo()
    {
        return $this->to;
    }

    /**
     * Get idSpecificPrice
     *
     * @return integer
     */
    public function getIdSpecificPrice()
    {
        return $this->idSpecificPrice;
    }
}
