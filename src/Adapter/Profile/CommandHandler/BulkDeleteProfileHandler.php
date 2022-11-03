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
 * needs please refer to https://devdocs.prestashop-project.org/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

namespace PrestaShop\PrestaShop\Adapter\Profile\CommandHandler;

use PrestaShop\PrestaShop\Core\Domain\Profile\Command\BulkDeleteProfileCommand;
use PrestaShop\PrestaShop\Core\Domain\Profile\CommandHandler\BulkDeleteProfileHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Profile\Exception\CannotDeleteSuperAdminProfileException;
use PrestaShop\PrestaShop\Core\Domain\Profile\Exception\FailedToDeleteProfileException;
use PrestaShop\PrestaShop\Core\Domain\Profile\Exception\ProfileException;
use PrestaShop\PrestaShop\Core\Domain\Profile\Exception\ProfileNotFoundException;
use PrestaShop\PrestaShop\Core\Employee\ContextEmployeeProviderInterface;
use PrestaShopException;
use Profile;

/**
 * Class BulkDeleteProfileHandler
 *
 * @internal
 */
final class BulkDeleteProfileHandler extends AbstractProfileHandler implements BulkDeleteProfileHandlerInterface
{
    /**
     * @var int
     */
    private $superAdminProfileId;

    /**
     * @var ContextEmployeeProviderInterface
     */
    private $contextEmployeeProvider;

    /**
     * @param int $superAdminProfileId
     * @param ContextEmployeeProviderInterface $contextEmployeeProvider
     */
    public function __construct($superAdminProfileId, ContextEmployeeProviderInterface $contextEmployeeProvider)
    {
        $this->superAdminProfileId = $superAdminProfileId;
        $this->contextEmployeeProvider = $contextEmployeeProvider;
    }

    /**
     * {@inheritdoc}
     */
    public function handle(BulkDeleteProfileCommand $command)
    {
        $entityIds = $command->getProfileIds();

        foreach ($entityIds as $entityId) {
            $entityIdValue = $entityId->getValue();

            try {
                $entity = new Profile($entityIdValue);

                if ($entity->id != $entityIdValue) {
                    throw new ProfileNotFoundException(sprintf('Profile with id %s cannot be found.', var_export($entityIdValue, true)));
                }

                if ($this->contextEmployeeProvider->getProfileId() === $entity->id) {
                    throw new FailedToDeleteProfileException(sprintf('Failed to delete Profile with id %s', var_export($entityIdValue, true)), FailedToDeleteProfileException::PROFILE_IS_ASSIGNED_TO_CONTEXT_EMPLOYEE);
                }

                if ($entity->id == $this->superAdminProfileId) {
                    throw new CannotDeleteSuperAdminProfileException(sprintf('Cannot delete Profile with id %s', var_export($entityIdValue, true)));
                }

                $this->assertProfileIsNotAssignedToEmployee($entity);

                if (false === $entity->delete()) {
                    throw new FailedToDeleteProfileException(sprintf('Failed to delete Profile with id %s', var_export($entityIdValue, true)), FailedToDeleteProfileException::UNEXPECTED_ERROR);
                }
            } catch (PrestaShopException $e) {
                throw new ProfileException(sprintf('Unexpected error occurred when deleting Profile with id %s', var_export($entityIdValue, true)), 0, $e);
            }
        }
    }
}
