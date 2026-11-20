<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShopBundle\EventSubscriber;

use PrestaShop\PrestaShop\Core\Hook\HookResultsProviderInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Event\CheckPassportEvent;

/**
 * Lets a module refuse a back office login attempt, which is what a captcha or an IP filter needs and
 * what the front office has had for years through validateCustomerFormFields and
 * actionSubmitAccountBefore. The employee side lost its equivalent when the legacy login controller and
 * its actionAdminLoginControllerLoginBefore hook were removed in 9.0.
 *
 * The form itself is already extensible - the login form handler dispatches actionBackOfficeLoginForm
 * when it builds the form, so a module can add its own field. What was missing is the other half:
 * somewhere to check that field before the employee is authenticated.
 */
class BackOfficeLoginCheckSubscriber implements EventSubscriberInterface
{
    public const HOOK_NAME = 'actionBackOfficeLoginCheck';

    /**
     * WHY this exact number: the listeners already registered on the firewall dispatcher for this event
     * are UserProviderListener (2048 and 1024), CsrfProtectionListener (512), UserCheckerListener (256)
     * and CheckCredentialsListener (0). Running above all of them means the hook is consulted on every
     * attempt - including one whose email matches no employee, which is precisely the case a captcha
     * has to cover - and that no password is ever compared for an attempt a module refuses. Sharing a
     * priority with a core listener would leave the order to registration order, so this deliberately
     * does not reuse 2048.
     */
    private const PRIORITY = 4096;

    public function __construct(
        private readonly RequestStack $requestStack,
        private readonly HookResultsProviderInterface $hookResultsProvider,
    ) {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            CheckPassportEvent::class => ['onCheckPassport', self::PRIORITY],
        ];
    }

    public function onCheckPassport(CheckPassportEvent $event): void
    {
        $request = $this->requestStack->getCurrentRequest();
        if (null === $request) {
            return;
        }

        $results = $this->hookResultsProvider->getResults(self::HOOK_NAME, [
            /*
             * WHY no password: a module deciding whether an attempt may proceed has no business reading
             * it, and the removed actionAdminLoginControllerLoginBefore handing it to every listening
             * module was a liability, not a feature to restore.
             */
            'email' => $event->getPassport()->getBadge(UserBadge::class)?->getUserIdentifier() ?? '',
            'request' => $request,
        ]);

        foreach ($results as $result) {
            /*
             * WHY only an explicit false: a module listening in order to observe returns null, and
             * treating that as a veto would lock every employee out the moment such a module is
             * installed.
             */
            if (false === $result) {
                throw new CustomUserMessageAuthenticationException(
                    'Your login attempt was refused. Please try again.'
                );
            }
        }
    }
}
