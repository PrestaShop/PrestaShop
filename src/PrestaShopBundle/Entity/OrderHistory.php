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
 * OrderHistory
 *
 * @ORM\Table(indexes={@ORM\Index(name="order_history_order", columns={"id_order"}), @ORM\Index(name="id_employee", columns={"id_employee"}), @ORM\Index(name="id_order_state", columns={"id_order_state"})})
 * @ORM\Entity
 */
class OrderHistory
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id_employee", type="integer", nullable=false)
     */
    private $idEmployee;

    /**
     * @var integer
     *
     * @ORM\Column(name="id_order", type="integer", nullable=false)
     */
    private $idOrder;

    /**
     * @var integer
     *
     * @ORM\Column(name="id_order_state", type="integer", nullable=false)
     */
    private $idOrderState;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date_add", type="datetime", nullable=false)
     */
    private $dateAdd;

    /**
     * @var integer
     *
     * @ORM\Column(name="id_order_history", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $idOrderHistory;



    /**
     * Set idEmployee
     *
     * @param integer $idEmployee
     *
     * @return OrderHistory
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
     * Set idOrder
     *
     * @param integer $idOrder
     *
     * @return OrderHistory
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
     * Set idOrderState
     *
     * @param integer $idOrderState
     *
     * @return OrderHistory
     */
    public function setIdOrderState($idOrderState)
    {
        $this->idOrderState = $idOrderState;

        return $this;
    }

    /**
     * Get idOrderState
     *
     * @return integer
     */
    public function getIdOrderState()
    {
        return $this->idOrderState;
    }

    /**
     * Set dateAdd
     *
     * @param \DateTime $dateAdd
     *
     * @return OrderHistory
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
     * Get idOrderHistory
     *
     * @return integer
     */
    public function getIdOrderHistory()
    {
        return $this->idOrderHistory;
    }
}
