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
