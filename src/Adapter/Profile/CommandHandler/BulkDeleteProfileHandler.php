<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Adapter\Profile\CommandHandler;

use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsCommandHandler;
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
#[AsCommandHandler]
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
        $exceptionToThrowLater = null;

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
            } catch (FailedToDeleteProfileException $e) {
                if ($e->getCode() === FailedToDeleteProfileException::PROFILE_IS_ASSIGNED_TO_CONTEXT_EMPLOYEE) {
                    $exceptionToThrowLater = $e;
                    continue;
                }
                throw $e;
            }
        }

        if ($exceptionToThrowLater) {
            throw $exceptionToThrowLater;
        }
    }
}
