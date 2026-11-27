<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\Profile\Employee\CommandHandler;

use Employee;
use PrestaShop\PrestaShop\Adapter\Employee\TwoFactor\EmployeeBackupCodeGenerator;
use PrestaShop\PrestaShop\Adapter\LegacyContext;
use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsCommandHandler;
use PrestaShop\PrestaShop\Core\Crypto\Hashing;
use PrestaShop\PrestaShop\Core\Domain\Employee\Command\EditEmployeeCommand;
use PrestaShop\PrestaShop\Core\Domain\Employee\CommandHandler\EditEmployeeHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Employee\Exception\EmailAlreadyUsedException;
use PrestaShop\PrestaShop\Core\Domain\Employee\Exception\EmployeeException;
use PrestaShop\PrestaShop\Core\Domain\Employee\Exception\InvalidProfileException;
use PrestaShop\PrestaShop\Core\Domain\Employee\Exception\MissingShopAssociationException;
use PrestaShop\PrestaShop\Core\Domain\Employee\ValueObject\GeneratedEmployeeBackupCodes;
use PrestaShop\PrestaShop\Core\Employee\Access\ProfileAccessCheckerInterface;
use PrestaShop\PrestaShop\Core\Employee\ContextEmployeeProviderInterface;
use PrestaShopBundle\Entity\Repository\EmployeeRepository;
use Shop;

/**
 * Handles command which edits employee using legacy object model
 *
 * @internal
 */
#[AsCommandHandler]
final class EditEmployeeHandler extends AbstractEmployeeHandler implements EditEmployeeHandlerInterface
{
    /**
     * @var Hashing
     */
    private $hashing;

    /**
     * @var ProfileAccessCheckerInterface
     */
    private $profileAccessChecker;

    /**
     * @var ContextEmployeeProviderInterface
     */
    private $contextEmployeeProvider;

    /**
     * @var LegacyContext
     */
    private $legacyContext;

    /**
     * @param Hashing $hashing
     * @param ProfileAccessCheckerInterface $profileAccessChecker
     * @param ContextEmployeeProviderInterface $contextEmployeeProvider
     * @param LegacyContext $legacyContext
     */
    public function __construct(
        Hashing $hashing,
        ProfileAccessCheckerInterface $profileAccessChecker,
        ContextEmployeeProviderInterface $contextEmployeeProvider,
        LegacyContext $legacyContext,
        private readonly EmployeeBackupCodeGenerator $backupCodeGenerator,
        private readonly EmployeeRepository $employeeRepository,
    ) {
        $this->hashing = $hashing;
        $this->profileAccessChecker = $profileAccessChecker;
        $this->contextEmployeeProvider = $contextEmployeeProvider;
        $this->legacyContext = $legacyContext;
    }

    /**
     * {@inheritdoc}
     */
    public function handle(EditEmployeeCommand $command): ?GeneratedEmployeeBackupCodes
    {
        $employee = new Employee($command->getEmployeeId()->getValue());
        $this->assertEmployeeWasFoundById($command->getEmployeeId(), $employee);

        if (null !== $command->getProfileId()) {
            $canAccessProfile = $this->profileAccessChecker->canEmployeeAccessProfile(
                $this->contextEmployeeProvider->getId(),
                (int) $command->getProfileId()
            );

            if (!$canAccessProfile) {
                throw new InvalidProfileException('You cannot access the provided profile.');
            }
        }

        if (null !== $command->getEmail()) {
            $this->assertEmailIsNotAlreadyUsed($employee, $command->getEmail()->getValue());
        }
        $this->assertHomepageIsAccessible($command->getDefaultPageId() ?: ((int) $employee->default_tab), $command->getProfileId() ?: ((int) $employee->id_profile));

        $hasTwoFactorChanges = null !== $command->getTwoFactorEnabled()
            || null !== $command->getTwoFactorTotpEnabled()
            || null !== $command->getTwoFactorEmailEnabled();
        $twoFactorState = $hasTwoFactorChanges
            ? $this->employeeRepository->getTwoFactorStateSnapshot((int) $employee->id)
            : null;
        if (null !== $twoFactorState) {
            // Decide the transition and capture its comparison token in the same read. ObjectModel
            // may have cached older flags; omitted command fields must preserve the current flags.
            $employee->two_factor_enabled = $twoFactorState['two_factor_enabled'];
            $employee->two_factor_totp_enabled = $twoFactorState['two_factor_totp_enabled'];
            $employee->two_factor_email_enabled = $twoFactorState['two_factor_email_enabled'];
        }
        $wasEnabled = (bool) $employee->two_factor_enabled
            && ($employee->two_factor_totp_enabled || $employee->two_factor_email_enabled);
        $isEnabled = ($command->getTwoFactorEnabled() ?? (bool) $employee->two_factor_enabled)
            && (($command->getTwoFactorTotpEnabled() ?? (bool) $employee->two_factor_totp_enabled)
                || ($command->getTwoFactorEmailEnabled() ?? (bool) $employee->two_factor_email_enabled));
        // Prepare randomness and hashes before any write; an active account keeps even an exhausted set.
        $backupCodeSet = $hasTwoFactorChanges && !$wasEnabled && $isEnabled
            ? $this->backupCodeGenerator->generateBackupCodeSet()
            : null;

        $this->updateEmployeeWithCommandData($employee, $command);

        if (null !== $command->getPlainPassword() && $employee->id == $this->contextEmployeeProvider->getId()) {
            $this->updatePasswordInCookie($employee);
        }

        /*
         * ObjectModel (including its hooks) and Doctrine use separate connections. These writes
         * were already non-atomic: a Doctrine transaction cannot roll back a legacy update.
         * Keep hook ordering, write only the backup-code column, and never return plaintext on failure.
         * Form uploads, extra properties and after-update hooks still run after this command: if they
         * fail, the account may already have codes. Recovery must be explicit regeneration, never
         * compensation or replay of this command against a potentially newer code set.
         */
        if ($hasTwoFactorChanges && !$isEnabled) {
            $this->employeeRepository->clearBackupCodes((int) $employee->id);
        } elseif (null !== $backupCodeSet) {
            $this->employeeRepository->saveBackupCodes((int) $employee->id, $backupCodeSet['hashedBackupCodes'], $twoFactorState['two_factor_backup_codes'] ?? null);

            return new GeneratedEmployeeBackupCodes((int) $employee->id, $backupCodeSet['plainBackupCodes']);
        }

        return null;
    }

