<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShopBundle\EventListener\Admin;

use DateTimeImmutable;
use Scheb\TwoFactorBundle\Security\Authentication\Token\TwoFactorTokenInterface;
use Scheb\TwoFactorBundle\Security\TwoFactor\Event\TwoFactorAuthenticationEvent;
use Scheb\TwoFactorBundle\Security\TwoFactor\Event\TwoFactorAuthenticationEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\RateLimiter\RateLimiterFactory;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Contracts\Translation\TranslatorInterface;

final class TwoFactorCheckRateLimitListener implements EventSubscriberInterface
{
    public function __construct(
        private readonly RateLimiterFactory $twoFactorCheckLimiter,
        private readonly TranslatorInterface $translator,
    ) {
    }

    public function onTwoFactorAttempt(TwoFactorAuthenticationEvent $event): void
    {
        $token = $event->getToken();
        if (!$token instanceof TwoFactorTokenInterface) {
            return;
        }

        $rateLimit = $this->twoFactorCheckLimiter->create($this->getRateLimitKey($event->getRequest()->getClientIp(), $token->getUser()))->consume();
        if ($rateLimit->isAccepted()) {
            return;
        }

        $retryAfterInSeconds = max(1, $rateLimit->getRetryAfter()->getTimestamp() - (new DateTimeImmutable())->getTimestamp());

        throw new CustomUserMessageAuthenticationException(
            $this->translator->trans(
                'Too many verification attempts. Please wait %seconds% seconds before trying again.',
                ['%seconds%' => $retryAfterInSeconds],
                'Admin.Login.Notification'
            )
        );
    }

    public function onTwoFactorComplete(TwoFactorAuthenticationEvent $event): void
    {
        $user = $event->getToken()->getUser();
        $this->twoFactorCheckLimiter->create($this->getRateLimitKey($event->getRequest()->getClientIp(), $user))->reset();
    }

    public static function getSubscribedEvents(): array
    {
        return [
            TwoFactorAuthenticationEvents::ATTEMPT => 'onTwoFactorAttempt',
            TwoFactorAuthenticationEvents::COMPLETE => 'onTwoFactorComplete',
        ];
    }

    private function getRateLimitKey(?string $ipAddress, object $user): string
    {
        $userId = method_exists($user, 'getId') ? (string) $user->getId() : 'unknown-user';

        return $userId . ':' . ($ipAddress ?? 'unknown-ip');
    }
}
