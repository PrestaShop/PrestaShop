<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
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
use PrestaShop\PrestaShop\Core\Util\Url\UrlCleaner;
use PrestaShopBundle\Entity\Employee\Employee;
use PrestaShopBundle\Entity\Repository\EmployeeRepository;
use PrestaShopBundle\Security\Admin\Exception\PasswordResetTemporarilyBlockedException;
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
     * @throws PasswordResetTemporarilyBlockedException
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

        if (empty($employee)) {
            throw new UserNotFoundException(sprintf('Employee with email "%s" does not exist.', $email));
        }

        $this->checkLastPasswordGeneration($employee);
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

    private function checkLastPasswordGeneration(Employee $employee): void
    {
        // If the employee reset his password recently he needs to wait a minimum of time
        $passwordResetInterval = (int) ($this->configuration->get('PS_PASSWD_TIME_BACK') ?: 360);
        if ($employee->getPasswordLastGeneration()->add(DateInterval::createFromDateString($passwordResetInterval . 'minutes'))->getTimestamp() > time()) {
            throw new PasswordResetTemporarilyBlockedException();
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

    private function updateEmployeeResetData(Employee $employee): void
    {
        $validityDuration = (int) ($this->configuration->get('PS_PASSWD_RESET_VALIDITY') ?: 1440);
        $salt = $employee->getId() . '+' . uniqid((string) mt_rand(0, mt_getrandmax()), true);
        $employee
            ->setResetPasswordToken(sha1(time() . $salt))
            ->setResetPasswordValidity((new DateTime())->add(DateInterval::createFromDateString($validityDuration . 'min')))
        ;
        $this->entityManager->flush();
    }
}
