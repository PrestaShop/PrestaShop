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
 * SupplyOrder
 *
 * @ORM\Table(indexes={@ORM\Index(name="id_supplier", columns={"id_supplier"}), @ORM\Index(name="id_warehouse", columns={"id_warehouse"}), @ORM\Index(name="reference", columns={"reference"})})
 * @ORM\Entity
 */
class SupplyOrder
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id_supplier", type="integer", nullable=false)
     */
    private $idSupplier;

    /**
     * @var string
     *
     * @ORM\Column(name="supplier_name", type="string", length=64, nullable=false)
     */
    private $supplierName;

    /**
     * @var integer
     *
     * @ORM\Column(name="id_lang", type="integer", nullable=false)
     */
    private $idLang;

    /**
     * @var integer
     *
     * @ORM\Column(name="id_warehouse", type="integer", nullable=false)
     */
    private $idWarehouse;

    /**
     * @var integer
     *
     * @ORM\Column(name="id_supply_order_state", type="integer", nullable=false)
     */
    private $idSupplyOrderState;

    /**
     * @var integer
     *
     * @ORM\Column(name="id_currency", type="integer", nullable=false)
     */
    private $idCurrency;

    /**
     * @var integer
     *
     * @ORM\Column(name="id_ref_currency", type="integer", nullable=false)
     */
    private $idRefCurrency;

    /**
     * @var string
     *
     * @ORM\Column(name="reference", type="string", length=64, nullable=false)
     */
    private $reference;

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
     * @var \DateTime
     *
     * @ORM\Column(name="date_delivery_expected", type="datetime", nullable=true)
     */
    private $dateDeliveryExpected;

    /**
     * @var string
     *
     * @ORM\Column(name="total_te", type="decimal", precision=20, scale=6, nullable=true)
     */
    private $totalTe = '0.000000';

    /**
     * @var string
     *
     * @ORM\Column(name="total_with_discount_te", type="decimal", precision=20, scale=6, nullable=true)
     */
    private $totalWithDiscountTe = '0.000000';

    /**
     * @var string
     *
     * @ORM\Column(name="total_tax", type="decimal", precision=20, scale=6, nullable=true)
     */
    private $totalTax = '0.000000';

    /**
     * @var string
     *
     * @ORM\Column(name="total_ti", type="decimal", precision=20, scale=6, nullable=true)
     */
    private $totalTi = '0.000000';

    /**
     * @var string
     *
     * @ORM\Column(name="discount_rate", type="decimal", precision=20, scale=6, nullable=true)
     */
    private $discountRate = '0.000000';

    /**
     * @var string
     *
     * @ORM\Column(name="discount_value_te", type="decimal", precision=20, scale=6, nullable=true)
     */
    private $discountValueTe = '0.000000';

    /**
     * @var boolean
     *
     * @ORM\Column(name="is_template", type="boolean", nullable=true)
     */
    private $isTemplate = '0';

    /**
     * @var integer
     *
     * @ORM\Column(name="id_supply_order", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $idSupplyOrder;



    /**
     * Set idSupplier
     *
     * @param integer $idSupplier
     *
     * @return SupplyOrder
     */
    public function setIdSupplier($idSupplier)
    {
        $this->idSupplier = $idSupplier;

        return $this;
    }

    /**
     * Get idSupplier
     *
     * @return integer
     */
    public function getIdSupplier()
    {
        return $this->idSupplier;
    }

    /**
     * Set supplierName
     *
     * @param string $supplierName
     *
     * @return SupplyOrder
     */
    public function setSupplierName($supplierName)
    {
        $this->supplierName = $supplierName;

        return $this;
    }

    /**
     * Get supplierName
     *
     * @return string
     */
    public function getSupplierName()
    {
        return $this->supplierName;
    }

    /**
     * Set idLang
     *
     * @param integer $idLang
     *
     * @return SupplyOrder
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

    /**
     * Set idWarehouse
     *
     * @param integer $idWarehouse
     *
     * @return SupplyOrder
     */
    public function setIdWarehouse($idWarehouse)
    {
        $this->idWarehouse = $idWarehouse;

        return $this;
    }

    /**
     * Get idWarehouse
     *
     * @return integer
     */
    public function getIdWarehouse()
    {
        return $this->idWarehouse;
    }

    /**
     * Set idSupplyOrderState
     *
     * @param integer $idSupplyOrderState
     *
     * @return SupplyOrder
     */
    public function setIdSupplyOrderState($idSupplyOrderState)
    {
        $this->idSupplyOrderState = $idSupplyOrderState;

        return $this;
    }

    /**
     * Get idSupplyOrderState
     *
     * @return integer
     */
    public function getIdSupplyOrderState()
    {
        return $this->idSupplyOrderState;
    }

    /**
     * Set idCurrency
     *
     * @param integer $idCurrency
     *
     * @return SupplyOrder
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
     * Set idRefCurrency
     *
     * @param integer $idRefCurrency
     *
     * @return SupplyOrder
     */
    public function setIdRefCurrency($idRefCurrency)
    {
        $this->idRefCurrency = $idRefCurrency;

        return $this;
    }

    /**
     * Get idRefCurrency
     *
     * @return integer
     */
    public function getIdRefCurrency()
    {
        return $this->idRefCurrency;
    }

    /**
     * Set reference
     *
     * @param string $reference
     *
     * @return SupplyOrder
     */
    public function setReference($reference)
    {
        $this->reference = $reference;

        return $this;
    }

    /**
     * Get reference
     *
     * @return string
     */
    public function getReference()
    {
        return $this->reference;
    }

    /**
     * Set dateAdd
     *
     * @param \DateTime $dateAdd
     *
     * @return SupplyOrder
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
     * @return SupplyOrder
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
     * Set dateDeliveryExpected
     *
     * @param \DateTime $dateDeliveryExpected
     *
     * @return SupplyOrder
     */
    public function setDateDeliveryExpected($dateDeliveryExpected)
    {
        $this->dateDeliveryExpected = $dateDeliveryExpected;

        return $this;
    }

    /**
     * Get dateDeliveryExpected
     *
     * @return \DateTime
     */
    public function getDateDeliveryExpected()
    {
        return $this->dateDeliveryExpected;
    }

    /**
     * Set totalTe
     *
     * @param string $totalTe
     *
     * @return SupplyOrder
     */
    public function setTotalTe($totalTe)
    {
        $this->totalTe = $totalTe;

        return $this;
    }

    /**
     * Get totalTe
     *
     * @return string
     */
    public function getTotalTe()
    {
        return $this->totalTe;
    }

    /**
     * Set totalWithDiscountTe
     *
     * @param string $totalWithDiscountTe
     *
     * @return SupplyOrder
     */
    public function setTotalWithDiscountTe($totalWithDiscountTe)
    {
        $this->totalWithDiscountTe = $totalWithDiscountTe;

        return $this;
    }

    /**
     * Get totalWithDiscountTe
     *
     * @return string
     */
    public function getTotalWithDiscountTe()
    {
        return $this->totalWithDiscountTe;
    }

    /**
     * Set totalTax
     *
     * @param string $totalTax
     *
     * @return SupplyOrder
     */
    public function setTotalTax($totalTax)
    {
        $this->totalTax = $totalTax;

        return $this;
    }

    /**
     * Get totalTax
     *
     * @return string
     */
    public function getTotalTax()
    {
        return $this->totalTax;
    }

    /**
     * Set totalTi
     *
     * @param string $totalTi
     *
     * @return SupplyOrder
     */
    public function setTotalTi($totalTi)
    {
        $this->totalTi = $totalTi;

        return $this;
    }

    /**
     * Get totalTi
     *
     * @return string
     */
    public function getTotalTi()
    {
        return $this->totalTi;
    }

    /**
     * Set discountRate
     *
     * @param string $discountRate
     *
     * @return SupplyOrder
     */
    public function setDiscountRate($discountRate)
    {
        $this->discountRate = $discountRate;

        return $this;
    }

    /**
     * Get discountRate
     *
     * @return string
     */
    public function getDiscountRate()
    {
        return $this->discountRate;
    }

    /**
     * Set discountValueTe
     *
     * @param string $discountValueTe
     *
     * @return SupplyOrder
     */
    public function setDiscountValueTe($discountValueTe)
    {
        $this->discountValueTe = $discountValueTe;

        return $this;
    }

    /**
     * Get discountValueTe
     *
     * @return string
     */
    public function getDiscountValueTe()
    {
        return $this->discountValueTe;
    }

    /**
     * Set isTemplate
     *
     * @param boolean $isTemplate
     *
     * @return SupplyOrder
     */
    public function setIsTemplate($isTemplate)
    {
        $this->isTemplate = $isTemplate;

        return $this;
    }

    /**
     * Get isTemplate
     *
     * @return boolean
     */
    public function getIsTemplate()
    {
        return $this->isTemplate;
    }

    /**
     * Get idSupplyOrder
     *
     * @return integer
     */
    public function getIdSupplyOrder()
    {
        return $this->idSupplyOrder;
    }
}
