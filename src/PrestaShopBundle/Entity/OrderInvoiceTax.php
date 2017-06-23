<?php

namespace PrestaShopBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * OrderInvoiceTax
 *
 * @ORM\Table(indexes={@ORM\Index(name="id_tax", columns={"id_tax"})})
 * @ORM\Entity
 */
class OrderInvoiceTax
{
    /**
     * @var string
     *
     * @ORM\Column(name="type", type="string", length=15, nullable=false)
     */
    private $type;

    /**
     * @var integer
     *
     * @ORM\Column(name="id_tax", type="integer", nullable=false)
     */
    private $idTax;

    /**
     * @var string
     *
     * @ORM\Column(name="amount", type="decimal", precision=10, scale=6, nullable=false)
     */
    private $amount = '0.000000';

    /**
     * @var integer
     *
     * @ORM\Column(name="id_order_invoice", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $idOrderInvoice;



    /**
     * Set type
     *
     * @param string $type
     *
     * @return OrderInvoiceTax
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set idTax
     *
     * @param integer $idTax
     *
     * @return OrderInvoiceTax
     */
    public function setIdTax($idTax)
    {
        $this->idTax = $idTax;

        return $this;
    }

    /**
     * Get idTax
     *
     * @return integer
     */
    public function getIdTax()
    {
        return $this->idTax;
    }

    /**
     * Set amount
     *
     * @param string $amount
     *
     * @return OrderInvoiceTax
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
     * Get idOrderInvoice
     *
     * @return integer
     */
    public function getIdOrderInvoice()
    {
        return $this->idOrderInvoice;
    }
}
