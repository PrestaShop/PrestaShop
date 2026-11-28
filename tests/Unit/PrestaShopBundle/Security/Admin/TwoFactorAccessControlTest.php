<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\PrestaShopBundle\Security\Admin;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Scheb\TwoFactorBundle\Security\Authentication\Token\TwoFactorTokenFactory;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\ExpressionLanguage\Expression;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestMatcher\AttributesRequestMatcher;
use Symfony\Component\HttpFoundation\RequestMatcher\PathRequestMatcher;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authentication\AuthenticationTrustResolver;
use Symfony\Component\Security\Core\Authentication\Token\NullToken;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\Authorization\AccessDecisionManager;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Security\Core\Authorization\ExpressionLanguage;
use Symfony\Component\Security\Core\Authorization\Voter\AuthenticatedVoter;
use Symfony\Component\Security\Core\Authorization\Voter\ExpressionVoter;
use Symfony\Component\Security\Core\Authorization\Voter\RoleVoter;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Http\AccessMap;
use Symfony\Component\Security\Http\EventListener\DefaultLogoutListener;
use Symfony\Component\Security\Http\Firewall\AccessListener;
use Symfony\Component\Security\Http\Firewall\LogoutListener;
use Symfony\Component\Security\Http\HttpUtils;
use Symfony\Component\Yaml\Yaml;

class TwoFactorAccessControlTest extends TestCase
{
    #[DataProvider('accessStates')]
    public function testAdminCatchAllRequiresEmployeeRoleButPreservesAnonymousControllers(
        bool $authenticated,
        array $roles,
        bool $anonymousController,
        bool $expected
    ): void {
        $configuration = Yaml::parseFile(dirname(__DIR__, 5) . '/app/config/admin/security.yml');
        $rules = $configuration['security']['access_control'];
        $catchAll = end($rules);
        $attributes = [(string) $catchAll['roles'], new Expression($catchAll['allow_if'])];
        $request = Request::create('/protected');
        $request->attributes->set('_anonymous_controller', $anonymousController);
        $trustResolver = new AuthenticationTrustResolver();
        $decisionManager = new AccessDecisionManager([
            new RoleVoter(),
            new AuthenticatedVoter($trustResolver),
            new ExpressionVoter(new ExpressionLanguage(), $trustResolver, $this->createMock(AuthorizationCheckerInterface::class)),
        ]);
        // An in-progress token has a user but no roles. This also rejects any other role-less authenticated token.
        $token = $authenticated
            ? new UsernamePasswordToken($this->createMock(UserInterface::class), 'main', $roles)
            : new NullToken();

        self::assertSame($expected, $decisionManager->decide($token, $attributes, $request, true));
    }

    #[DataProvider('logoutStates')]
    public function testLogoutPassesAccessControlAndClearsTheToken(bool $inProgress): void
    {
        $configuration = Yaml::parseFile(dirname(__DIR__, 5) . '/app/config/admin/security.yml');
        $map = new AccessMap();
        foreach ($configuration['security']['access_control'] as $rule) {
            $matcher = isset($rule['route'])
                ? new AttributesRequestMatcher(['_route' => '^' . preg_quote($rule['route'], '{') . '$'])
                : new PathRequestMatcher($rule['path']);
            $attributes = [(string) $rule['roles']];
            if (isset($rule['allow_if'])) {
                $attributes[] = new Expression($rule['allow_if']);
            }
            $map->add($matcher, $attributes);
        }
        $request = Request::create('/logout');
        $request->attributes->set('_route', 'admin_logout');
        self::assertSame([AuthenticatedVoter::PUBLIC_ACCESS], $map->getPatterns($request)[0]);
        $token = new UsernamePasswordToken($this->createMock(UserInterface::class), 'main', ['ROLE_EMPLOYEE']);
        $tokens = new TokenStorage();
        $tokens->setToken($inProgress ? (new TwoFactorTokenFactory())->create($token, 'main', ['email', 'totp']) : $token);
        $trustResolver = new AuthenticationTrustResolver();
        $decisions = new AccessDecisionManager([
            new RoleVoter(),
            new AuthenticatedVoter($trustResolver),
            new ExpressionVoter(new ExpressionLanguage(), $trustResolver, $this->createMock(AuthorizationCheckerInterface::class)),
        ]);
        $event = new RequestEvent($this->createMock(HttpKernelInterface::class), $request, HttpKernelInterface::MAIN_REQUEST);
        // A lazy firewall runs access control before the external logout listener.
        (new AccessListener($tokens, $decisions, $map))($event);
        self::assertFalse($event->hasResponse());
        self::assertNotNull($tokens->getToken());

        $router = $this->createMock(RouterInterface::class);
        $router->method('generate')->with($configuration['security']['firewalls']['main']['logout']['target'])->willReturn('/login');
        $httpUtils = new HttpUtils($router);
        $dispatcher = new EventDispatcher();
        $dispatcher->addSubscriber(new DefaultLogoutListener($httpUtils, $configuration['security']['firewalls']['main']['logout']['target']));
        $logout = new LogoutListener($tokens, $httpUtils, $dispatcher, [
            'logout_path' => $configuration['security']['firewalls']['main']['logout']['path'],
        ]);
        $logout($event);

        self::assertNull($tokens->getToken());
        $response = $event->getResponse();
        self::assertNotNull($response);
        self::assertSame('/login', $response->headers->get('Location'));
    }

    public static function logoutStates(): iterable
    {
        yield 'incomplete two-factor authentication' => [true];
        yield 'fully authenticated employee' => [false];
    }

    public static function accessStates(): iterable
    {
        yield 'authenticated user without roles' => [true, [], false, false];
        yield 'fully authenticated employee' => [true, ['ROLE_EMPLOYEE'], false, true];
        yield 'anonymous controller' => [false, [], true, true];
        yield 'protected anonymous request' => [false, [], false, false];
    }
}
