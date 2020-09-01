<?php
/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.md.
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
 * needs please refer to https://devdocs.prestashop.com/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

namespace PrestaShopBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use PrestaShop\PrestaShop\Adapter\Configuration;

/**
 * StockMvt.
 *
 * @ORM\Table(indexes={@ORM\Index(name="id_stock", columns={"id_stock"}), @ORM\Index(name="id_stock_mvt_reason", columns={"id_stock_mvt_reason"})})
 * @ORM\Entity(repositoryClass="PrestaShopBundle\Entity\Repository\StockMovementRepository")
 */
class StockMvt
{
    /**
     * @var int
     *
     * @ORM\Column(name="id_stock_mvt", type="bigint")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $idStockMvt;

    /**
     * @var int
     *
     * @ORM\Column(name="id_stock", type="integer", nullable=false)
     */
    private $idStock;

    /**
     * @var int
     *
     * @ORM\Column(name="id_order", type="integer", nullable=true)
     */
    private $idOrder;

    /**
     * @var int
     *
     * @ORM\Column(name="id_supply_order", type="integer", nullable=true)
     */
    private $idSupplyOrder;

    /**
     * @var int
     *
     * @ORM\Column(name="id_stock_mvt_reason", type="integer", nullable=false)
     */
    private $idStockMvtReason;

    /**
     * @var int
     *
     * @ORM\Column(name="id_employee", type="integer", nullable=false)
     */
    private $idEmployee;

    /**
     * @var string
     *
     * @ORM\Column(name="employee_lastname", type="string", length=32, nullable=true)
     */
    private $employeeLastname = '';

    /**
     * @var string
     *
     * @ORM\Column(name="employee_firstname", type="string", length=32, nullable=true)
     */
    private $employeeFirstname = '';

    /**
     * @var int
     *
     * @ORM\Column(name="physical_quantity", type="integer", nullable=false)
     */
    private $physicalQuantity;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date_add", type="datetime", nullable=false)
     */
    private $dateAdd;

    /**
     * @var int
     *
     * @ORM\Column(name="sign", type="smallint", nullable=false, options={"default":1})
     */
    private $sign = '1';

    /**
     * @var string
     *
     * @ORM\Column(name="price_te", type="decimal", precision=20, scale=6, nullable=true, options={"default":"0.000000"})
     */
    private $priceTe = '0.000000';

    /**
     * @var string
     *
     * @ORM\Column(name="last_wa", type="decimal", precision=20, scale=6, nullable=true, options={"default":"0.000000"})
     */
    private $lastWa = '0.000000';

    /**
     * @var string
     *
     * @ORM\Column(name="current_wa", type="decimal", precision=20, scale=6, nullable=true, options={"default":"0.000000"})
     */
    private $currentWa = '0.000000';

    /**
     * @var int
     *
     * @ORM\Column(name="referer", type="bigint", nullable=true)
     */
    private $referer;

    public function __construct()
    {
        $configuration = new Configuration();
        $this->setIdStockMvtReason($this->getSign() >= 1 ? $configuration->get('PS_STOCK_MVT_INC_EMPLOYEE_EDITION') : $configuration->get('PS_STOCK_MVT_DEC_EMPLOYEE_EDITION'));
    }

    /**
     * Get idStockMvt.
     *
     * @return int
     */
    public function getIdStockMvt()
    {
        return $this->idStockMvt;
    }

    /**
     * Set idStock.
     *
     * @param int $idStock
     *
     * @return StockMvt
     */
    public function setIdStock($idStock)
    {
        $this->idStock = $idStock;

        return $this;
    }

    /**
     * Get idStock.
     *
     * @return int
     */
    public function getIdStock()
    {
        return $this->idStock;
    }

    /**
     * Set idOrder.
     *
     * @param int $idOrder
     *
     * @return StockMvt
     */
    public function setIdOrder($idOrder)
    {
        $this->idOrder = $idOrder;

        return $this;
    }

    /**
     * Get idOrder.
     *
     * @return int
     */
    public function getIdOrder()
    {
        return $this->idOrder;
    }

    /**
     * Set idSupplyOrder.
     *
     * @param int $idSupplyOrder
     *
     * @return StockMvt
     */
    public function setIdSupplyOrder($idSupplyOrder)
    {
        $this->idSupplyOrder = $idSupplyOrder;

        return $this;
    }

