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

declare(strict_types=1);

namespace PrestaShopBundle\Entity;

use DateTime;
use Doctrine\ORM\Mapping as ORM;

/**
 * Mutation entity allows to track a modification/mutation performed on a table, usually in the BO, but it could
 * also allow tracking some modification in FO like a payment module changing an order status.
 *
 * As such the mutation needs to hold three mandatory elements:
 * - modified table
 * - modified row ID
 * - action performed (create, update, delete, ...)
 *
 * @ORM\Table
 * @ORM\HasLifecycleCallbacks
 * @ORM\Entity(repositoryClass="PrestaShopBundle\Entity\Repository\MutationRepository")
 */
class Mutation
{
    /**
     * @ORM\Id
     * @ORM\Column(name="id_mutation", type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private int $id;

    /**
     * @ORM\Column(name="mutated_table", type="string", length=255)
     */
    private string $table;

    /**
     * @ORM\Column(name="id_row", type="integer")
     */
    private int $rowId;

    /**
     * @ORM\Column(name="mutation_action", type="string", length=255)
     */
    private string $action;

    /**
     * @ORM\Column(name="id_employee", type="integer", nullable=true)
     */
    private ?int $employeeId = null;

    /**
     * @ORM\Column(name="id_api_client", type="integer", nullable=true)
     */
    private ?int $apiClientId = null;

    /**
     * @ORM\Column(name="id_module", type="integer", nullable=true)
     */
    private ?int $moduleId = null;

    /**
     * @ORM\Column(name="date_add", type="datetime", nullable=false)
     */
    private DateTime $dateAdd;

    public function getId(): int
    {
        return $this->id;
    }

    public function getTable(): string
    {
        return $this->table;
    }

    public function setTable(string $table): self
    {
        $this->table = $table;

        return $this;
    }

    public function getRowId(): int
    {
        return $this->rowId;
    }

    public function setRowId(int $rowId): self
    {
        $this->rowId = $rowId;

        return $this;
    }

    public function getAction(): string
    {
        return $this->action;
    }

    public function setAction(string $action): self
    {
        $this->action = $action;

        return $this;
    }

    public function getEmployeeId(): ?int
    {
        return $this->employeeId;
    }

    public function setEmployeeId(?int $employeeId): self
    {
        $this->employeeId = $employeeId;

        return $this;
    }

    public function getApiClientId(): ?int
    {
        return $this->apiClientId;
    }

    public function setApiClientId(?int $apiClientId): self
    {
        $this->apiClientId = $apiClientId;

        return $this;
    }

    public function getModuleId(): ?int
    {
        return $this->moduleId;
    }

    public function setModuleId(?int $moduleId): self
    {
        $this->moduleId = $moduleId;

        return $this;
    }

    public function getDateAdd(): DateTime
    {
        return $this->dateAdd;
    }

    /**
     * Now we tell doctrine that before we persist or update we call the updateTimestamps() function.
     *
     * @ORM\PrePersist
     * @ORM\PreUpdate
     */
    public function updateTimestamps()
    {
        $this->dateAdd = new DateTime();
    }
}
