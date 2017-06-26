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
 * OrderSlipDetail
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class OrderSlipDetail
{
    /**
     * @var integer
     *
     * @ORM\Column(name="product_quantity", type="integer", nullable=false, options={"default":0})
     */
    private $productQuantity = '0';

    /**
     * @var string
     *
     * @ORM\Column(name="unit_price_tax_excl", type="decimal", precision=20, scale=6, nullable=true)
     */
    private $unitPriceTaxExcl;

    /**
     * @var string
     *
     * @ORM\Column(name="unit_price_tax_incl", type="decimal", precision=20, scale=6, nullable=true)
     */
    private $unitPriceTaxIncl;

    /**
     * @var string
     *
     * @ORM\Column(name="total_price_tax_excl", type="decimal", precision=20, scale=6, nullable=true)
     */
    private $totalPriceTaxExcl;

    /**
     * @var string
     *
     * @ORM\Column(name="total_price_tax_incl", type="decimal", precision=20, scale=6, nullable=true)
     */
    private $totalPriceTaxIncl;

    /**
     * @var string
     *
     * @ORM\Column(name="amount_tax_excl", type="decimal", precision=20, scale=6, nullable=true)
     */
    private $amountTaxExcl;

    /**
     * @var string
     *
     * @ORM\Column(name="amount_tax_incl", type="decimal", precision=20, scale=6, nullable=true)
     */
    private $amountTaxIncl;

    /**
     * @var integer
     *
     * @ORM\Column(name="id_order_slip", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $idOrderSlip;

    /**
     * @var integer
     *
     * @ORM\Column(name="id_order_detail", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $idOrderDetail;



    /**
     * Set productQuantity
     *
     * @param integer $productQuantity
     *
     * @return OrderSlipDetail
     */
    public function setProductQuantity($productQuantity)
    {
        $this->productQuantity = $productQuantity;

        return $this;
    }

    /**
     * Get productQuantity
     *
     * @return integer
     */
    public function getProductQuantity()
    {
        return $this->productQuantity;
    }

    /**
     * Set unitPriceTaxExcl
     *
     * @param string $unitPriceTaxExcl
     *
     * @return OrderSlipDetail
     */
    public function setUnitPriceTaxExcl($unitPriceTaxExcl)
    {
        $this->unitPriceTaxExcl = $unitPriceTaxExcl;

        return $this;
    }

    /**
     * Get unitPriceTaxExcl
     *
     * @return string
     */
    public function getUnitPriceTaxExcl()
    {
        return $this->unitPriceTaxExcl;
    }

    /**
     * Set unitPriceTaxIncl
     *
     * @param string $unitPriceTaxIncl
     *
     * @return OrderSlipDetail
     */
    public function setUnitPriceTaxIncl($unitPriceTaxIncl)
    {
        $this->unitPriceTaxIncl = $unitPriceTaxIncl;

        return $this;
    }

    /**
     * Get unitPriceTaxIncl
     *
     * @return string
     */
    public function getUnitPriceTaxIncl()
    {
        return $this->unitPriceTaxIncl;
    }

    /**
     * Set totalPriceTaxExcl
     *
     * @param string $totalPriceTaxExcl
     *
     * @return OrderSlipDetail
     */
    public function setTotalPriceTaxExcl($totalPriceTaxExcl)
    {
        $this->totalPriceTaxExcl = $totalPriceTaxExcl;

        return $this;
    }

    /**
     * Get totalPriceTaxExcl
     *
     * @return string
     */
    public function getTotalPriceTaxExcl()
    {
        return $this->totalPriceTaxExcl;
    }

    /**
     * Set totalPriceTaxIncl
     *
     * @param string $totalPriceTaxIncl
     *
     * @return OrderSlipDetail
     */
    public function setTotalPriceTaxIncl($totalPriceTaxIncl)
    {
        $this->totalPriceTaxIncl = $totalPriceTaxIncl;

        return $this;
    }

    /**
     * Get totalPriceTaxIncl
     *
     * @return string
     */
    public function getTotalPriceTaxIncl()
    {
        return $this->totalPriceTaxIncl;
    }

    /**
     * Set amountTaxExcl
     *
     * @param string $amountTaxExcl
     *
     * @return OrderSlipDetail
     */
    public function setAmountTaxExcl($amountTaxExcl)
    {
        $this->amountTaxExcl = $amountTaxExcl;

        return $this;
    }

    /**
     * Get amountTaxExcl
     *
     * @return string
     */
    public function getAmountTaxExcl()
    {
        return $this->amountTaxExcl;
    }

    /**
     * Set amountTaxIncl
     *
     * @param string $amountTaxIncl
     *
     * @return OrderSlipDetail
     */
    public function setAmountTaxIncl($amountTaxIncl)
    {
        $this->amountTaxIncl = $amountTaxIncl;

        return $this;
    }

    /**
     * Get amountTaxIncl
     *
     * @return string
     */
    public function getAmountTaxIncl()
    {
        return $this->amountTaxIncl;
    }

    /**
     * Set idOrderSlip
     *
     * @param integer $idOrderSlip
     *
     * @return OrderSlipDetail
     */
    public function setIdOrderSlip($idOrderSlip)
    {
        $this->idOrderSlip = $idOrderSlip;

        return $this;
    }

    /**
     * Get idOrderSlip
     *
     * @return integer
     */
    public function getIdOrderSlip()
    {
        return $this->idOrderSlip;
    }

    /**
     * Set idOrderDetail
     *
     * @param integer $idOrderDetail
     *
     * @return OrderSlipDetail
     */
    public function setIdOrderDetail($idOrderDetail)
    {
        $this->idOrderDetail = $idOrderDetail;

        return $this;
    }

    /**
     * Get idOrderDetail
     *
     * @return integer
     */
    public function getIdOrderDetail()
    {
        return $this->idOrderDetail;
    }
}
