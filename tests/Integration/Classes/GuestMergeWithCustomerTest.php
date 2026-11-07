<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Integration\Classes;

use Db;
use Guest;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Tests\Resources\DatabaseDump;

class GuestMergeWithCustomerTest extends KernelTestCase
{
    private const CUSTOMER_ID = 1;

    protected function setUp(): void
    {
        parent::setUp();
        self::bootKernel();
    }

    protected function tearDown(): void
    {
        // A merge on an unloaded guest rewrites every fixture cart and connection without a guest, so
        // deleting the rows created here is not enough to undo it.
        DatabaseDump::restoreTables(['cart', 'connections', 'guest']);

        parent::tearDown();
    }

    public function testAVisitorGuestIsMergedIntoTheCustomerGuest(): void
    {
        $customerGuestId = $this->createGuest(self::CUSTOMER_ID);
        $visitorGuestId = $this->createGuest(0);
        $visitorCartId = $this->createCart($visitorGuestId);
        $guestLessCartId = $this->createCart(0);

        self::assertTrue((new Guest($visitorGuestId))->mergeWithCustomer($customerGuestId, self::CUSTOMER_ID));

        self::assertSame($customerGuestId, $this->getCartGuestId($visitorCartId));
        self::assertSame(0, $this->getCartGuestId($guestLessCartId));
    }

    public function testAGuestThatIsNotLoadedLeavesTheGuestLessCartsAlone(): void
    {
        $customerGuestId = $this->createGuest(self::CUSTOMER_ID);
        $guestLessCartId = $this->createCart(0);

        $merged = (new Guest())->mergeWithCustomer($customerGuestId, self::CUSTOMER_ID);

        self::assertSame(0, $this->getCartGuestId($guestLessCartId));
        self::assertFalse($merged);
        self::assertSame(
            self::CUSTOMER_ID,
            (int) Db::getInstance()->getValue('SELECT id_customer FROM ' . _DB_PREFIX_ . 'guest WHERE id_guest = ' . $customerGuestId)
        );
    }

    private function createGuest(int $customerId): int
    {
        $guest = new Guest();
        $guest->id_customer = $customerId;
        $guest->add();

        return (int) $guest->id;
    }

    private function createCart(int $guestId): int
    {
        Db::getInstance()->insert('cart', [
            'id_shop_group' => 1,
            'id_shop' => 1,
            'id_lang' => 1,
            'id_currency' => 1,
            'id_guest' => $guestId,
            'secure_key' => 'guest-merge-test',
            'date_add' => date('Y-m-d H:i:s'),
            'date_upd' => date('Y-m-d H:i:s'),
        ]);

        return (int) Db::getInstance()->Insert_ID();
    }

    private function getCartGuestId(int $cartId): int
    {
        return (int) Db::getInstance()->getValue('SELECT id_guest FROM ' . _DB_PREFIX_ . 'cart WHERE id_cart = ' . $cartId);
    }
}
