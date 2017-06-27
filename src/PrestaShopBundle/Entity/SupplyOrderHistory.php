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
 * SupplyOrderHistory
 *
 * @ORM\Table(indexes={@ORM\Index(name="id_supply_order", columns={"id_supply_order"}), @ORM\Index(name="id_employee", columns={"id_employee"}), @ORM\Index(name="id_state", columns={"id_state"})})
 * @ORM\Entity
 */
class SupplyOrderHistory
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id_supply_order", type="integer", nullable=false)
     */
    private $idSupplyOrder;

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
     * @ORM\Column(name="id_state", type="integer", nullable=false)
     */
    private $idState;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date_add", type="datetime", nullable=false)
     */
    private $dateAdd;

    /**
     * @var integer
     *
     * @ORM\Column(name="id_supply_order_history", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $idSupplyOrderHistory;



    /**
     * Set idSupplyOrder
     *
     * @param integer $idSupplyOrder
     *
     * @return SupplyOrderHistory
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
     * Set idEmployee
     *
     * @param integer $idEmployee
     *
     * @return SupplyOrderHistory
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
     * @return SupplyOrderHistory
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
     * @return SupplyOrderHistory
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
     * Set idState
     *
     * @param integer $idState
     *
     * @return SupplyOrderHistory
     */
    public function setIdState($idState)
    {
        $this->idState = $idState;

        return $this;
    }

    /**
     * Get idState
     *
     * @return integer
     */
    public function getIdState()
    {
        return $this->idState;
    }

    /**
     * Set dateAdd
     *
     * @param \DateTime $dateAdd
     *
     * @return SupplyOrderHistory
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
     * Get idSupplyOrderHistory
     *
     * @return integer
     */
    public function getIdSupplyOrderHistory()
    {
        return $this->idSupplyOrderHistory;
    }
}
