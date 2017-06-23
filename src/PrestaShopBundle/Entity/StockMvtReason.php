<?php

namespace PrestaShopBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * StockMvtReason
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class StockMvtReason
{
    /**
     * @var boolean
     *
     * @ORM\Column(name="sign", type="boolean", nullable=false)
     */
    private $sign = '1';

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
     * @var boolean
     *
     * @ORM\Column(name="deleted", type="boolean", nullable=false)
     */
    private $deleted = '0';

    /**
     * @var integer
     *
     * @ORM\Column(name="id_stock_mvt_reason", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $idStockMvtReason;



    /**
     * Set sign
     *
     * @param boolean $sign
     *
     * @return StockMvtReason
     */
    public function setSign($sign)
    {
        $this->sign = $sign;

        return $this;
    }

    /**
     * Get sign
     *
     * @return boolean
     */
    public function getSign()
    {
        return $this->sign;
    }

    /**
     * Set dateAdd
     *
     * @param \DateTime $dateAdd
     *
     * @return StockMvtReason
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
     * @return StockMvtReason
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
     * Set deleted
     *
     * @param boolean $deleted
     *
     * @return StockMvtReason
     */
    public function setDeleted($deleted)
    {
        $this->deleted = $deleted;

        return $this;
    }

    /**
     * Get deleted
     *
     * @return boolean
     */
    public function getDeleted()
    {
        return $this->deleted;
    }

    /**
     * Get idStockMvtReason
     *
     * @return integer
     */
    public function getIdStockMvtReason()
    {
        return $this->idStockMvtReason;
    }
}
