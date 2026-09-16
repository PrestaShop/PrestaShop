<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Integration\Classes;

use Customer;
use CustomerSession;
use PHPUnit\Framework\TestCase;
use Tests\Resources\classes\TestCookie;
use Tools;

class CookieTest extends TestCase
{
    /**
     * getSession() memoizes the session it built, and until now it returned that copy
     * whatever session id was asked for. A cookie carrying an expired session id therefore
     * kept the unloaded session it had built on the first call, so the session registered
     * later during the same request (on login, for instance) was never seen and
     * isSessionAlive() stayed false for the rest of the request.
     */
    public function testSessionIsAliveAfterRegisteringANewSessionOverAStaleOne(): void
    {
        $customer = $this->createDummyCustomer();

        $cookie = new TestCookie();
        $cookie->id_customer = (int) $customer->id;
        $cookie->session_id = 123456789;
        $cookie->session_token = sha1('expired-session-token');

        // Builds and memoizes a session that cannot be loaded, as it does not exist anymore
        $this->assertFalse($cookie->isSessionAlive());

        $cookie->registerSession(new CustomerSession());

        $this->assertTrue($cookie->isSessionAlive());

        $cookie->deleteSession();
    }

    /**
     * Reading the same session twice must not hit the database again: the session is saved
     * when it is loaded, and that save triggers hooks which can read the session back.
     */
    public function testSessionOfTheSameIdIsOnlyBuiltOnce(): void
    {
        $customer = $this->createDummyCustomer();

        $cookie = new TestCookie();
        $cookie->id_customer = (int) $customer->id;
        $cookie->registerSession(new CustomerSession());

        $session = $cookie->getSession($cookie->session_id);

        $this->assertInstanceOf(CustomerSession::class, $session);
        $this->assertSame($session, $cookie->getSession($cookie->session_id));

        $cookie->deleteSession();
    }

    private function createDummyCustomer(): Customer
    {
        $customer = new Customer();
        $customer->firstname = 'Jenna';
        $customer->lastname = 'Doe';
        $customer->email = 'pub+' . uniqid() . '@prestashop.com';
        $customer->passwd = Tools::hash('prestashop');
        $customer->save();

        return $customer;
    }
}
