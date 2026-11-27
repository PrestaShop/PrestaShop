<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShopBundle\EventListener\Admin;

use LogicException;
use PrestaShop\PrestaShop\Adapter\Employee\TwoFactor\EmployeeTwoFactorRateLimiter;
use PrestaShop\PrestaShop\Core\Domain\Employee\Exception\EmployeeTwoFactorException;
use PrestaShopBundle\Entity\Employee\Employee;
use Scheb\TwoFactorBundle\Security\Http\Authenticator\Passport\Credentials\TwoFactorCodeCredentials;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Event\FinishRequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Lock\LockInterface;
use Symfony\Component\Security\Core\Exception\AccountStatusException;
use Symfony\Component\Security\Core\Exception\BadCredentialsException;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Core\Exception\UserNotFoundException;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\Event\CheckPassportEvent;
use Symfony\Component\Security\Http\Event\LoginFailureEvent;
use Symfony\Component\Security\Http\Event\LoginSuccessEvent;

final class TwoFactorCheckRateLimitListener implements EventSubscriberInterface
{
    private const LOCK_ATTRIBUTE = '_employee_2fa_verification_lock';
    private const EMPLOYEE_ATTRIBUTE = '_employee_2fa_verification_employee';
    private const PASSPORT_ATTRIBUTE = '_employee_2fa_verification_passport';

    public function __construct(
        private readonly EmployeeTwoFactorRateLimiter $rateLimiter,
        private readonly RequestStack $requestStack,
    ) {
    }

    public function checkPassport(CheckPassportEvent $event): void
    {
        $passport = $event->getPassport();
        if (!$passport->hasBadge(TwoFactorCodeCredentials::class) || !$passport->getUser() instanceof Employee) {
            return;
        }
        $request = $this->requestStack->getCurrentRequest();
        if (null === $request) {
            throw new LogicException('Two-factor verification requires an HTTP request.');
        }

        try {
            $employeeId = $passport->getUser()->getId();
            $lock = $this->rateLimiter->startVerification($employeeId);
        } catch (EmployeeTwoFactorException $e) {
            throw new CustomUserMessageAuthenticationException('Too many verification attempts. Please try again later.', [], $e->getCode(), $e);
        }
        // Keep the lock on the request/passport, never on the serialized security token.
        $passport->setAttribute(self::LOCK_ATTRIBUTE, $lock);
        $passport->setAttribute(self::EMPLOYEE_ATTRIBUTE, $employeeId);
        $request->attributes->set(self::PASSPORT_ATTRIBUTE, $passport);
    }

    public function onVerificationSuccess(CheckPassportEvent $event): void
    {
        $passport = $event->getPassport();
        $credentials = $passport->getBadge(TwoFactorCodeCredentials::class);
        $lock = $passport->getAttribute(self::LOCK_ATTRIBUTE);
        if (!$lock instanceof LockInterface || !$credentials instanceof TwoFactorCodeCredentials || !$credentials->isResolved()) {
            return;
        }

        try {
            $this->rateLimiter->assertVerificationLock($lock);
        } finally {
            $this->release($passport, $this->requestStack->getCurrentRequest());
        }
    }

    public function onLoginFailure(LoginFailureEvent $event): void
    {
        $passport = $event->getPassport();
        if (null === $passport || !$passport->getAttribute(self::LOCK_ATTRIBUTE) instanceof LockInterface) {
            return;
        }
        try {
            $credentials = $passport->getBadge(TwoFactorCodeCredentials::class);
            $exception = $event->getException();
            if (
                $credentials instanceof TwoFactorCodeCredentials
                && !$credentials->isResolved()
                && $exception instanceof BadCredentialsException
                && !$exception->getPrevious() instanceof AccountStatusException
                && !$exception->getPrevious() instanceof UserNotFoundException
            ) {
                $this->rateLimiter->recordFailure(
                    $passport->getAttribute(self::EMPLOYEE_ATTRIBUTE),
                    $passport->getAttribute(self::LOCK_ATTRIBUTE)
                );
            }
        } finally {
            $this->release($passport, $event->getRequest());
        }
    }

    public function onLoginSuccess(LoginSuccessEvent $event): void
    {
        // Also clean up if another listener stopped CheckPassportEvent propagation.
        $this->release($event->getPassport(), $event->getRequest());
    }

    public function onKernelException(ExceptionEvent $event): void
    {
        $this->releaseRequest($event->getRequest());
    }

    public function onFinishRequest(FinishRequestEvent $event): void
    {
        $this->releaseRequest($event->getRequest());
    }

    private function releaseRequest(Request $request): void
    {
        $passport = $request->attributes->get(self::PASSPORT_ATTRIBUTE);
        if ($passport instanceof Passport) {
            $this->release($passport, $request);
        }
    }

    private function release(Passport $passport, ?Request $request): void
    {
        $lock = $passport->getAttribute(self::LOCK_ATTRIBUTE);
        $passport->setAttribute(self::LOCK_ATTRIBUTE, null);
        $passport->setAttribute(self::EMPLOYEE_ATTRIBUTE, null);
        if (null !== $request && $request->attributes->get(self::PASSPORT_ATTRIBUTE) === $passport) {
            $request->attributes->remove(self::PASSPORT_ATTRIBUTE);
        }
        if ($lock instanceof LockInterface) {
            $lock->release();
        }
    }

    public static function getSubscribedEvents(): array
    {
        return [
            CheckPassportEvent::class => [['checkPassport', 256], ['onVerificationSuccess', -1]],
            LoginFailureEvent::class => 'onLoginFailure',
            LoginSuccessEvent::class => 'onLoginSuccess',
            KernelEvents::EXCEPTION => ['onKernelException', PHP_INT_MAX],
            KernelEvents::FINISH_REQUEST => ['onFinishRequest', PHP_INT_MAX],
        ];
    }
}
