<?php

namespace PrestaShopBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * OrderSlipDetailTax
 *
 * @ORM\Table(indexes={@ORM\Index(name="id_order_slip_detail", columns={"id_order_slip_detail"}), @ORM\Index(name="id_tax", columns={"id_tax"})})
 * @ORM\Entity
 */
class OrderSlipDetailTax
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id_tax", type="integer", nullable=false)
     */
    private $idTax;

    /**
     * @var string
     *
     * @ORM\Column(name="unit_amount", type="decimal", precision=16, scale=6, nullable=false)
     */
    private $unitAmount = '0.000000';

    /**
     * @var string
     *
     * @ORM\Column(name="total_amount", type="decimal", precision=16, scale=6, nullable=false)
     */
    private $totalAmount = '0.000000';

    /**
     * @var integer
     *
     * @ORM\Column(name="id_order_slip_detail", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $idOrderSlipDetail;



    /**
     * Set idTax
     *
     * @param integer $idTax
     *
     * @return OrderSlipDetailTax
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
     * Set unitAmount
     *
     * @param string $unitAmount
     *
     * @return OrderSlipDetailTax
     */
    public function setUnitAmount($unitAmount)
    {
        $this->unitAmount = $unitAmount;

        return $this;
    }

    /**
     * Get unitAmount
     *
     * @return string
     */
    public function getUnitAmount()
    {
        return $this->unitAmount;
    }

    /**
     * Set totalAmount
     *
     * @param string $totalAmount
     *
     * @return OrderSlipDetailTax
     */
    public function setTotalAmount($totalAmount)
    {
        $this->totalAmount = $totalAmount;

        return $this;
    }

    /**
     * Get totalAmount
     *
     * @return string
     */
    public function getTotalAmount()
    {
        return $this->totalAmount;
    }

    /**
     * Get idOrderSlipDetail
     *
     * @return integer
     */
    public function getIdOrderSlipDetail()
    {
        return $this->idOrderSlipDetail;
    }
}