    /**
     * Update employee object model with data from employee edit command.
     *
     * @param Employee $employee
     * @param EditEmployeeCommand $command
     *
     * @throws EmployeeException
     */
    private function updateEmployeeWithCommandData(Employee $employee, EditEmployeeCommand $command)
    {
        // Only the fields set on the command are updated, the others keep their value
        if (null !== $command->getFirstName()) {
            $employee->firstname = $command->getFirstName()->getValue();
        }
        if (null !== $command->getLastName()) {
            $employee->lastname = $command->getLastName()->getValue();
        }
        if (null !== $command->getEmail()) {
            $employee->email = $command->getEmail()->getValue();
        }
        if (null !== $command->getDefaultPageId()) {
            $employee->default_tab = $command->getDefaultPageId();
        }
        if (null !== $command->getLanguageId()) {
            $employee->id_lang = $command->getLanguageId();
        }
        $employee->id_last_order = $employee->getLastElementsForNotify('order');
        $employee->id_last_customer_message = $employee->getLastElementsForNotify('customer_message');
        $employee->id_last_customer = $employee->getLastElementsForNotify('customer');
        if (null !== $command->hasEnabledGravatar()) {
            $employee->has_enabled_gravatar = $command->hasEnabledGravatar();
        }

        if (null !== $command->getTwoFactorEnabled()) {
            $employee->two_factor_enabled = $command->getTwoFactorEnabled();
        }
        if (null !== $command->getTwoFactorTotpEnabled()) {
            $employee->two_factor_totp_enabled = $command->getTwoFactorTotpEnabled();
        }
        if (null !== $command->getTwoFactorEmailEnabled()) {
            $employee->two_factor_email_enabled = $command->getTwoFactorEmailEnabled();
        }

        if (null !== $command->getTwoFactorRequired()) {
            $employee->two_factor_required = $command->getTwoFactorRequired();
        }

        if (false === $command->getTwoFactorTotpEnabled()) {
            $employee->two_factor_totp_secret = null;
        }

        // Allow changing profile and active status only when editing not own account.
        if ((int) $employee->id !== $this->contextEmployeeProvider->getId()) {
            if (null !== $command->getProfileId()) {
                $employee->id_profile = $command->getProfileId();
            }
            if (null !== $command->isActive()) {
                $employee->active = $command->isActive();
            }
        }

        $shopAssociation = $command->getShopAssociation();

        if (null !== $shopAssociation && !$employee->isSuperAdmin() && empty($shopAssociation)) {
            throw new MissingShopAssociationException('Employee must be associated to at least one shop.');
        }

        if (null !== $command->getPlainPassword()) {
            $employee->passwd = $this->hashing->hash($command->getPlainPassword()->getValue());
        }

        if (false === $employee->update()) {
            throw new EmployeeException(sprintf('Cannot update employee with id "%s"', $employee->id));
        }

        if ($employee->isSuperAdmin()) {
            $shopAssociation = array_values(Shop::getShops(false, null, true));
        }

        // Allow changing shop association only when editing not own account.
        if (null !== $shopAssociation && $employee->id != $this->contextEmployeeProvider->getId()) {
            $this->associateWithShops($employee, $shopAssociation);
        }
    }

    /**
     * @param Employee $employee
     * @param string $email
     *
     * @throws EmailAlreadyUsedException
     */
    private function assertEmailIsNotAlreadyUsed(Employee $employee, $email)
    {
        // Don't count own email as usage.
        if ($employee->email === $email) {
            return;
        }

        if (Employee::employeeExists($email)) {
            throw new EmailAlreadyUsedException($email, 'An account already exists for this email address');
        }
    }

    /**
     * Update employee password in cookie.
     *
     * @param Employee $employee
     */
    private function updatePasswordInCookie(Employee $employee)
    {
        $this->legacyContext->getContext()->cookie->passwd = $employee->passwd;
        $this->legacyContext->getContext()->employee->passwd = $employee->passwd;
        $this->legacyContext->getContext()->cookie->write();
    }
}
