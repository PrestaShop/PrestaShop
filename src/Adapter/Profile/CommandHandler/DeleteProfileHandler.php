<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Adapter\Profile\CommandHandler;

use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsCommandHandler;
use PrestaShop\PrestaShop\Core\Domain\Profile\Command\DeleteProfileCommand;
use PrestaShop\PrestaShop\Core\Domain\Profile\CommandHandler\DeleteProfileHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Profile\Exception\CannotDeleteSuperAdminProfileException;
use PrestaShop\PrestaShop\Core\Domain\Profile\Exception\FailedToDeleteProfileException;
use PrestaShop\PrestaShop\Core\Domain\Profile\Exception\ProfileException;
use PrestaShop\PrestaShop\Core\Domain\Profile\Exception\ProfileNotFoundException;
use PrestaShop\PrestaShop\Core\Employee\ContextEmployeeProviderInterface;
use PrestaShopException;
use Profile;

/**
 * Class DeleteProfileHandler
 *
 * @internal
 */
#[AsCommandHandler]
final class DeleteProfileHandler extends AbstractProfileHandler implements DeleteProfileHandlerInterface
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
    public function handle(DeleteProfileCommand $command)
    {
        $entityId = $command->getProfileId()->getValue();

        try {
            $entity = new Profile($entityId);

            if ($entity->id != $entityId) {
                throw new ProfileNotFoundException(sprintf('Profile with id %s cannot be found.', var_export($entityId, true)));
            }

            if ($this->contextEmployeeProvider->getProfileId() === $entity->id) {
                throw new FailedToDeleteProfileException(sprintf('Failed to delete Profile with id %s', var_export($entityId, true)), FailedToDeleteProfileException::PROFILE_IS_ASSIGNED_TO_CONTEXT_EMPLOYEE);
            }

            $this->assertProfileIsNotAssignedToEmployee($entity);

            if ($entity->id == $this->superAdminProfileId) {
                throw new CannotDeleteSuperAdminProfileException(sprintf('Cannot delete Profile with id %s', var_export($entityId, true)));
            }

            if (false === $entity->delete()) {
                throw new FailedToDeleteProfileException(sprintf('Failed to delete Profile with id %s', var_export($entityId, true)));
            }
        } catch (PrestaShopException $e) {
            throw new ProfileException(sprintf('Unexpected error occurred when deleting Profile with id %s', var_export($entityId, true)), 0, $e);
        }
    }
}
