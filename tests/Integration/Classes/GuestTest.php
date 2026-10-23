<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Integration\Classes;

use Cookie;
use Customer;
use Guest;
use PHPUnit\Framework\TestCase;

class GuestTest extends TestCase
{
    /**
     * @var Guest[]
     */
    private array $guests = [];

    private ?Customer $customer = null;

    protected function tearDown(): void
    {
        foreach ($this->guests as $guest) {
            $guest->delete();
        }
        $this->customer?->delete();

        parent::tearDown();
    }

    public function testSetNewGuestBindsTheNewGuestToTheCustomer(): void
    {
        $this->customer = new Customer();
        $this->customer->firstname = 'Guest';
        $this->customer->lastname = 'Test';
        $this->customer->email = 'guest-test-' . uniqid() . '@prestashop.com';
        $this->customer->passwd = password_hash('guest-test', PASSWORD_BCRYPT);
        $this->customer->add();
        $this->assertFalse(Guest::getFromCustomer((int) $this->customer->id));

        $cookie = new Cookie('guestTest');
        $cookie->id_customer = (int) $this->customer->id;
        Guest::setNewGuest($cookie);

        $guest = $this->loadGuest((int) $cookie->id_guest);
        $this->assertSame((int) $this->customer->id, (int) $guest->id_customer);
        $this->assertSame((int) $guest->id, (int) Guest::getFromCustomer((int) $this->customer->id));
    }

    public function testSetNewGuestWithoutCustomerDoesNotReuseAnAnonymousGuest(): void
    {
        $anonymousGuest = new Guest();
        $anonymousGuest->id_customer = 0;
        $anonymousGuest->add();
        $this->guests[] = $anonymousGuest;

        $cookie = new Cookie('guestTest');
        $cookie->id_customer = 0;
        Guest::setNewGuest($cookie);

        $guest = $this->loadGuest((int) $cookie->id_guest);
        // A freshly created guest, not any of the existing anonymous ones
        $this->assertGreaterThan((int) $anonymousGuest->id, (int) $guest->id);
        $this->assertSame(0, (int) $guest->id_customer);
    }

    private function loadGuest(int $guestId): Guest
    {
        $guest = new Guest($guestId);
        $this->assertSame($guestId, (int) $guest->id);
        $this->guests[] = $guest;

        return $guest;
    }
}
