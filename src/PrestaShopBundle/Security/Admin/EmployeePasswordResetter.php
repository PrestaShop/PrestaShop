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

namespace PrestaShopBundle\Security\Admin;

use DateInterval;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\UnexpectedResultException;
use Mail;
use PrestaShop\PrestaShop\Core\ConfigurationInterface;
use PrestaShop\PrestaShop\Core\Context\ShopContext;
use PrestaShop\PrestaShop\Core\Crypto\Hashing;
use PrestaShop\PrestaShop\Core\Util\DateTime\DateTime as DateTimeUtil;
use PrestaShop\PrestaShop\Core\Util\Url\UrlCleaner;
use PrestaShopBundle\Entity\Employee\Employee;
use PrestaShopBundle\Entity\Repository\EmployeeRepository;
use PrestaShopBundle\Security\Admin\Exception\PendingPasswordResetExistingException;
use RuntimeException;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Exception\UserNotFoundException;
use Symfony\Contracts\Translation\TranslatorInterface;

class EmployeePasswordResetter
{
    public function __construct(
        private readonly EmployeeRepository $employeeRepository,
        private readonly ConfigurationInterface $configuration,
        private readonly EntityManagerInterface $entityManager,
        private readonly RouterInterface $router,
        private readonly TranslatorInterface $translator,
        private readonly ShopContext $shopContext,
        private readonly Hashing $hashing,
        private readonly string $cookieKey,
    ) {
    }

    /**
     * @param string $email
     *
     * @return string
     *
     * @throws PendingPasswordResetExistingException
     * @throws UserNotFoundException
     * @throws RuntimeException
     */
    public function sendResetEmail(string $email): string
    {
        try {
            /** @var Employee|null $employee */
            $employee = $this->employeeRepository->loadEmployeeByIdentifier($email);
        } catch (UnexpectedResultException) {
            throw new UserNotFoundException(sprintf('Employee with email "%s" does not exist.', $email));
        }

        $this->checkLastSentMail($employee, $email);
        $this->updateEmployeeResetData($employee);

        return $this->doSendResetEmail($employee);
    }

    public function getEmployeeByValidResetPasswordToken(string $resetPasswordToken): ?Employee
    {
        try {
            /** @var Employee|null $employee */
            $employee = $this->employeeRepository->findOneBy(['resetPasswordToken' => $resetPasswordToken]);
        } catch (UnexpectedResultException) {
            return null;
        }

        if (!empty($employee)
            && $employee->hasValidResetPasswordToken()
            && $employee->getResetPasswordToken() === $resetPasswordToken) {
            return $employee;
        }

        return null;
    }

    public function resetPassword(Employee $employee, string $newPassword): void
    {
        $employee
            ->setResetPasswordValidity(null)
            ->setResetPasswordToken(null)
            ->setPassword($this->hashing->hash($newPassword, $this->cookieKey))
            ->setPasswordLastGeneration(new DateTime())
        ;
        $this->entityManager->flush();

        $params = [
            '{email}' => $employee->getEmail(),
            '{lastname}' => $employee->getLastName(),
            '{firstname}' => $employee->getFirstName(),
        ];
        $employeeName = $this->translator->trans('%firstname% %lastname%', [
            '%firstname%' => $employee->getFirstName(),
            '%lastname%' => $employee->getLastName(),
        ], 'Admin.Global');

        Mail::Send(
            $employee->getDefaultLanguage()->getId(),
            'password',
            $this->translator->trans('Your new password', [], 'Emails.Subject', $employee->getDefaultLanguage()->getLocale()),
            $params,
            $employee->getEmail(),
            $employeeName,
        );
    }

