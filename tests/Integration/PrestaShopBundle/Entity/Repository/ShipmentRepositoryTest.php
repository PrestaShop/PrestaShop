<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Integration\PrestaShopBundle\Entity\Repository;

use Doctrine\DBAL\Connection;
use PrestaShopBundle\Entity\Repository\ShipmentRepository;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Tests\Resources\DatabaseDump;

/**
 * findPrintableIds() is the server-side guard behind the "Download delivery slips" bulk action. The
 * ids it receives come from a POST body, which is not scoped the way the listing they were shown in
 * is, so every condition it enforces is pinned here: shop scoping, soft deletion, packing, tracking
 * and the paid state of the order.
 */
class ShipmentRepositoryTest extends KernelTestCase
{
    private const TABLES_TO_RESTORE = ['shipment', 'orders', 'order_state'];

    private const SHOP_ID = 1;

    private const OTHER_SHOP_ID = 2;

    private const A_DATE = '2026-01-01 00:00:00';

    /**
     * Shipment ids by the reason they are, or are not, printable.
     *
     * @var array<string, int>
     */
    private static array $shipments = [];

    private ShipmentRepository $repository;

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        DatabaseDump::restoreTables(self::TABLES_TO_RESTORE);

        self::bootKernel();
        $connection = self::getContainer()->get('doctrine.dbal.default_connection');
        $prefix = (string) self::getContainer()->getParameter('database_prefix');

        $paidState = self::insertOrderState($connection, $prefix, true);
        $unpaidState = self::insertOrderState($connection, $prefix, false);

        $paidOrder = self::insertOrder($connection, $prefix, $paidState, self::SHOP_ID);
        $unpaidOrder = self::insertOrder($connection, $prefix, $unpaidState, self::SHOP_ID);
        $otherShopOrder = self::insertOrder($connection, $prefix, $paidState, self::OTHER_SHOP_ID);

        // Inserted first so that it carries the lowest id: the ordering assertion below relies on it.
        self::$shipments['printable'] = self::insertShipment($connection, $prefix, $paidOrder, [
            'packed_at' => self::A_DATE,
            'tracking_number' => 'TRACK-1',
        ]);
        self::$shipments['not_packed'] = self::insertShipment($connection, $prefix, $paidOrder, [
            'packed_at' => null,
            'tracking_number' => 'TRACK-2',
        ]);
        self::$shipments['no_tracking_number'] = self::insertShipment($connection, $prefix, $paidOrder, [
            'packed_at' => self::A_DATE,
            'tracking_number' => null,
        ]);
        self::$shipments['empty_tracking_number'] = self::insertShipment($connection, $prefix, $paidOrder, [
            'packed_at' => self::A_DATE,
            'tracking_number' => '',
        ]);
        self::$shipments['deleted'] = self::insertShipment($connection, $prefix, $paidOrder, [
            'packed_at' => self::A_DATE,
            'tracking_number' => 'TRACK-3',
            'deleted' => 1,
        ]);
        self::$shipments['order_not_paid'] = self::insertShipment($connection, $prefix, $unpaidOrder, [
            'packed_at' => self::A_DATE,
            'tracking_number' => 'TRACK-4',
        ]);
        self::$shipments['other_shop'] = self::insertShipment($connection, $prefix, $otherShopOrder, [
            'packed_at' => self::A_DATE,
            'tracking_number' => 'TRACK-5',
        ]);
        self::$shipments['printable_too'] = self::insertShipment($connection, $prefix, $paidOrder, [
            'packed_at' => self::A_DATE,
            'tracking_number' => 'TRACK-6',
        ]);

