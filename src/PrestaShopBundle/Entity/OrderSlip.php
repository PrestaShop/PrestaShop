<?php

namespace PrestaShopBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * OrderSlip
 *
 * @ORM\Table(indexes={@ORM\Index(name="order_slip_customer", columns={"id_customer"}), @ORM\Index(name="id_order", columns={"id_order"})})
 * @ORM\Entity
 */
class OrderSlip
{
    /**
     * @var string
     *
     * @ORM\Column(name="conversion_rate", type="decimal", precision=13, scale=6, nullable=false)
     */
    private $conversionRate = '1.000000';

    /**
     * @var integer
     *
     * @ORM\Column(name="id_customer", type="integer", nullable=false)
     */
    private $idCustomer;

    /**
     * @var integer
     *
     * @ORM\Column(name="id_order", type="integer", nullable=false)
     */
    private $idOrder;

    /**
     * @var string
     *
     * @ORM\Column(name="total_products_tax_excl", type="decimal", precision=20, scale=6, nullable=true)
     */
    private $totalProductsTaxExcl;

    /**
     * @var string
     *
     * @ORM\Column(name="total_products_tax_incl", type="decimal", precision=20, scale=6, nullable=true)
     */
    private $totalProductsTaxIncl;

    /**
     * @var string
     *
     * @ORM\Column(name="total_shipping_tax_excl", type="decimal", precision=20, scale=6, nullable=true)
     */
    private $totalShippingTaxExcl;

    /**
     * @var string
     *
     * @ORM\Column(name="total_shipping_tax_incl", type="decimal", precision=20, scale=6, nullable=true)
     */
    private $totalShippingTaxIncl;

    /**
     * @var boolean
     *
     * @ORM\Column(name="shipping_cost", type="boolean", nullable=false)
     */
    private $shippingCost = '0';

    /**
     * @var string
     *
     * @ORM\Column(name="amount", type="decimal", precision=10, scale=2, nullable=false)
     */
    private $amount;

    /**
     * @var string
     *
     * @ORM\Column(name="shipping_cost_amount", type="decimal", precision=10, scale=2, nullable=false)
     */
    private $shippingCostAmount;

    /**
     * @var boolean
     *
     * @ORM\Column(name="partial", type="boolean", nullable=false)
     */
    private $partial;

    /**
     * @var boolean
     *
     * @ORM\Column(name="order_slip_type", type="boolean", nullable=false)
     */
    private $orderSlipType = '0';

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
     * @ORM\Column(name="id_order_slip", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $idOrderSlip;



    /**
     * Set conversionRate
     *
     * @param string $conversionRate
     *
     * @return OrderSlip
     */
    public function setConversionRate($conversionRate)
    {
        $this->conversionRate = $conversionRate;

        return $this;
    }

    /**
     * Get conversionRate
     *
     * @return string
     */
    public function getConversionRate()
    {
        return $this->conversionRate;
    }

    /**
     * Set idCustomer
     *
     * @param integer $idCustomer
     *
     * @return OrderSlip
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
     * Set idOrder
     *
     * @param integer $idOrder
     *
     * @return OrderSlip
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
     * Set totalProductsTaxExcl
     *
     * @param string $totalProductsTaxExcl
     *
     * @return OrderSlip
     */
    public function setTotalProductsTaxExcl($totalProductsTaxExcl)
    {
        $this->totalProductsTaxExcl = $totalProductsTaxExcl;

        return $this;
    }

    /**
     * Get totalProductsTaxExcl
     *
     * @return string
     */
    public function getTotalProductsTaxExcl()
    {
        return $this->totalProductsTaxExcl;
    }

    /**
     * Set totalProductsTaxIncl
     *
     * @param string $totalProductsTaxIncl
     *
     * @return OrderSlip
     */
    public function setTotalProductsTaxIncl($totalProductsTaxIncl)
    {
        $this->totalProductsTaxIncl = $totalProductsTaxIncl;

        return $this;
    }

    /**
     * Get totalProductsTaxIncl
     *
     * @return string
     */
    public function getTotalProductsTaxIncl()
    {
        return $this->totalProductsTaxIncl;
    }

    /**
     * Set totalShippingTaxExcl
     *
     * @param string $totalShippingTaxExcl
     *
     * @return OrderSlip
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
     * @return OrderSlip
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
     * Set shippingCost
     *
     * @param boolean $shippingCost
     *
     * @return OrderSlip
     */
    public function setShippingCost($shippingCost)
    {
        $this->shippingCost = $shippingCost;

        return $this;
    }

    /**
     * Get shippingCost
     *
     * @return boolean
     */
    public function getShippingCost()
    {
        return $this->shippingCost;
    }

    /**
     * Set amount
     *
     * @param string $amount
     *
     * @return OrderSlip
     */
    public function setAmount($amount)
    {
        $this->amount = $amount;

        return $this;
    }

    /**
     * Get amount
     *
     * @return string
     */
    public function getAmount()
    {
        return $this->amount;
    }

    /**
     * Set shippingCostAmount
     *
     * @param string $shippingCostAmount
     *
     * @return OrderSlip
     */
    public function setShippingCostAmount($shippingCostAmount)
    {
        $this->shippingCostAmount = $shippingCostAmount;

        return $this;
    }

    /**
     * Get shippingCostAmount
     *
     * @return string
     */
    public function getShippingCostAmount()
    {
        return $this->shippingCostAmount;
    }

    /**
     * Set partial
     *
     * @param boolean $partial
     *
     * @return OrderSlip
     */
    public function setPartial($partial)
    {
        $this->partial = $partial;

        return $this;
    }

    /**
     * Get partial
     *
     * @return boolean
     */
    public function getPartial()
    {
        return $this->partial;
    }

    /**
     * Set orderSlipType
     *
     * @param boolean $orderSlipType
     *
     * @return OrderSlip
     */
    public function setOrderSlipType($orderSlipType)
    {
        $this->orderSlipType = $orderSlipType;

        return $this;
    }

    /**
     * Get orderSlipType
     *
     * @return boolean
     */
    public function getOrderSlipType()
    {
        return $this->orderSlipType;
    }

    /**
     * Set dateAdd
     *
     * @param \DateTime $dateAdd
     *
     * @return OrderSlip
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
     * @return OrderSlip
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
     * Get idOrderSlip
     *
     * @return integer
     */
    public function getIdOrderSlip()
    {
        return $this->idOrderSlip;
    }
}