    /**
     * Get idSupplyOrder.
     *
     * @return int
     */
    public function getIdSupplyOrder()
    {
        return $this->idSupplyOrder;
    }

    /**
     * Set idStockMvtReason.
     *
     * @param int $idStockMvtReason
     *
     * @return StockMvt
     */
    public function setIdStockMvtReason($idStockMvtReason)
    {
        $this->idStockMvtReason = $idStockMvtReason;

        return $this;
    }

    /**
     * Get idStockMvtReason.
     *
     * @return int
     */
    public function getIdStockMvtReason()
    {
        return $this->idStockMvtReason;
    }

    /**
     * Set idEmployee.
     *
     * @param int $idEmployee
     *
     * @return StockMvt
     */
    public function setIdEmployee($idEmployee)
    {
        $this->idEmployee = $idEmployee;

        return $this;
    }

    /**
     * Get idEmployee.
     *
     * @return int
     */
    public function getIdEmployee()
    {
        return $this->idEmployee;
    }

    /**
     * Set employeeLastname.
     *
     * @param string $employeeLastname
     *
     * @return StockMvt
     */
    public function setEmployeeLastname($employeeLastname)
    {
        $this->employeeLastname = $employeeLastname;

        return $this;
    }

    /**
     * Get employeeLastname.
     *
     * @return string
     */
    public function getEmployeeLastname()
    {
        return $this->employeeLastname;
    }

    /**
     * Set employeeFirstname.
     *
     * @param string $employeeFirstname
     *
     * @return StockMvt
     */
    public function setEmployeeFirstname($employeeFirstname)
    {
        $this->employeeFirstname = $employeeFirstname;

        return $this;
    }

    /**
     * Get employeeFirstname.
     *
     * @return string
     */
    public function getEmployeeFirstname()
    {
        return $this->employeeFirstname;
    }

    /**
     * Set physicalQuantity.
     *
     * @param int $physicalQuantity
     *
     * @return StockMvt
     */
    public function setPhysicalQuantity($physicalQuantity)
    {
        $this->physicalQuantity = $physicalQuantity;

        return $this;
    }

    /**
     * Get physicalQuantity.
     *
     * @return int
     */
    public function getPhysicalQuantity()
    {
        return $this->physicalQuantity;
    }

    /**
     * Set dateAdd.
     *
     * @param \DateTime $dateAdd
     *
     * @return StockMvt
     */
    public function setDateAdd($dateAdd)
    {
        $this->dateAdd = $dateAdd;

        return $this;
    }

    /**
     * Get dateAdd.
     *
     * @return \DateTime
     */
    public function getDateAdd()
    {
        return $this->dateAdd;
    }

    /**
     * Set sign.
     *
     * @param int $sign
     *
     * @return StockMvt
     */
    public function setSign($sign)
    {
        $this->sign = $sign;

        return $this;
    }

    /**
     * Get sign.
     *
     * @return int
     */
    public function getSign()
    {
        return $this->sign;
    }

    /**
     * Set priceTe.
     *
     * @param string $priceTe
     *
     * @return StockMvt
     */
    public function setPriceTe($priceTe)
    {
        $this->priceTe = $priceTe;

        return $this;
    }

    /**
     * Get priceTe.
     *
     * @return string
     */
    public function getPriceTe()
    {
        return $this->priceTe;
    }

    /**
     * Set lastWa.
     *
     * @param string $lastWa
     *
     * @return StockMvt
     */
    public function setLastWa($lastWa)
    {
        $this->lastWa = $lastWa;

        return $this;
    }

    /**
     * Get lastWa.
     *
     * @return string
     */
    public function getLastWa()
    {
        return $this->lastWa;
    }

    /**
     * Set currentWa.
     *
     * @param string $currentWa
     *
     * @return StockMvt
     */
    public function setCurrentWa($currentWa)
    {
        $this->currentWa = $currentWa;

        return $this;
    }

    /**
     * Get currentWa.
     *
     * @return string
     */
    public function getCurrentWa()
    {
        return $this->currentWa;
    }

    /**
     * Set referer.
     *
     * @param int $referer
     *
     * @return StockMvt
     */
    public function setReferer($referer)
    {
        $this->referer = $referer;

        return $this;
    }

    /**
     * Get referer.
     *
     * @return int
     */
    public function getReferer()
    {
        return $this->referer;
    }
}
