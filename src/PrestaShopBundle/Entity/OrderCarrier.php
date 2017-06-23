<?php

namespace PrestaShopBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * OrderCarrier
 *
 * @ORM\Table(indexes={@ORM\Index(name="id_order", columns={"id_order"}), @ORM\Index(name="id_carrier", columns={"id_carrier"}), @ORM\Index(name="id_order_invoice", columns={"id_order_invoice"})})
 * @ORM\Entity
 */
class OrderCarrier
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
     * @ORM\Column(name="id_carrier", type="integer", nullable=false)
     */
    private $idCarrier;

    /**
     * @var integer
     *
     * @ORM\Column(name="id_order_invoice", type="integer", nullable=true)
     */
    private $idOrderInvoice;

    /**
     * @var string
     *
     * @ORM\Column(name="weight", type="decimal", precision=20, scale=6, nullable=true)
     */
    private $weight;

    /**
     * @var string
     *
     * @ORM\Column(name="shipping_cost_tax_excl", type="decimal", precision=20, scale=6, nullable=true)
     */
    private $shippingCostTaxExcl;

    /**
     * @var string
     *
     * @ORM\Column(name="shipping_cost_tax_incl", type="decimal", precision=20, scale=6, nullable=true)
     */
    private $shippingCostTaxIncl;

    /**
     * @var string
     *
     * @ORM\Column(name="tracking_number", type="string", length=64, nullable=true)
     */
    private $trackingNumber;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date_add", type="datetime", nullable=false)
     */
    private $dateAdd;

    /**
     * @var integer
     *
     * @ORM\Column(name="id_order_carrier", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $idOrderCarrier;



    /**
     * Set idOrder
     *
     * @param integer $idOrder
     *
     * @return OrderCarrier
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
     * Set idCarrier
     *
     * @param integer $idCarrier
     *
     * @return OrderCarrier
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
     * Set idOrderInvoice
     *
     * @param integer $idOrderInvoice
     *
     * @return OrderCarrier
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
     * Set weight
     *
     * @param string $weight
     *
     * @return OrderCarrier
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
     * Set shippingCostTaxExcl
     *
     * @param string $shippingCostTaxExcl
     *
     * @return OrderCarrier
     */
    public function setShippingCostTaxExcl($shippingCostTaxExcl)
    {
        $this->shippingCostTaxExcl = $shippingCostTaxExcl;

        return $this;
    }

    /**
     * Get shippingCostTaxExcl
     *
     * @return string
     */
    public function getShippingCostTaxExcl()
    {
        return $this->shippingCostTaxExcl;
    }

    /**
     * Set shippingCostTaxIncl
     *
     * @param string $shippingCostTaxIncl
     *
     * @return OrderCarrier
     */
    public function setShippingCostTaxIncl($shippingCostTaxIncl)
    {
        $this->shippingCostTaxIncl = $shippingCostTaxIncl;

        return $this;
    }

    /**
     * Get shippingCostTaxIncl
     *
     * @return string
     */
    public function getShippingCostTaxIncl()
    {
        return $this->shippingCostTaxIncl;
    }

    /**
     * Set trackingNumber
     *
     * @param string $trackingNumber
     *
     * @return OrderCarrier
     */
    public function setTrackingNumber($trackingNumber)
    {
        $this->trackingNumber = $trackingNumber;

        return $this;
    }

    /**
     * Get trackingNumber
     *
     * @return string
     */
    public function getTrackingNumber()
    {
        return $this->trackingNumber;
    }

    /**
     * Set dateAdd
     *
     * @param \DateTime $dateAdd
     *
     * @return OrderCarrier
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
     * Get idOrderCarrier
     *
     * @return integer
     */
    public function getIdOrderCarrier()
    {
        return $this->idOrderCarrier;
    }
}
