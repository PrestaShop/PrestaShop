<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Integration\Classes;

use ConnectionsSource;
use Db;
use PHPUnit\Framework\TestCase;

class ConnectionsSourceTest extends TestCase
{
    /** @var int[] */
    private $orderIds = [];

    /** @var int[] */
    private $guestIds = [];

    /** @var int[] */
    private $connectionIds = [];

    protected function tearDown(): void
    {
        $db = Db::getInstance();
        foreach ($this->connectionIds as $id) {
            $db->delete('connections_source', 'id_connections = ' . $id);
            $db->delete('connections', 'id_connections = ' . $id);
        }
        foreach ($this->guestIds as $id) {
            $db->delete('guest', 'id_guest = ' . $id);
        }
        foreach ($this->orderIds as $id) {
            $db->delete('orders', 'id_order = ' . $id);
        }
        $this->connectionIds = $this->guestIds = $this->orderIds = [];

        parent::tearDown();
    }

    public function testItReturnsNoSourcesForAnOrderWithoutACustomer(): void
    {
        // A guest row with id_customer = 0 is what every anonymous visit produces, so this one
        // stands in for the whole anonymous population the orphan order would otherwise join.
        $anonymousGuestId = $this->createGuest(0);
        $this->createSource($anonymousGuestId);
        $orphanOrderId = $this->createOrder(0);

        $this->assertSame(
            [],
            ConnectionsSource::getOrderSources($orphanOrderId),
            'an order with no customer must not match every anonymous guest'
        );
    }

    public function testItStillReturnsTheSourcesOfARealCustomer(): void
    {
        $customerId = 999123;
        $guestId = $this->createGuest($customerId);
        $this->createSource($guestId, 'https://example.com/referer');
        $orderId = $this->createOrder($customerId);

        $sources = ConnectionsSource::getOrderSources($orderId);

        $this->assertCount(1, $sources);
        $this->assertSame('https://example.com/referer', $sources[0]['http_referer']);
    }

    private function createGuest(int $customerId): int
    {
        Db::getInstance()->insert('guest', ['id_customer' => $customerId]);
        $id = (int) Db::getInstance()->Insert_ID();
        $this->guestIds[] = $id;

        return $id;
    }

    private function createSource(int $guestId, string $referer = 'https://example.com/'): void
    {
        Db::getInstance()->insert('connections', [
            'id_guest' => $guestId,
            'id_page' => 1,
            'date_add' => date('Y-m-d H:i:s'),
        ]);
        $connectionId = (int) Db::getInstance()->Insert_ID();
        $this->connectionIds[] = $connectionId;

        Db::getInstance()->insert('connections_source', [
            'id_connections' => $connectionId,
            'http_referer' => $referer,
            'request_uri' => '/index.php',
            'keywords' => '',
            'date_add' => date('Y-m-d H:i:s'),
        ]);
    }

    private function createOrder(int $customerId): int
    {
        $now = date('Y-m-d H:i:s');
        Db::getInstance()->insert('orders', [
            'id_customer' => $customerId,
            'id_cart' => 0,
            'id_currency' => 1,
            'id_lang' => 1,
            'id_carrier' => 0,
            'id_address_delivery' => 0,
            'id_address_invoice' => 0,
            'current_state' => 1,
            'payment' => 'Test',
            'date_add' => $now,
            'date_upd' => $now,
            'invoice_date' => $now,
            'delivery_date' => $now,
        ]);
        $id = (int) Db::getInstance()->Insert_ID();
        $this->orderIds[] = $id;

        return $id;
    }
}
