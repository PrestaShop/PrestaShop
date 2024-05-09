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

use DateTime;
use Doctrine\ORM\Mapping as ORM;
use PrestaShop\PrestaShop\Adapter\Configuration;

/**
 * StockMvt.
 *
 * @ORM\Table(indexes={@ORM\Index(name="id_stock", columns={"id_stock"}), @ORM\Index(name="id_stock_mvt_reason", columns={"id_stock_mvt_reason"})})
 *
 * @ORM\Entity(repositoryClass="PrestaShopBundle\Entity\Repository\StockMovementRepository")
 */
class StockMvt
{
    /**
     * @ORM\Column(name="id_stock_mvt", type="bigint")
     *
     * @ORM\Id
     *
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private int $idStockMvt;

    /**
     * @ORM\Column(name="id_stock", type="integer", nullable=false)
     */
    private int $idStock;

    /**
     * @ORM\Column(name="id_order", type="integer", nullable=true)
     */
    private ?int $idOrder;

    /**
     * @ORM\Column(name="id_stock_mvt_reason", type="integer", nullable=false)
     */
    private int $idStockMvtReason;

    /**
     * @ORM\Column(name="id_employee", type="integer", nullable=false)
     */
    private int $idEmployee = 0;

    /**
     * @ORM\Column(name="employee_lastname", type="string", length=255, nullable=true)
     */
    private ?string $employeeLastname = '';

    /**
     * @ORM\Column(name="employee_firstname", type="string", length=255, nullable=true)
     */
    private ?string $employeeFirstname = '';

    /**
     * @ORM\Column(name="physical_quantity", type="integer", nullable=false, options={"unsigned":true})
     */
    private int $physicalQuantity;

    /**
     * @var DateTime
     *
     * @ORM\Column(name="date_add", type="datetime", nullable=false)
     */
    private DateTime $dateAdd;

    /**
     * @ORM\Column(name="sign", type="smallint", nullable=false, options={"default":1})
     */
    private int $sign = 1;

    public function __construct()
    {
        $configuration = new Configuration();

        // Default movement reason in case nothing else was provided
        $this->setIdStockMvtReason(
            $this->getSign() >= 1 ?
            $configuration->get('PS_STOCK_MVT_INC_EMPLOYEE_EDITION') :
            $configuration->get('PS_STOCK_MVT_DEC_EMPLOYEE_EDITION')
        );
    }

    public function getIdStockMvt(): int
    {
        return $this->idStockMvt;
    }

    public function setIdStock(int $idStock): static
    {
        $this->idStock = $idStock;

        return $this;
    }

    public function getIdStock(): int
    {
        return $this->idStock;
    }

    public function setIdOrder(?int $idOrder): static
    {
        $this->idOrder = $idOrder;

        return $this;
    }

    public function getIdOrder(): ?int
    {
        return $this->idOrder;
    }

    public function setIdStockMvtReason(int $idStockMvtReason): static
    {
        $this->idStockMvtReason = $idStockMvtReason;

        return $this;
    }

    public function getIdStockMvtReason(): int
    {
        return $this->idStockMvtReason;
    }

    public function setIdEmployee(int $idEmployee): static
    {
        $this->idEmployee = $idEmployee;

        return $this;
    }

    public function getIdEmployee(): int
    {
        return $this->idEmployee;
    }

    public function setEmployeeLastname(?string $employeeLastname): static
    {
        $this->employeeLastname = $employeeLastname;

        return $this;
    }

    public function getEmployeeLastname(): ?string
    {
        return $this->employeeLastname;
    }

    public function setEmployeeFirstname(?string $employeeFirstname): static
    {
        $this->employeeFirstname = $employeeFirstname;

        return $this;
    }

    public function getEmployeeFirstname(): ?string
    {
        return $this->employeeFirstname;
    }

    public function setPhysicalQuantity(int $physicalQuantity): static
    {
        $this->physicalQuantity = $physicalQuantity;

        return $this;
    }

    public function getPhysicalQuantity(): int
    {
        return $this->physicalQuantity;
    }

    public function setDateAdd(DateTime $dateAdd): static
    {
        $this->dateAdd = $dateAdd;

        return $this;
    }

    public function getDateAdd(): DateTime
    {
        return $this->dateAdd;
    }

    public function setSign(int $sign): static
    {
        $this->sign = $sign;

        return $this;
    }

    public function getSign(): int
    {
        return $this->sign;
    }
}