    private function checkLastSentMail(?Employee $employee, string $email): void
    {
        // We don't want to give a hint to any hacker by handling the error messages differently when the employee is not known,
        // so we keep track of mail sent even to unknown emails so that we can check if they were asked too recently.
        if (empty($employee)) {
            $unknownResetEmails = $this->getUnknownResetEmails();

            // Email not known, store the fake generation date and trigger a user not found exception
            if (!isset($unknownResetEmails[$email])) {
                $this->fakeSendEmail($unknownResetEmails, $email);
            } else {
                $lastResetEmail = new DateTime($unknownResetEmails[$email]);
                $validityDuration = (int) ($this->configuration->get('PS_PASSWD_RESET_VALIDITY') ?: 1440);
                if ($lastResetEmail->add(DateInterval::createFromDateString($validityDuration . 'min'))->getTimestamp() > time()) {
                    throw new PendingPasswordResetExistingException();
                } else {
                    // The mail was sent long ago enough, we update the saved date
                    $this->fakeSendEmail($unknownResetEmails, $email);
                }
            }
        } elseif ($employee->getResetPasswordValidity() && $employee->getResetPasswordValidity()->getTimestamp() > time()) {
            throw new PendingPasswordResetExistingException();
        }
    }

    private function doSendResetEmail(Employee $employee): string
    {
        $resetUrl = $this->router->generate('admin_reset_password', ['resetToken' => $employee->getResetPasswordToken()]);
        // We remove thr CSRF token that is automatically added by the router and is useless for this url, and we need an absolute url
        $resetUrl = rtrim($this->shopContext->getBaseURL(), '/') . '/' . trim(UrlCleaner::cleanUrl($resetUrl, ['_token', 'token']), '/');

        $params = [
            '{email}' => $employee->getEmail(),
            '{lastname}' => $employee->getLastName(),
            '{firstname}' => $employee->getFirstName(),
            '{url}' => $resetUrl,
        ];
        $employeeName = $this->translator->trans('%firstname% %lastname%', [
            '%firstname%' => $employee->getFirstName(),
            '%lastname%' => $employee->getLastName(),
        ], 'Admin.Global');

        $mailSent = Mail::Send(
            $employee->getDefaultLanguage()->getId(),
            'password_query',
            $this->translator->trans('Your new password', [], 'Emails.Subject', $employee->getDefaultLanguage()->getLocale()),
            $params,
            $employee->getEmail(),
            $employeeName,
        );

        if (!$mailSent) {
            throw new RuntimeException('Unable to send reset email.');
        }

        return $resetUrl;
    }

    private function fakeSendEmail(array $unknownResetEmails, string $email): void
    {
        $unknownResetEmails[$email] = (new DateTime())->format(DateTimeUtil::DEFAULT_DATETIME_FORMAT);
        $this->updateUnknownResetEmails($unknownResetEmails);
        throw new UserNotFoundException(sprintf('Employee with email "%s" does not exist.', $email));
    }

    private function updateEmployeeResetData(Employee $employee): void
    {
        $validityDuration = (int) ($this->configuration->get('PS_PASSWD_RESET_VALIDITY') ?: 1440);
        $salt = $employee->getId() . '+' . uniqid((string) mt_rand(0, mt_getrandmax()), true);
        $employee
            ->setResetPasswordToken(sha1(time() . $salt))
            ->setResetPasswordValidity((new DateTime())->add(DateInterval::createFromDateString($validityDuration . 'min')))
            ->setPasswordLastGeneration(new DateTime())
        ;
        $this->entityManager->flush();
    }

    private function updateUnknownResetEmails(array $unknownEmails): void
    {
        if (empty($unknownEmails)) {
            $this->configuration->set('PS_UNKNOWN_RESET_EMAILS', null);
        } else {
            $this->configuration->set('PS_UNKNOWN_RESET_EMAILS', json_encode($unknownEmails));
        }
    }

    private function getUnknownResetEmails(): array
    {
        $unknownResetEmail = $this->configuration->get('PS_UNKNOWN_RESET_EMAILS');
        if (empty($unknownResetEmail)) {
            return [];
        }

        return json_decode($unknownResetEmail, true);
    }
}
