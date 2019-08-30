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
use PrestaShop\PrestaShop\Core\ConfigurationInterface;
use PrestaShop\PrestaShop\Core\Domain\Employee\Command\SendResetPasswordEmailCommand;
use PrestaShop\PrestaShop\Core\Domain\Employee\CommandHandler\SendResetPasswordEmailHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Employee\Exception\EmployeeNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\Employee\Exception\FailedToSendEmailException;
use PrestaShop\PrestaShop\Core\Domain\Employee\Exception\PasswordResetTooFrequentException;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * Handles the command which sends reset password email to the employee.
 *
 * @internal
 */
final class SendResetPasswordEmailHandler implements SendResetPasswordEmailHandlerInterface
{
    /**
     * @var ConfigurationInterface
     */
    private $configuration;

    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * @var UrlGeneratorInterface
     */
    private $urlGenerator;

    /**
     * @param ConfigurationInterface $configuration
     * @param TranslatorInterface $translator
     * @param UrlGeneratorInterface $urlGenerator
     */
    public function __construct(
        ConfigurationInterface $configuration,
        TranslatorInterface $translator,
        UrlGeneratorInterface $urlGenerator
    ) {
        $this->configuration = $configuration;
        $this->translator = $translator;
        $this->urlGenerator = $urlGenerator;
    }

    /**
     * {@inheritdoc}
     */
    public function handle(SendResetPasswordEmailCommand $command)
    {
        $employee = new Employee();

        if (!$employee->getByEmail($command->getEmail()->getValue()) || !$employee) {
            throw new EmployeeNotFoundException();
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

        if (!$employee->hasRecentResetPasswordToken()) {
            $employee->stampResetPasswordToken();
            $employee->update();
        }

        $resetPasswordUrl = $this->urlGenerator->generate(
            '_admin_reset_password',
            [
                'employeeId' => (int) $employee->id,
                'resetToken' => $employee->reset_password_token,
            ],
            UrlGeneratorInterface::ABSOLUTE_URL
        );
        $params = array(
            '{email}' => $employee->email,
            '{lastname}' => $employee->lastname,
            '{firstname}' => $employee->firstname,
            '{url}' => $resetPasswordUrl,
        );

        $employeeLanguage = new Language((int) $employee->id_lang);

        $mailWasSent = Mail::send(
            $employee->id_lang,
            'password_query',
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
            throw new FailedToSendEmailException();
        }
    }
}
