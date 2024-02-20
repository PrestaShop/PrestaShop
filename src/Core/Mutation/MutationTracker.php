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

namespace PrestaShop\PrestaShop\Core\Mutation;

use Doctrine\ORM\EntityManagerInterface;
use PrestaShop\PrestaShop\Core\Context\ApiClientContext;
use PrestaShop\PrestaShop\Core\Context\EmployeeContext;
use PrestaShopBundle\Entity\Mutation;
use PrestaShopBundle\Entity\MutationAction;
use PrestaShopBundle\Entity\MutatorType;

/**
 * The mutation track service helps to add mutation from any service in the code base, it automatically
 * fills the related potential modifiers (employee and/or api client from the context). The purpose is
 * to let it automatically set these associations based on the context services, except for module which
 * must be done manually.
 */
class MutationTracker
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly ApiClientContext $apiClientContext,
        private readonly EmployeeContext $employeeContext,
    ) {
    }

    /**
     * Add a mutation associated to the logged in Employee if present.
     */
    public function addMutationForEmployee(string $mutationTable, int $mutationRowId, MutationAction $action, string $mutationDetails = ''): void
    {
        if ($this->employeeContext->getEmployee()) {
            $this->persistMutation(
                $mutationTable,
                $mutationRowId,
                $action,
                MutatorType::EMPLOYEE,
                (string) $this->employeeContext->getEmployee()->getId(),
                $mutationDetails
            );
        }
    }

    /**
     * Adds mutation associated to the authenticated ApiClient if present.
     */
    public function addMutationForApiClient(string $mutationTable, int $mutationRowId, MutationAction $action, string $mutationDetails = ''): void
    {
        if ($this->apiClientContext->getApiClient()) {
            $this->persistMutation(
                $mutationTable,
                $mutationRowId,
                $action,
                MutatorType::API_CLIENT,
                (string) $this->apiClientContext->getApiClient()->getId(),
                $mutationDetails
            );
        }
    }

    /**
     * Add mutation for module, the identifier must be specified explicitly since it cannot be guesses based on context.
     */
    public function addMutationForModule(string $mutationTable, int $mutationRowId, MutationAction $action, string $moduleIdentifier, string $mutationDetails = ''): void
    {
        $this->persistMutation(
            $mutationTable,
            $mutationRowId,
            $action,
            MutatorType::MODULE,
            $moduleIdentifier,
            $mutationDetails
        );
    }

    private function persistMutation(
        string $mutationTable,
        int $mutationRowId,
        MutationAction $action,
        MutatorType $mutatorType,
        string $mutatorIdentifier,
        string $mutationDetails
    ): void {
        $mutation = new Mutation();
        $mutation
            ->setMutationTable($mutationTable)
            ->setMutationRowId($mutationRowId)
            ->setAction($action)
            ->setMutatorType($mutatorType)
            ->setMutatorIdentifier($mutatorIdentifier)
            ->setMutationDetails($mutationDetails)
        ;

        $this->entityManager->persist($mutation);
        $this->entityManager->flush();
    }
}
