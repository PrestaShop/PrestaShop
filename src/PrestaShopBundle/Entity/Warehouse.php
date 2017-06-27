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
 * Warehouse
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class Warehouse
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id_currency", type="integer", nullable=false)
     */
    private $idCurrency;

    /**
     * @var integer
     *
     * @ORM\Column(name="id_address", type="integer", nullable=false)
     */
    private $idAddress;

    /**
     * @var integer
     *
     * @ORM\Column(name="id_employee", type="integer", nullable=false)
     */
    private $idEmployee;

    /**
     * @var string
     *
     * @ORM\Column(name="reference", type="string", length=32, nullable=true)
     */
    private $reference;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=45, nullable=false)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="management_type", type="string", nullable=false, columnDefinition="ENUM('WA','FIFO','LIFO')", options={"default"="WA"})
     */
    private $managementType = 'WA';

    /**
     * @var boolean
     *
     * @ORM\Column(name="deleted", type="boolean", nullable=false, options={"default":0})
     */
    private $deleted = '0';

    /**
     * @var integer
     *
     * @ORM\Column(name="id_warehouse", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $idWarehouse;



    /**
     * Set idCurrency
     *
     * @param integer $idCurrency
     *
     * @return Warehouse
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
     * Set idAddress
     *
     * @param integer $idAddress
     *
     * @return Warehouse
     */
    public function setIdAddress($idAddress)
    {
        $this->idAddress = $idAddress;

        return $this;
    }

    /**
     * Get idAddress
     *
     * @return integer
     */
    public function getIdAddress()
    {
        return $this->idAddress;
    }

    /**
     * Set idEmployee
     *
     * @param integer $idEmployee
     *
     * @return Warehouse
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
     * Set reference
     *
     * @param string $reference
     *
     * @return Warehouse
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
     * Set name
     *
     * @param string $name
     *
     * @return Warehouse
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
     * Set managementType
     *
     * @param string $managementType
     *
     * @return Warehouse
     */
    public function setManagementType($managementType)
    {
        $this->managementType = $managementType;

        return $this;
    }

    /**
     * Get managementType
     *
     * @return string
     */
    public function getManagementType()
    {
        return $this->managementType;
    }

    /**
     * Set deleted
     *
     * @param boolean $deleted
     *
     * @return Warehouse
     */
    public function setDeleted($deleted)
    {
        $this->deleted = $deleted;

        return $this;
    }

    /**
     * Get deleted
     *
     * @return boolean
     */
    public function getDeleted()
    {
        return $this->deleted;
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
}
