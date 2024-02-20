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
 * - mutation table: which table was modified
 * - mutation row ID: ID identifying which row of the modified table was modified
 * - action performed (create, update, delete) identified by an enum to remain small in DB
 * - mutator type: Employee, ApiClient or Module
 * - mutator identifier: Identifier of associated mutator (usually an int matching the row, but can be a technical name for a module)
 *
 * @ORM\Table
 * @ORM\HasLifecycleCallbacks
 * @ORM\Entity(repositoryClass="PrestaShopBundle\Entity\Repository\MutationRepository")
 */
class Mutation
{
    /**
     * @ORM\Id
     * @ORM\Column(name="id_mutation", type="integer", options={"unsigned": true})
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private int $id;

    /**
     * @ORM\Column(name="mutation_table", type="string", length=255)
     */
    private string $mutationTable;

    /**
     * @ORM\Column(name="mutation_row_id", type="bigint")
     */
    private int $mutationRowId;

    /**
     * @ORM\Column(name="mutation_action", enumType="PrestaShopBundle\Entity\MutationAction", columnDefinition="ENUM('create', 'update', 'delete')"))
     */
    private MutationAction $action;

    /**
     * @ORM\Column(name="mutator_type", enumType="PrestaShopBundle\Entity\MutatorType", columnDefinition="ENUM('employee', 'api_client', 'module')")
     */
    private MutatorType $mutatorType;

    /**
     * @ORM\Column(name="mutator_identifier", type="string", length=255)
     */
    private string $mutatorIdentifier;

    /**
     * @ORM\Column(name="mutation_details", type="string", length=255, nullable=true)
     */
    private string $mutationDetails;

    /**
     * @ORM\Column(name="date_add", type="datetime", nullable=false)
     */
    private DateTime $dateAdd;

    public function getId(): int
    {
        return $this->id;
    }

    public function getMutationTable(): string
    {
        return $this->mutationTable;
    }

    public function setMutationTable(string $mutationTable): self
    {
        $this->mutationTable = $mutationTable;

        return $this;
    }

    public function getMutationRowId(): int
    {
        return $this->mutationRowId;
    }

    public function setMutationRowId(int $mutationRowId): self
    {
        $this->mutationRowId = $mutationRowId;

        return $this;
    }

    public function getAction(): MutationAction
    {
        return $this->action;
    }

    public function setAction(MutationAction $action): self
    {
        $this->action = $action;

        return $this;
    }

    public function getMutatorType(): MutatorType
    {
        return $this->mutatorType;
    }

    public function setMutatorType(MutatorType $mutatorType): self
    {
        $this->mutatorType = $mutatorType;

        return $this;
    }

    public function getMutatorIdentifier(): string
    {
        return $this->mutatorIdentifier;
    }

    public function setMutatorIdentifier(string $mutatorIdentifier): self
    {
        $this->mutatorIdentifier = $mutatorIdentifier;

        return $this;
    }

    public function getMutationDetails(): string
    {
        return $this->mutationDetails;
    }

    public function setMutationDetails(string $mutationDetails): self
    {
        $this->mutationDetails = $mutationDetails;

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
