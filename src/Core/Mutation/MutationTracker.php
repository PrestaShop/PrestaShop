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

/**
 * The mutation track service helps to add mutation from any service in the code base, it automatically
 * fills the related potential modifiers (employee and/or api client from the context). The purpose is
 * to let it automatically set these associations, but you can manually override those values if needed,
 * but as long as these parameters are empty the default value will always be set if present, so if you
 * need more accurate control on this association you should create and persist the mutation manually.
 *
 * It also contains a few action constants as the most usually used, but the action remains a customizable
 * field.
 */
class MutationTracker
{
    public const CREATE_ACTION = 'create';
    public const UPDATE_ACTION = 'update';
    public const DELETE_ACTION = 'delete';

    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly ApiClientContext $apiClientContext,
        private readonly EmployeeContext $employeeContext,
    ) {
    }

    /**
     * Add a mutation automatically filled with associations to employee and/or api client if they are available
     * in their respective context. You can also manually specify a module since we can't guess the module that is
     * performing the mutation.
     *
     * @param string $table
     * @param int $rowId
     * @param string $action
     * @param int|null $moduleId
     */
    public function addMutation(string $table, int $rowId, string $action, int $moduleId = null): void
    {
        $mutation = new Mutation();
        $mutation
            ->setTable($table)
            ->setRowId($rowId)
            ->setAction($action)
            ->setEmployeeId($this->employeeContext->getEmployee()?->getId())
            ->setApiClientId($this->apiClientContext->getApiClient()?->getId())
            ->setModuleId($moduleId)
        ;
        $this->entityManager->persist($mutation);
        $this->entityManager->flush();
    }
}
