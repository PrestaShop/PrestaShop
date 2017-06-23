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
 * OrderInvoice
 *
 * @ORM\Table(indexes={@ORM\Index(name="id_order", columns={"id_order"})})
 * @ORM\Entity
 */
class OrderInvoice
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
     * @ORM\Column(name="number", type="integer", nullable=false)
     */
    private $number;

    /**
     * @var integer
     *
     * @ORM\Column(name="delivery_number", type="integer", nullable=false)
     */
    private $deliveryNumber;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="delivery_date", type="datetime", nullable=true)
     */
    private $deliveryDate;

    /**
     * @var string
     *
     * @ORM\Column(name="total_discount_tax_excl", type="decimal", precision=20, scale=6, nullable=false)
     */
    private $totalDiscountTaxExcl = '0.000000';

    /**
     * @var string
     *
     * @ORM\Column(name="total_discount_tax_incl", type="decimal", precision=20, scale=6, nullable=false)
     */
    private $totalDiscountTaxIncl = '0.000000';

    /**
     * @var string
     *
     * @ORM\Column(name="total_paid_tax_excl", type="decimal", precision=20, scale=6, nullable=false)
     */
    private $totalPaidTaxExcl = '0.000000';

    /**
     * @var string
     *
     * @ORM\Column(name="total_paid_tax_incl", type="decimal", precision=20, scale=6, nullable=false)
     */
    private $totalPaidTaxIncl = '0.000000';

    /**
     * @var string
     *
     * @ORM\Column(name="total_products", type="decimal", precision=20, scale=6, nullable=false)
     */
    private $totalProducts = '0.000000';

    /**
     * @var string
     *
     * @ORM\Column(name="total_products_wt", type="decimal", precision=20, scale=6, nullable=false)
     */
    private $totalProductsWt = '0.000000';

    /**
     * @var string
     *
     * @ORM\Column(name="total_shipping_tax_excl", type="decimal", precision=20, scale=6, nullable=false)
     */
    private $totalShippingTaxExcl = '0.000000';

    /**
     * @var string
     *
     * @ORM\Column(name="total_shipping_tax_incl", type="decimal", precision=20, scale=6, nullable=false)
     */
    private $totalShippingTaxIncl = '0.000000';

    /**
     * @var integer
     *
     * @ORM\Column(name="shipping_tax_computation_method", type="integer", nullable=false)
     */
    private $shippingTaxComputationMethod;

    /**
     * @var string
     *
     * @ORM\Column(name="total_wrapping_tax_excl", type="decimal", precision=20, scale=6, nullable=false)
     */
    private $totalWrappingTaxExcl = '0.000000';

    /**
     * @var string
     *
     * @ORM\Column(name="total_wrapping_tax_incl", type="decimal", precision=20, scale=6, nullable=false)
     */
    private $totalWrappingTaxIncl = '0.000000';

    /**
     * @var string
     *
     * @ORM\Column(name="shop_address", type="text", length=65535, nullable=true)
     */
    private $shopAddress;

    /**
     * @var string
     *
     * @ORM\Column(name="note", type="text", length=65535, nullable=true)
     */
    private $note;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date_add", type="datetime", nullable=false)
     */
    private $dateAdd;

    /**
     * @var integer
     *
     * @ORM\Column(name="id_order_invoice", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $idOrderInvoice;



    /**
     * Set idOrder
     *
     * @param integer $idOrder
     *
     * @return OrderInvoice
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
     * Set number
     *
     * @param integer $number
     *
     * @return OrderInvoice
     */
    public function setNumber($number)
    {
        $this->number = $number;

        return $this;
    }

    /**
     * Get number
     *
     * @return integer
     */
    public function getNumber()
    {
        return $this->number;
    }

    /**
     * Set deliveryNumber
     *
     * @param integer $deliveryNumber
     *
     * @return OrderInvoice
     */
    public function setDeliveryNumber($deliveryNumber)
    {
        $this->deliveryNumber = $deliveryNumber;

        return $this;
    }

    /**
     * Get deliveryNumber
     *
     * @return integer
     */
    public function getDeliveryNumber()
    {
        return $this->deliveryNumber;
    }

    /**
     * Set deliveryDate
     *
     * @param \DateTime $deliveryDate
     *
     * @return OrderInvoice
     */
    public function setDeliveryDate($deliveryDate)
    {
        $this->deliveryDate = $deliveryDate;

        return $this;
    }

    /**
     * Get deliveryDate
     *
     * @return \DateTime
     */
    public function getDeliveryDate()
    {
        return $this->deliveryDate;
    }

    /**
     * Set totalDiscountTaxExcl
     *
     * @param string $totalDiscountTaxExcl
     *
     * @return OrderInvoice
     */
    public function setTotalDiscountTaxExcl($totalDiscountTaxExcl)
    {
        $this->totalDiscountTaxExcl = $totalDiscountTaxExcl;

        return $this;
    }

    /**
     * Get totalDiscountTaxExcl
     *
     * @return string
     */
    public function getTotalDiscountTaxExcl()
    {
        return $this->totalDiscountTaxExcl;
    }

    /**
     * Set totalDiscountTaxIncl
     *
     * @param string $totalDiscountTaxIncl
     *
     * @return OrderInvoice
     */
    public function setTotalDiscountTaxIncl($totalDiscountTaxIncl)
    {
        $this->totalDiscountTaxIncl = $totalDiscountTaxIncl;

        return $this;
    }

    /**
     * Get totalDiscountTaxIncl
     *
     * @return string
     */
    public function getTotalDiscountTaxIncl()
    {
        return $this->totalDiscountTaxIncl;
    }

    /**
     * Set totalPaidTaxExcl
     *
     * @param string $totalPaidTaxExcl
     *
     * @return OrderInvoice
     */
    public function setTotalPaidTaxExcl($totalPaidTaxExcl)
    {
        $this->totalPaidTaxExcl = $totalPaidTaxExcl;

        return $this;
    }

    /**
     * Get totalPaidTaxExcl
     *
     * @return string
     */
    public function getTotalPaidTaxExcl()
    {
        return $this->totalPaidTaxExcl;
    }

    /**
     * Set totalPaidTaxIncl
     *
     * @param string $totalPaidTaxIncl
     *
     * @return OrderInvoice
     */
    public function setTotalPaidTaxIncl($totalPaidTaxIncl)
    {
        $this->totalPaidTaxIncl = $totalPaidTaxIncl;

        return $this;
    }

    /**
     * Get totalPaidTaxIncl
     *
     * @return string
     */
    public function getTotalPaidTaxIncl()
    {
        return $this->totalPaidTaxIncl;
    }

    /**
     * Set totalProducts
     *
     * @param string $totalProducts
     *
     * @return OrderInvoice
     */
    public function setTotalProducts($totalProducts)
    {
        $this->totalProducts = $totalProducts;

        return $this;
    }

    /**
     * Get totalProducts
     *
     * @return string
     */
    public function getTotalProducts()
    {
        return $this->totalProducts;
    }

    /**
     * Set totalProductsWt
     *
     * @param string $totalProductsWt
     *
     * @return OrderInvoice
     */
    public function setTotalProductsWt($totalProductsWt)
    {
        $this->totalProductsWt = $totalProductsWt;

        return $this;
    }

    /**
     * Get totalProductsWt
     *
     * @return string
     */
    public function getTotalProductsWt()
    {
        return $this->totalProductsWt;
    }

    /**
     * Set totalShippingTaxExcl
     *
     * @param string $totalShippingTaxExcl
     *
     * @return OrderInvoice
     */
    public function setTotalShippingTaxExcl($totalShippingTaxExcl)
    {
        $this->totalShippingTaxExcl = $totalShippingTaxExcl;

        return $this;
    }

    /**
     * Get totalShippingTaxExcl
     *
     * @return string
     */
    public function getTotalShippingTaxExcl()
    {
        return $this->totalShippingTaxExcl;
    }

    /**
     * Set totalShippingTaxIncl
     *
     * @param string $totalShippingTaxIncl
     *
     * @return OrderInvoice
     */
    public function setTotalShippingTaxIncl($totalShippingTaxIncl)
    {
        $this->totalShippingTaxIncl = $totalShippingTaxIncl;

        return $this;
    }

    /**
     * Get totalShippingTaxIncl
     *
     * @return string
     */
    public function getTotalShippingTaxIncl()
    {
        return $this->totalShippingTaxIncl;
    }

    /**
     * Set shippingTaxComputationMethod
     *
     * @param integer $shippingTaxComputationMethod
     *
     * @return OrderInvoice
     */
    public function setShippingTaxComputationMethod($shippingTaxComputationMethod)
    {
        $this->shippingTaxComputationMethod = $shippingTaxComputationMethod;

        return $this;
    }

    /**
     * Get shippingTaxComputationMethod
     *
     * @return integer
     */
    public function getShippingTaxComputationMethod()
    {
        return $this->shippingTaxComputationMethod;
    }

    /**
     * Set totalWrappingTaxExcl
     *
     * @param string $totalWrappingTaxExcl
     *
     * @return OrderInvoice
     */
    public function setTotalWrappingTaxExcl($totalWrappingTaxExcl)
    {
        $this->totalWrappingTaxExcl = $totalWrappingTaxExcl;

        return $this;
    }

    /**
     * Get totalWrappingTaxExcl
     *
     * @return string
     */
    public function getTotalWrappingTaxExcl()
    {
        return $this->totalWrappingTaxExcl;
    }

    /**
     * Set totalWrappingTaxIncl
     *
     * @param string $totalWrappingTaxIncl
     *
     * @return OrderInvoice
     */
    public function setTotalWrappingTaxIncl($totalWrappingTaxIncl)
    {
        $this->totalWrappingTaxIncl = $totalWrappingTaxIncl;

        return $this;
    }

    /**
     * Get totalWrappingTaxIncl
     *
     * @return string
     */
    public function getTotalWrappingTaxIncl()
    {
        return $this->totalWrappingTaxIncl;
    }

    /**
     * Set shopAddress
     *
     * @param string $shopAddress
     *
     * @return OrderInvoice
     */
    public function setShopAddress($shopAddress)
    {
        $this->shopAddress = $shopAddress;

        return $this;
    }

    /**
     * Get shopAddress
     *
     * @return string
     */
    public function getShopAddress()
    {
        return $this->shopAddress;
    }

    /**
     * Set note
     *
     * @param string $note
     *
     * @return OrderInvoice
     */
    public function setNote($note)
    {
        $this->note = $note;

        return $this;
    }

    /**
     * Get note
     *
     * @return string
     */
    public function getNote()
    {
        return $this->note;
    }

    /**
     * Set dateAdd
     *
     * @param \DateTime $dateAdd
     *
     * @return OrderInvoice
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
     * Get idOrderInvoice
     *
     * @return integer
     */
    public function getIdOrderInvoice()
    {
        return $this->idOrderInvoice;
    }
}
