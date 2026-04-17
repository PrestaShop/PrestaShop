<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Integration\PrestaShopBundle\Admin\Security;

use PrestaShopBundle\Security\Admin\SessionRenewer;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\Storage\MockArraySessionStorage;
use Symfony\Component\Security\Csrf\CsrfTokenManager;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Tests\Integration\Utility\ContextMockerTrait;

class SessionRenewerTest extends KernelTestCase
{
    use ContextMockerTrait;

    private CsrfTokenManager $sessionTokenManager;
    private SessionRenewer $sessionRenewer;
    private Session $session;

    protected function setUp(): void
    {
        static::mockContext();

        $this->session = new Session(new MockArraySessionStorage());
        $request = new Request([], [], [], [], [], [], null);

        $request->setSession($this->session);
        $requestStack = new RequestStack();
        $requestStack->push($request);
        $clearableTokenStorage = $this->getContainer()->get('security.csrf.token_storage');
        $this->sessionRenewer = new SessionRenewer($clearableTokenStorage, $requestStack);

        $this->sessionTokenManager = $this->getContainer()->get(CsrfTokenManagerInterface::class);
    }

    public function testRenew(): void
    {
        $originalSessionId = $this->session->getId();
        $token = $this->sessionTokenManager->getToken('foo');

        self::assertEquals($originalSessionId, $this->session->getId());
        self::assertTrue($this->sessionTokenManager->isTokenValid($token));

        $this->sessionRenewer->renew();

        self::assertNotEquals($originalSessionId, $this->session->getId());
        self::assertFalse($this->sessionTokenManager->isTokenValid($token));
    }
}
