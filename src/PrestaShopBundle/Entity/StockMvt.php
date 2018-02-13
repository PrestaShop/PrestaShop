<?php
/**
 * 2007-2018 PrestaShop
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
 * @copyright 2007-2018 PrestaShop SA
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */


namespace PrestaShopBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use PrestaShop\PrestaShop\Adapter\Configuration;

/**
 * StockMvt
 *
 * @ORM\Table(indexes={@ORM\Index(name="id_stock", columns={"id_stock"}), @ORM\Index(name="id_stock_mvt_reason", columns={"id_stock_mvt_reason"})})
 * @ORM\Entity(repositoryClass="PrestaShopBundle\Entity\Repository\StockMovementRepository")
 */
class StockMvt
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id_stock_mvt", type="bigint")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $idStockMvt;


    /**
     * @var integer
     *
     * @ORM\Column(name="id_stock", type="integer", nullable=false)
     */
    private $idStock;

    /**
     * @var integer
     *
     * @ORM\Column(name="id_order", type="integer", nullable=true)
     */
    private $idOrder;

    /**
     * @var integer
     *
     * @ORM\Column(name="id_supply_order", type="integer", nullable=true)
     */
    private $idSupplyOrder;

    /**
     * @var integer
     *
     * @ORM\Column(name="id_stock_mvt_reason", type="integer", nullable=false)
     */
    private $idStockMvtReason;

    /**
     * @var integer
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
     * @var integer
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
     * @var integer
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
     * @var integer
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
     * Get idStockMvt
     *
     * @return integer
     */
    public function getIdStockMvt()
    {
        return $this->idStockMvt;
    }

    /**
     * Set idStock
     *
     * @param integer $idStock
     *
     * @return StockMvt
     */
    public function setIdStock($idStock)
    {
        $this->idStock = $idStock;

        return $this;
    }

    /**
     * Get idStock
     *
     * @return integer
     */
    public function getIdStock()
    {
        return $this->idStock;
    }

    /**
     * Set idOrder
     *
     * @param integer $idOrder
     *
     * @return StockMvt
     */
    public function setIdOrder($idOrder)
    {
        $this->idOrder = $idOrder;

        return $this;
    }

    /**
     * Get idOrder
     *
     * @return integer
     */
    public function getIdOrder()
    {
        return $this->idOrder;
    }

    /**
     * Set idSupplyOrder
     *
     * @param integer $idSupplyOrder
     *
     * @return StockMvt
     */
    public function setIdSupplyOrder($idSupplyOrder)
    {
        $this->idSupplyOrder = $idSupplyOrder;

        return $this;
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

    /**
     * Set idStockMvtReason
     *
     * @param integer $idStockMvtReason
     *
     * @return StockMvt
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
     * Set idEmployee
     *
     * @param integer $idEmployee
     *
     * @return StockMvt
     */
    public function setIdEmployee($idEmployee)
    {
        $this->idEmployee = $idEmployee;

        return $this;
    }

    /**
     * Get idEmployee
     *
     * @return integer
     */
    public function getIdEmployee()
    {
        return $this->idEmployee;
    }

    /**
     * Set employeeLastname
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
     * Get employeeLastname
     *
     * @return string
     */
    public function getEmployeeLastname()
    {
        return $this->employeeLastname;
    }

    /**
     * Set employeeFirstname
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
     * Get employeeFirstname
     *
     * @return string
     */
    public function getEmployeeFirstname()
    {
        return $this->employeeFirstname;
    }

    /**
     * Set physicalQuantity
     *
     * @param integer $physicalQuantity
     *
     * @return StockMvt
     */
    public function setPhysicalQuantity($physicalQuantity)
    {
        $this->physicalQuantity = $physicalQuantity;

        return $this;
    }

    /**
     * Get physicalQuantity
     *
     * @return integer
     */
    public function getPhysicalQuantity()
    {
        return $this->physicalQuantity;
    }

    /**
     * Set dateAdd
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
     * Get dateAdd
     *
     * @return \DateTime
     */
    public function getDateAdd()
    {
        return $this->dateAdd;
    }

    /**
     * Set sign
     *
     * @param integer $sign
     *
     * @return StockMvt
     */
    public function setSign($sign)
    {
        $this->sign = $sign;

        return $this;
    }

    /**
     * Get sign
     *
     * @return integer
     */
    public function getSign()
    {
        return $this->sign;
    }

    /**
     * Set priceTe
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
     * Get priceTe
     *
     * @return string
     */
    public function getPriceTe()
    {
        return $this->priceTe;
    }

    /**
     * Set lastWa
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
     * Get lastWa
     *
     * @return string
     */
    public function getLastWa()
    {
        return $this->lastWa;
    }

    /**
     * Set currentWa
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
     * Get currentWa
     *
     * @return string
     */
    public function getCurrentWa()
    {
        return $this->currentWa;
    }

    /**
     * Set referer
     *
     * @param integer $referer
     *
     * @return StockMvt
     */
    public function setReferer($referer)
    {
        $this->referer = $referer;

        return $this;
    }

    /**
     * Get referer
     *
     * @return integer
     */
    public function getReferer()
    {
        return $this->referer;
    }
}
