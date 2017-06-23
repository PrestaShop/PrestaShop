<?php

namespace PrestaShopBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * OrderInvoicePayment
 *
 * @ORM\Table(indexes={@ORM\Index(name="order_payment", columns={"id_order_payment"}), @ORM\Index(name="id_order", columns={"id_order"})})
 * @ORM\Entity
 */
class OrderInvoicePayment
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
     * @ORM\Column(name="id_order_invoice", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $idOrderInvoice;

    /**
     * @var integer
     *
     * @ORM\Column(name="id_order_payment", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $idOrderPayment;



    /**
     * Set idOrder
     *
     * @param integer $idOrder
     *
     * @return OrderInvoicePayment
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
     * @return OrderInvoicePayment
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
     * Set idOrderPayment
     *
     * @param integer $idOrderPayment
     *
     * @return OrderInvoicePayment
     */
    public function setIdOrderPayment($idOrderPayment)
    {
        $this->idOrderPayment = $idOrderPayment;

        return $this;
    }

    /**
     * Get idOrderPayment
     *
     * @return integer
     */
    public function getIdOrderPayment()
    {
        return $this->idOrderPayment;
    }
}
