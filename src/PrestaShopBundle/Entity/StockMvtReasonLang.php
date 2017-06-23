<?php

namespace PrestaShopBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * StockMvtReasonLang
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class StockMvtReasonLang
{
    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255, nullable=false)
     */
    private $name;

    /**
     * @var integer
     *
     * @ORM\Column(name="id_stock_mvt_reason", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $idStockMvtReason;

    /**
     * @var integer
     *
     * @ORM\Column(name="id_lang", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $idLang;



    /**
     * Set name
     *
     * @param string $name
     *
     * @return StockMvtReasonLang
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set idStockMvtReason
     *
     * @param integer $idStockMvtReason
     *
     * @return StockMvtReasonLang
     */
    public function setIdStockMvtReason($idStockMvtReason)
    {
        $this->idStockMvtReason = $idStockMvtReason;

        return $this;
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

    /**
     * Set idLang
     *
     * @param integer $idLang
     *
     * @return StockMvtReasonLang
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
}