        self::ensureKernelShutdown();
    }

    public static function tearDownAfterClass(): void
    {
        parent::tearDownAfterClass();
        DatabaseDump::restoreTables(self::TABLES_TO_RESTORE);
    }

    protected function setUp(): void
    {
        parent::setUp();
        self::bootKernel();

        $this->repository = self::getContainer()->get(ShipmentRepository::class);
    }

    public function testItKeepsOnlyTheShipmentsADeliverySlipMayBeProducedFor(): void
    {
        $printable = $this->repository->findPrintableIds(array_values(self::$shipments), [self::SHOP_ID]);

        self::assertSame(
            [self::$shipments['printable'], self::$shipments['printable_too']],
            $printable
        );
    }

    /**
     * @dataProvider provideRejectedShipments
     */
    public function testItDropsAShipmentThatIsNotPrintable(string $reason): void
    {
        $printable = $this->repository->findPrintableIds(
            [self::$shipments['printable'], self::$shipments[$reason]],
            [self::SHOP_ID]
        );

        self::assertSame([self::$shipments['printable']], $printable, sprintf('a %s shipment must be dropped', $reason));
    }

    /**
     * @return iterable<string, array{string}>
     */
    public static function provideRejectedShipments(): iterable
    {
        yield 'not packed yet' => ['not_packed'];
        yield 'without a tracking number' => ['no_tracking_number'];
        yield 'with an empty tracking number' => ['empty_tracking_number'];
        yield 'soft deleted' => ['deleted'];
        yield 'whose order is not paid' => ['order_not_paid'];
        yield 'belonging to another shop' => ['other_shop'];
    }

    public function testAShipmentOfAnotherShopIsReachableOnceThatShopIsInScope(): void
    {
        // The very shipment the previous test drops, to show it is the scoping that rejects it and
        // not one of the other conditions.
        $printable = $this->repository->findPrintableIds(
            [self::$shipments['other_shop']],
            [self::SHOP_ID, self::OTHER_SHOP_ID]
        );

        self::assertSame([self::$shipments['other_shop']], $printable);
    }

    public function testItNeverReachesBeyondTheSubmittedIds(): void
    {
        // 'printable_too' is printable and sits in the same shop, so only the submitted ids being
        // considered keeps it out of the result.
        $printable = $this->repository->findPrintableIds([self::$shipments['printable']], [self::SHOP_ID]);

        self::assertSame([self::$shipments['printable']], $printable);
    }

    public function testItReturnsTheIdsInAscendingOrderWhateverTheSubmittedOrder(): void
    {
        $printable = $this->repository->findPrintableIds(
            [self::$shipments['printable_too'], self::$shipments['printable']],
            [self::SHOP_ID]
        );

        self::assertSame(
            [self::$shipments['printable'], self::$shipments['printable_too']],
            $printable
        );
    }

    public function testItReturnsNothingWhenNoShipmentIsSubmitted(): void
    {
        self::assertSame([], $this->repository->findPrintableIds([], [self::SHOP_ID]));
    }

    public function testItReturnsNothingWhenNoShopIsInScope(): void
    {
        self::assertSame([], $this->repository->findPrintableIds(array_values(self::$shipments), []));
    }

    private static function insertOrderState(Connection $connection, string $prefix, bool $paid): int
    {
        $connection->insert($prefix . 'order_state', [
            'paid' => $paid ? 1 : 0,
            'unremovable' => 0,
        ]);

        return (int) $connection->lastInsertId();
    }

    private static function insertOrder(Connection $connection, string $prefix, int $stateId, int $shopId): int
    {
        $connection->insert($prefix . 'orders', [
            'reference' => 'REF' . $stateId . $shopId,
            'id_shop' => $shopId,
            'id_shop_group' => 1,
            'id_carrier' => 1,
            'id_lang' => 1,
            'id_customer' => 1,
            'id_cart' => 1,
            'id_currency' => 1,
            'id_address_delivery' => 1,
            'id_address_invoice' => 1,
            'current_state' => $stateId,
            'payment' => 'Test',
            'invoice_date' => self::A_DATE,
            'delivery_date' => self::A_DATE,
            'date_add' => self::A_DATE,
            'date_upd' => self::A_DATE,
        ]);

        return (int) $connection->lastInsertId();
    }

    /**
     * @param array{packed_at?: string|null, tracking_number?: string|null, deleted?: int} $attributes
     */
    private static function insertShipment(Connection $connection, string $prefix, int $orderId, array $attributes): int
    {
        $connection->insert($prefix . 'shipment', $attributes + [
            'id_order' => $orderId,
            'id_carrier' => 1,
            'deleted' => 0,
            'date_add' => self::A_DATE,
            'date_upd' => self::A_DATE,
        ]);

        return (int) $connection->lastInsertId();
    }
}
