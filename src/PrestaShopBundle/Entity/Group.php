<?php

namespace PrestaShopBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Group
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class Group
{
    /**
     * @var string
     *
     * @ORM\Column(name="reduction", type="decimal", precision=17, scale=2, nullable=false)
     */
    private $reduction = '0.00';

    /**
     * @var boolean
     *
     * @ORM\Column(name="price_display_method", type="boolean", nullable=false)
     */
    private $priceDisplayMethod = '0';

    /**
     * @var boolean
     *
     * @ORM\Column(name="show_prices", type="boolean", nullable=false)
     */
    private $showPrices = '1';

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
     * @ORM\Column(name="id_group", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $idGroup;



    /**
     * Set reduction
     *
     * @param string $reduction
     *
     * @return Group
     */
    public function setReduction($reduction)
    {
        $this->reduction = $reduction;

        return $this;
    }

    /**
     * Get reduction
     *
     * @return string
     */
    public function getReduction()
    {
        return $this->reduction;
    }

    /**
     * Set priceDisplayMethod
     *
     * @param boolean $priceDisplayMethod
     *
     * @return Group
     */
    public function setPriceDisplayMethod($priceDisplayMethod)
    {
        $this->priceDisplayMethod = $priceDisplayMethod;

        return $this;
    }

    /**
     * Get priceDisplayMethod
     *
     * @return boolean
     */
    public function getPriceDisplayMethod()
    {
        return $this->priceDisplayMethod;
    }

    /**
     * Set showPrices
     *
     * @param boolean $showPrices
     *
     * @return Group
     */
    public function setShowPrices($showPrices)
    {
        $this->showPrices = $showPrices;

        return $this;
    }

    /**
     * Get showPrices
     *
     * @return boolean
     */
    public function getShowPrices()
    {
        return $this->showPrices;
    }

    /**
     * Set dateAdd
     *
     * @param \DateTime $dateAdd
     *
     * @return Group
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
     * @return Group
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
     * Get idGroup
     *
     * @return integer
     */
    public function getIdGroup()
    {
        return $this->idGroup;
    }
}
