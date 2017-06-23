<?php

namespace PrestaShopBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * SpecificPriceRule
 *
 * @ORM\Table(indexes={@ORM\Index(name="id_product", columns={"id_shop", "id_currency", "id_country", "id_group", "from_quantity", "from", "to"})})
 * @ORM\Entity
 */
class SpecificPriceRule
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
     * @ORM\Column(name="id_shop", type="integer", nullable=false)
     */
    private $idShop = '1';

    /**
     * @var integer
     *
     * @ORM\Column(name="id_currency", type="integer", nullable=false)
     */
    private $idCurrency;

    /**
     * @var integer
     *
     * @ORM\Column(name="id_country", type="integer", nullable=false)
     */
    private $idCountry;

    /**
     * @var integer
     *
     * @ORM\Column(name="id_group", type="integer", nullable=false)
     */
    private $idGroup;

    /**
     * @var integer
     *
     * @ORM\Column(name="from_quantity", type="integer", nullable=false)
     */
    private $fromQuantity;

    /**
     * @var string
     *
     * @ORM\Column(name="price", type="decimal", precision=20, scale=6, nullable=true)
     */
    private $price;

    /**
     * @var string
     *
     * @ORM\Column(name="reduction", type="decimal", precision=20, scale=6, nullable=false)
     */
    private $reduction;

    /**
     * @var boolean
     *
     * @ORM\Column(name="reduction_tax", type="boolean", nullable=false)
     */
    private $reductionTax = '1';

    /**
     * @var string
     *
     * @ORM\Column(name="reduction_type", type="string", nullable=false)
     */
    private $reductionType;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="from", type="datetime", nullable=false)
     */
    private $from;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="to", type="datetime", nullable=false)
     */
    private $to;

    /**
     * @var integer
     *
     * @ORM\Column(name="id_specific_price_rule", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $idSpecificPriceRule;



    /**
     * Set name
     *
     * @param string $name
     *
     * @return SpecificPriceRule
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
     * Set idShop
     *
     * @param integer $idShop
     *
     * @return SpecificPriceRule
     */
    public function setIdShop($idShop)
    {
        $this->idShop = $idShop;

        return $this;
    }

    /**
     * Get idShop
     *
     * @return integer
     */
    public function getIdShop()
    {
        return $this->idShop;
    }

    /**
     * Set idCurrency
     *
     * @param integer $idCurrency
     *
     * @return SpecificPriceRule
     */
    public function setIdCurrency($idCurrency)
    {
        $this->idCurrency = $idCurrency;

        return $this;
    }

    /**
     * Get idCurrency
     *
     * @return integer
     */
    public function getIdCurrency()
    {
        return $this->idCurrency;
    }

    /**
     * Set idCountry
     *
     * @param integer $idCountry
     *
     * @return SpecificPriceRule
     */
    public function setIdCountry($idCountry)
    {
        $this->idCountry = $idCountry;

        return $this;
    }

    /**
     * Get idCountry
     *
     * @return integer
     */
    public function getIdCountry()
    {
        return $this->idCountry;
    }

    /**
     * Set idGroup
     *
     * @param integer $idGroup
     *
     * @return SpecificPriceRule
     */
    public function setIdGroup($idGroup)
    {
        $this->idGroup = $idGroup;

        return $this;
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

    /**
     * Set fromQuantity
     *
     * @param integer $fromQuantity
     *
     * @return SpecificPriceRule
     */
    public function setFromQuantity($fromQuantity)
    {
        $this->fromQuantity = $fromQuantity;

        return $this;
    }

    /**
     * Get fromQuantity
     *
     * @return integer
     */
    public function getFromQuantity()
    {
        return $this->fromQuantity;
    }

    /**
     * Set price
     *
     * @param string $price
     *
     * @return SpecificPriceRule
     */
    public function setPrice($price)
    {
        $this->price = $price;

        return $this;
    }

    /**
     * Get price
     *
     * @return string
     */
    public function getPrice()
    {
        return $this->price;
    }

    /**
     * Set reduction
     *
     * @param string $reduction
     *
     * @return SpecificPriceRule
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
     * Set reductionTax
     *
     * @param boolean $reductionTax
     *
     * @return SpecificPriceRule
     */
    public function setReductionTax($reductionTax)
    {
        $this->reductionTax = $reductionTax;

        return $this;
    }

    /**
     * Get reductionTax
     *
     * @return boolean
     */
    public function getReductionTax()
    {
        return $this->reductionTax;
    }

    /**
     * Set reductionType
     *
     * @param string $reductionType
     *
     * @return SpecificPriceRule
     */
    public function setReductionType($reductionType)
    {
        $this->reductionType = $reductionType;

        return $this;
    }

    /**
     * Get reductionType
     *
     * @return string
     */
    public function getReductionType()
    {
        return $this->reductionType;
    }

    /**
     * Set from
     *
     * @param \DateTime $from
     *
     * @return SpecificPriceRule
     */
    public function setFrom($from)
    {
        $this->from = $from;

        return $this;
    }

    /**
     * Get from
     *
     * @return \DateTime
     */
    public function getFrom()
    {
        return $this->from;
    }

    /**
     * Set to
     *
     * @param \DateTime $to
     *
     * @return SpecificPriceRule
     */
    public function setTo($to)
    {
        $this->to = $to;

        return $this;
    }

    /**
     * Get to
     *
     * @return \DateTime
     */
    public function getTo()
    {
        return $this->to;
    }

    /**
     * Get idSpecificPriceRule
     *
     * @return integer
     */
    public function getIdSpecificPriceRule()
    {
        return $this->idSpecificPriceRule;
    }
}
