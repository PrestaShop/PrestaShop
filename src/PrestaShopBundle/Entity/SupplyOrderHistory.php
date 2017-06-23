<?php

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
