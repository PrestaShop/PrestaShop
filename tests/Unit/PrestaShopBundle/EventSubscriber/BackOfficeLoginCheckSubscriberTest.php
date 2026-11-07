<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\PrestaShopBundle\EventSubscriber;

use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\Hook\HookResultsProviderInterface;
use PrestaShopBundle\EventSubscriber\BackOfficeLoginCheckSubscriber;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Http\Authenticator\AuthenticatorInterface;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\SelfValidatingPassport;
use Symfony\Component\Security\Http\Event\CheckPassportEvent;

class BackOfficeLoginCheckSubscriberTest extends TestCase
{
    public function testItListensOnTheEventThatPrecedesTheCredentialCheck(): void
    {
        $events = BackOfficeLoginCheckSubscriber::getSubscribedEvents();

        $this->assertArrayHasKey(CheckPassportEvent::class, $events);
        $this->assertSame('onCheckPassport', $events[CheckPassportEvent::class][0]);
    }

    public function testItOutranksEveryCoreListenerOfThatEvent(): void
    {
        // Measured on the admin firewall dispatcher: UserProviderListener 2048 and 1024,
        // CsrfProtectionListener 512, UserCheckerListener 256, CheckCredentialsListener 0. Running above
        // all of them is what makes the hook reachable for an email matching no employee, which is the
        // case a captcha has to cover, and what keeps a refused attempt from reaching the password
        // comparison.
        $priority = BackOfficeLoginCheckSubscriber::getSubscribedEvents()[CheckPassportEvent::class][1];

        $this->assertGreaterThan(2048, $priority);
    }

    public function testAModuleReturningFalseRefusesTheAttempt(): void
    {
        $subscriber = $this->createSubscriber(['securitymodule' => false]);

        $this->expectException(CustomUserMessageAuthenticationException::class);

        $subscriber->onCheckPassport($this->createEvent());
    }

    public function testOneModuleRefusingIsEnoughEvenWhenTheOthersAgree(): void
    {
        $subscriber = $this->createSubscriber([
            'observer' => null,
            'securitymodule' => false,
            'another' => true,
        ]);

        $this->expectException(CustomUserMessageAuthenticationException::class);

        $subscriber->onCheckPassport($this->createEvent());
    }

    /**
     * @dataProvider provideResultsThatDoNotRefuse
     */
    public function testOnlyAnExplicitFalseRefuses(mixed $moduleResult): void
    {
        // A module listening in order to observe returns null, and the legacy hook layer answers with an
        // empty string for a module that returns nothing. Treating any of those as a veto would lock
        // every employee out the moment such a module is installed.
        $subscriber = $this->createSubscriber(['observer' => $moduleResult]);

        $subscriber->onCheckPassport($this->createEvent());

        $this->addToAssertionCount(1);
    }

    public static function provideResultsThatDoNotRefuse(): iterable
    {
        yield 'a module that only observes' => [null];
        yield 'a module that explicitly agrees' => [true];
        yield 'a module that rendered nothing' => [''];
        yield 'a falsy value that is not false' => [0];
        yield 'the string a template would produce' => ['0'];
    }

    public function testNoModuleListeningLetsTheLoginContinue(): void
    {
        $subscriber = $this->createSubscriber([]);

        $subscriber->onCheckPassport($this->createEvent());

        $this->addToAssertionCount(1);
    }

    public function testTheHookIsNotEvenRunOutsideARequest(): void
    {
        $provider = $this->createMock(HookResultsProviderInterface::class);
        $provider->expects($this->never())->method('getResults');

        $subscriber = new BackOfficeLoginCheckSubscriber(new RequestStack(), $provider);

        $subscriber->onCheckPassport($this->createEvent());
    }

    public function testTheHookReceivesTheSubmittedEmailAndTheRequest(): void
    {
        $request = new Request();
        $stack = new RequestStack();
        $stack->push($request);

        $provider = $this->createMock(HookResultsProviderInterface::class);
        $provider->expects($this->once())
            ->method('getResults')
            ->with(
                BackOfficeLoginCheckSubscriber::HOOK_NAME,
                // The password is deliberately absent: a module deciding whether an attempt may proceed
                // has no business reading it.
                ['email' => 'employee@example.com', 'request' => $request]
            )
            ->willReturn([]);

        (new BackOfficeLoginCheckSubscriber($stack, $provider))->onCheckPassport($this->createEvent());
    }

    public function testTheHookIsDeclaredSoItAppearsInTheBackOfficeHookList(): void
    {
        $hookXml = (string) file_get_contents(__DIR__ . '/../../../../install-dev/data/xml/hook.xml');

        $this->assertStringContainsString(
            '<name>' . BackOfficeLoginCheckSubscriber::HOOK_NAME . '</name>',
            $hookXml,
            'an undeclared hook still works but never shows up in the back office hook list'
        );
    }

    /**
     * @param array<string, mixed> $hookResults
     */
    private function createSubscriber(array $hookResults): BackOfficeLoginCheckSubscriber
    {
        $stack = new RequestStack();
        $stack->push(new Request());

        $provider = $this->createMock(HookResultsProviderInterface::class);
        $provider->method('getResults')->willReturn($hookResults);

        return new BackOfficeLoginCheckSubscriber($stack, $provider);
    }

    private function createEvent(): CheckPassportEvent
    {
        return new CheckPassportEvent(
            $this->createMock(AuthenticatorInterface::class),
            new SelfValidatingPassport(new UserBadge('employee@example.com'))
        );
    }
}
