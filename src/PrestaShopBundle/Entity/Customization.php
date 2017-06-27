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
 * Customization
 *
 * @ORM\Table(indexes={@ORM\Index(name="id_product_attribute", columns={"id_product_attribute"}), @ORM\Index(name="id_cart_product", columns={"id_cart", "id_product", "id_product_attribute"})})
 * @ORM\Entity
 */
class Customization
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id_product_attribute", type="integer", nullable=false, options={"default":0})
     */
    private $idProductAttribute = '0';

    /**
     * @var integer
     *
     * @ORM\Column(name="quantity", type="integer", nullable=false)
     */
    private $quantity;

    /**
     * @var integer
     *
     * @ORM\Column(name="quantity_refunded", type="integer", nullable=false, options={"default":0})
     */
    private $quantityRefunded = '0';

    /**
     * @var integer
     *
     * @ORM\Column(name="quantity_returned", type="integer", nullable=false, options={"default":0})
     */
    private $quantityReturned = '0';

    /**
     * @var boolean
     *
     * @ORM\Column(name="in_cart", type="boolean", nullable=false, options={"default":0})
     */
    private $inCart = '0';

    /**
     * @var integer
     *
     * @ORM\Column(name="id_customization", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $idCustomization;

    /**
     * @var integer
     *
     * @ORM\Column(name="id_cart", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $idCart;

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
     * @ORM\Column(name="id_address_delivery", type="integer", options={"default":0})
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $idAddressDelivery;



    /**
     * Set idProductAttribute
     *
     * @param integer $idProductAttribute
     *
     * @return Customization
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
     * Set quantity
     *
     * @param integer $quantity
     *
     * @return Customization
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
     * Set quantityRefunded
     *
     * @param integer $quantityRefunded
     *
     * @return Customization
     */
    public function setQuantityRefunded($quantityRefunded)
    {
        $this->quantityRefunded = $quantityRefunded;

        return $this;
    }

    /**
     * Get quantityRefunded
     *
     * @return integer
     */
    public function getQuantityRefunded()
    {
        return $this->quantityRefunded;
    }

    /**
     * Set quantityReturned
     *
     * @param integer $quantityReturned
     *
     * @return Customization
     */
    public function setQuantityReturned($quantityReturned)
    {
        $this->quantityReturned = $quantityReturned;

        return $this;
    }

    /**
     * Get quantityReturned
     *
     * @return integer
     */
    public function getQuantityReturned()
    {
        return $this->quantityReturned;
    }

    /**
     * Set inCart
     *
     * @param boolean $inCart
     *
     * @return Customization
     */
    public function setInCart($inCart)
    {
        $this->inCart = $inCart;

        return $this;
    }

    /**
     * Get inCart
     *
     * @return boolean
     */
    public function getInCart()
    {
        return $this->inCart;
    }

    /**
     * Set idCustomization
     *
     * @param integer $idCustomization
     *
     * @return Customization
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
     * Set idCart
     *
     * @param integer $idCart
     *
     * @return Customization
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
     * @return Customization
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
     * Set idAddressDelivery
     *
     * @param integer $idAddressDelivery
     *
     * @return Customization
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
}
