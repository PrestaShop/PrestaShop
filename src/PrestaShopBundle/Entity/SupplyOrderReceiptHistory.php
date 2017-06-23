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
 * SupplyOrderReceiptHistory
 *
 * @ORM\Table(indexes={@ORM\Index(name="id_supply_order_detail", columns={"id_supply_order_detail"}), @ORM\Index(name="id_supply_order_state", columns={"id_supply_order_state"})})
 * @ORM\Entity
 */
class SupplyOrderReceiptHistory
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id_supply_order_detail", type="integer", nullable=false)
     */
    private $idSupplyOrderDetail;

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
     * @ORM\Column(name="id_supply_order_state", type="integer", nullable=false)
     */
    private $idSupplyOrderState;

    /**
     * @var integer
     *
     * @ORM\Column(name="quantity", type="integer", nullable=false)
     */
    private $quantity;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date_add", type="datetime", nullable=false)
     */
    private $dateAdd;

    /**
     * @var integer
     *
     * @ORM\Column(name="id_supply_order_receipt_history", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $idSupplyOrderReceiptHistory;



    /**
     * Set idSupplyOrderDetail
     *
     * @param integer $idSupplyOrderDetail
     *
     * @return SupplyOrderReceiptHistory
     */
    public function setIdSupplyOrderDetail($idSupplyOrderDetail)
    {
        $this->idSupplyOrderDetail = $idSupplyOrderDetail;

        return $this;
    }

    /**
     * Get idSupplyOrderDetail
     *
     * @return integer
     */
    public function getIdSupplyOrderDetail()
    {
        return $this->idSupplyOrderDetail;
    }

    /**
     * Set idEmployee
     *
     * @param integer $idEmployee
     *
     * @return SupplyOrderReceiptHistory
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
     * @return SupplyOrderReceiptHistory
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
     * @return SupplyOrderReceiptHistory
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
     * Set idSupplyOrderState
     *
     * @param integer $idSupplyOrderState
     *
     * @return SupplyOrderReceiptHistory
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
     * Set quantity
     *
     * @param integer $quantity
     *
     * @return SupplyOrderReceiptHistory
     */
    public function setQuantity($quantity)
    {
        $this->quantity = $quantity;

        return $this;
    }

    /**
     * Get quantity
     *
     * @return integer
     */
    public function getQuantity()
    {
        return $this->quantity;
    }

    /**
     * Set dateAdd
     *
     * @param \DateTime $dateAdd
     *
     * @return SupplyOrderReceiptHistory
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
     * Get idSupplyOrderReceiptHistory
     *
     * @return integer
     */
    public function getIdSupplyOrderReceiptHistory()
    {
        return $this->idSupplyOrderReceiptHistory;
    }
}
