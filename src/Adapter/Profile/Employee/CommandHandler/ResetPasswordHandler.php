<?php
/**
 * 2007-2019 PrestaShop and Contributors
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
 * needs please refer to https://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2019 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShop\PrestaShop\Adapter\Profile\Employee\CommandHandler;

use Employee;
use Language;
use Mail;
use PrestaShop\PrestaShop\Adapter\LegacyContext;
use PrestaShop\PrestaShop\Core\ConfigurationInterface;
use PrestaShop\PrestaShop\Core\Crypto\Hashing;
use PrestaShop\PrestaShop\Core\Domain\Employee\Command\ResetPasswordCommand;
use PrestaShop\PrestaShop\Core\Domain\Employee\CommandHandler\ResetPasswordHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Employee\Exception\EmployeeConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Employee\Exception\EmployeeNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\Employee\Exception\PasswordResetTooFrequentException;
use PrestaShop\PrestaShop\Core\Domain\Employee\Exception\ResetPasswordTokenExpiredException;
use PrestaShop\PrestaShop\Core\Domain\Employee\Exception\UnableToResetPasswordException;
use PrestaShop\PrestaShop\Core\Domain\Employee\ValueObject\Password;
use Shop;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * Handles the command which resets employee's password.
 *
 * @internal
 */
final class ResetPasswordHandler implements ResetPasswordHandlerInterface
{
    /**
     * @var ConfigurationInterface
     */
    private $configuration;

    /**
     * @var Hashing
     */
    private $hashing;

    /**
     * @var LegacyContext
     */
    private $legacyContext;

    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * @param ConfigurationInterface $configuration
     * @param Hashing $hashing
     * @param LegacyContext $legacyContext
     * @param TranslatorInterface $translator
     */
    public function __construct(
        ConfigurationInterface $configuration,
        Hashing $hashing,
        LegacyContext $legacyContext,
        TranslatorInterface $translator
    ) {
        $this->configuration = $configuration;
        $this->hashing = $hashing;
        $this->legacyContext = $legacyContext;
        $this->translator = $translator;
    }

    /**
     * {@inheritdoc}
     */
    public function handle(ResetPasswordCommand $command)
    {
        $employee = new Employee();

        // Legacy condition. Employee object is loaded in $employee->getByEmail() call, so it must go first.
        $employeeFound = $employee->getByEmail($command->getEmail()->getValue())
            && $employee
            && $employee->id == $command->getEmployeeId()->getValue()
        ;

        if (!$employeeFound) {
            throw new EmployeeNotFoundException(null, 'Employee could not be found by given email.');
        }

        if (Password::MIN_LENGTH > strlen($command->getNewPlainPassword())) {
            throw new EmployeeConstraintException(
                sprintf(
                    'Password must be not shorter than %d symbols',
                    Password::MIN_LENGTH
                ),
                EmployeeConstraintException::INVALID_PASSWORD
            );
        }

        $timeExpression = sprintf(
            '%s + %s minutes',
            (string) $employee->last_passwd_gen,
            (string) $this->configuration->get('PS_PASSWD_TIME_BACK')
        );
        $canResetPassword = (strtotime($timeExpression) - time()) <= 0;

        if (!$canResetPassword) {
            throw new PasswordResetTooFrequentException('Password has been reset too recently, you must wait.');
        }

        if ($employee->getValidResetPasswordToken() !== $command->getResetToken()) {
            throw new ResetPasswordTokenExpiredException('Reset password token has expired.');
        }

        $employee->passwd = $this->hashing->hash($command->getNewPlainPassword());
        $employee->last_passwd_gen = date('Y-m-d H:i:s', time());

        $params = array(
            '{email}' => $employee->email,
            '{lastname}' => $employee->lastname,
            '{firstname}' => $employee->firstname,
        );

        $employeeLanguage = new Language((int) $this->legacyContext->getContext()->employee->id_lang);

        $mailWasSent = Mail::send(
            $employee->id_lang,
            'password',
            $this->translator->trans(
                'Your new password',
                [],
                'Emails.Subject',
                $employeeLanguage->locale
            ),
            $params,
            $employee->email,
            $employee->firstname . ' ' . $employee->lastname
        );

        if (!$mailWasSent) {
            throw new UnableToResetPasswordException('Password reset email could not be sent.');
        }

        // Update employee only if the mail can be sent
        Shop::setContext(Shop::CONTEXT_SHOP, (int) min($employee->getAssociatedShops()));

        if (!$employee->update()) {
            throw new UnableToResetPasswordException('Employee\'s password could not be updated.');
        }

        $employee->removeResetPasswordToken(); // Delete temporary reset token

        // Not sure if we need to call $employee->update() again, as it was called a few lines above,
        // but this is copied from the legacy logic and it's risky to change it
        // without knowing the reasoning behind it.
        $employee->update();
    }
}
