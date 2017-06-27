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
 * Cart
 *
 * @ORM\Table(indexes={@ORM\Index(name="cart_customer", columns={"id_customer"}), @ORM\Index(name="id_address_delivery", columns={"id_address_delivery"}), @ORM\Index(name="id_address_invoice", columns={"id_address_invoice"}), @ORM\Index(name="id_carrier", columns={"id_carrier"}), @ORM\Index(name="id_lang", columns={"id_lang"}), @ORM\Index(name="id_currency", columns={"id_currency"}), @ORM\Index(name="id_guest", columns={"id_guest"}), @ORM\Index(name="id_shop_group", columns={"id_shop_group"}), @ORM\Index(name="id_shop_2", columns={"id_shop", "date_upd"}), @ORM\Index(name="id_shop", columns={"id_shop", "date_add"})})
 * @ORM\Entity
 */
class Cart
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id_shop_group", type="integer", nullable=false, options={"default":1})
     */
    private $idShopGroup = '1';

    /**
     * @var integer
     *
     * @ORM\Column(name="id_shop", type="integer", nullable=false, options={"default":1})
     */
    private $idShop = '1';

    /**
     * @var integer
     *
     * @ORM\Column(name="id_carrier", type="integer", nullable=false)
     */
    private $idCarrier;

    /**
     * @var string
     *
     * @ORM\Column(name="delivery_option", type="text", length=65535, nullable=false)
     */
    private $deliveryOption;

    /**
     * @var integer
     *
     * @ORM\Column(name="id_lang", type="integer", nullable=false)
     */
    private $idLang;

    /**
     * @var integer
     *
     * @ORM\Column(name="id_address_delivery", type="integer", nullable=false)
     */
    private $idAddressDelivery;

    /**
     * @var integer
     *
     * @ORM\Column(name="id_address_invoice", type="integer", nullable=false)
     */
    private $idAddressInvoice;

    /**
     * @var integer
     *
     * @ORM\Column(name="id_currency", type="integer", nullable=false)
     */
    private $idCurrency;

    /**
     * @var integer
     *
     * @ORM\Column(name="id_customer", type="integer", nullable=false)
     */
    private $idCustomer;

    /**
     * @var integer
     *
     * @ORM\Column(name="id_guest", type="integer", nullable=false)
     */
    private $idGuest;

    /**
     * @var string
     *
     * @ORM\Column(name="secure_key", type="string", length=32, nullable=false, options={"default":-1})
     */
    private $secureKey = '-1';

    /**
     * @var boolean
     *
     * @ORM\Column(name="recyclable", type="boolean", nullable=false, options={"default":1})
     */
    private $recyclable = '1';

    /**
     * @var boolean
     *
     * @ORM\Column(name="gift", type="boolean", nullable=false, options={"default":0})
     */
    private $gift = '0';

    /**
     * @var string
     *
     * @ORM\Column(name="gift_message", type="text", length=65535, nullable=true)
     */
    private $giftMessage;

    /**
     * @var boolean
     *
     * @ORM\Column(name="mobile_theme", type="boolean", nullable=false, options={"default":0})
     */
    private $mobileTheme = '0';

    /**
     * @var boolean
     *
     * @ORM\Column(name="allow_seperated_package", type="boolean", nullable=false, options={"default":0})
     */
    private $allowSeperatedPackage = '0';

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
     * @var string
     *
     * @ORM\Column(name="checkout_session_data", type="text", length=16777215, nullable=true)
     */
    private $checkoutSessionData;

    /**
     * @var integer
     *
     * @ORM\Column(name="id_cart", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $idCart;



    /**
     * Set idShopGroup
     *
     * @param integer $idShopGroup
     *
     * @return Cart
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
     * Set idShop
     *
     * @param integer $idShop
     *
     * @return Cart
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
     * Set idCarrier
     *
     * @param integer $idCarrier
     *
     * @return Cart
     */
    public function setIdCarrier($idCarrier)
    {
        $this->idCarrier = $idCarrier;

        return $this;
    }

    /**
     * Get idCarrier
     *
     * @return integer
     */
    public function getIdCarrier()
    {
        return $this->idCarrier;
    }

    /**
     * Set deliveryOption
     *
     * @param string $deliveryOption
     *
     * @return Cart
     */
    public function setDeliveryOption($deliveryOption)
    {
        $this->deliveryOption = $deliveryOption;

        return $this;
    }

    /**
     * Get deliveryOption
     *
     * @return string
     */
    public function getDeliveryOption()
    {
        return $this->deliveryOption;
    }

    /**
     * Set idLang
     *
     * @param integer $idLang
     *
     * @return Cart
     */
    public function setIdLang($idLang)
    {
        $this->idLang = $idLang;

        return $this;
    }

    /**
     * Get idLang
     *
     * @return integer
     */
    public function getIdLang()
    {
        return $this->idLang;
    }

    /**
     * Set idAddressDelivery
     *
     * @param integer $idAddressDelivery
     *
     * @return Cart
     */
    public function setIdAddressDelivery($idAddressDelivery)
    {
        $this->idAddressDelivery = $idAddressDelivery;

        return $this;
    }

    /**
     * Get idAddressDelivery
     *
     * @return integer
     */
    public function getIdAddressDelivery()
    {
        return $this->idAddressDelivery;
    }

    /**
     * Set idAddressInvoice
     *
     * @param integer $idAddressInvoice
     *
     * @return Cart
     */
    public function setIdAddressInvoice($idAddressInvoice)
    {
        $this->idAddressInvoice = $idAddressInvoice;

        return $this;
    }

    /**
     * Get idAddressInvoice
     *
     * @return integer
     */
    public function getIdAddressInvoice()
    {
        return $this->idAddressInvoice;
    }

    /**
     * Set idCurrency
     *
     * @param integer $idCurrency
     *
     * @return Cart
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
     * Set idCustomer
     *
     * @param integer $idCustomer
     *
     * @return Cart
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
     * Set idGuest
     *
     * @param integer $idGuest
     *
     * @return Cart
     */
    public function setIdGuest($idGuest)
    {
        $this->idGuest = $idGuest;

        return $this;
    }

    /**
     * Get idGuest
     *
     * @return integer
     */
    public function getIdGuest()
    {
        return $this->idGuest;
    }

    /**
     * Set secureKey
     *
     * @param string $secureKey
     *
     * @return Cart
     */
    public function setSecureKey($secureKey)
    {
        $this->secureKey = $secureKey;

        return $this;
    }

    /**
     * Get secureKey
     *
     * @return string
     */
    public function getSecureKey()
    {
        return $this->secureKey;
    }

    /**
     * Set recyclable
     *
     * @param boolean $recyclable
     *
     * @return Cart
     */
    public function setRecyclable($recyclable)
    {
        $this->recyclable = $recyclable;

        return $this;
    }

    /**
     * Get recyclable
     *
     * @return boolean
     */
    public function getRecyclable()
    {
        return $this->recyclable;
    }

    /**
     * Set gift
     *
     * @param boolean $gift
     *
     * @return Cart
     */
    public function setGift($gift)
    {
        $this->gift = $gift;

        return $this;
    }

    /**
     * Get gift
     *
     * @return boolean
     */
    public function getGift()
    {
        return $this->gift;
    }

    /**
     * Set giftMessage
     *
     * @param string $giftMessage
     *
     * @return Cart
     */
    public function setGiftMessage($giftMessage)
    {
        $this->giftMessage = $giftMessage;

        return $this;
    }

    /**
     * Get giftMessage
     *
     * @return string
     */
    public function getGiftMessage()
    {
        return $this->giftMessage;
    }

    /**
     * Set mobileTheme
     *
     * @param boolean $mobileTheme
     *
     * @return Cart
     */
    public function setMobileTheme($mobileTheme)
    {
        $this->mobileTheme = $mobileTheme;

        return $this;
    }

    /**
     * Get mobileTheme
     *
     * @return boolean
     */
    public function getMobileTheme()
    {
        return $this->mobileTheme;
    }

    /**
     * Set allowSeperatedPackage
     *
     * @param boolean $allowSeperatedPackage
     *
     * @return Cart
     */
    public function setAllowSeperatedPackage($allowSeperatedPackage)
    {
        $this->allowSeperatedPackage = $allowSeperatedPackage;

        return $this;
    }

    /**
     * Get allowSeperatedPackage
     *
     * @return boolean
     */
    public function getAllowSeperatedPackage()
    {
        return $this->allowSeperatedPackage;
    }

    /**
     * Set dateAdd
     *
     * @param \DateTime $dateAdd
     *
     * @return Cart
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
     * @return Cart
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
     * Set checkoutSessionData
     *
     * @param string $checkoutSessionData
     *
     * @return Cart
     */
    public function setCheckoutSessionData($checkoutSessionData)
    {
        $this->checkoutSessionData = $checkoutSessionData;

        return $this;
    }

    /**
     * Get checkoutSessionData
     *
     * @return string
     */
    public function getCheckoutSessionData()
    {
        return $this->checkoutSessionData;
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
}
