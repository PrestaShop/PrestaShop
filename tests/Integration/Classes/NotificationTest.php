<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Integration\Classes;

use Context;
use Customer;
use Db;
use Employee;
use Notification;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Tests\Resources\DatabaseDump;
use Tools;

class NotificationTest extends KernelTestCase
{
    /**
     * The notification panel only ever displays five elements, so the number of
     * customers created here has to be greater than that for the total to be
     * distinguishable from the size of the returned list.
     */
    private const NEW_CUSTOMERS = 8;

    private const DISPLAYED_ELEMENTS = 5;

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        DatabaseDump::restoreTables(['customer']);
    }

    public static function tearDownAfterClass(): void
    {
        DatabaseDump::restoreTables(['customer']);
    }

    protected function setUp(): void
    {
        parent::setUp();

        $context = Context::getContext();
        // Order notifications format prices through the locale service, which needs the container.
        $context->container = self::bootKernel()->getContainer();
        // Notification results carry back-office links, which need an employee in the context.
        if (!$context->employee || !$context->employee->id) {
            $context->employee = new Employee(1);
        }
    }

    /**
     * The badge shows how many elements are new, not how many of them fit in the
     * dropdown, so the total has to keep counting past the LIMIT of the list query.
     */
    public function testTotalCountsEveryNewElementBeyondTheDisplayedOnes(): void
    {
        $lastSeenCustomerId = $this->getLastCustomerId();

        for ($i = 0; $i < self::NEW_CUSTOMERS; ++$i) {
            $this->createDummyCustomer();
        }

        $notifications = Notification::getLastElementsIdsByType('customer', $lastSeenCustomerId);

        $this->assertSame(self::NEW_CUSTOMERS, (int) $notifications['total']);
        $this->assertCount(self::DISPLAYED_ELEMENTS, $notifications['results']);
    }

    public function testTotalIsZeroWhenNothingIsNew(): void
    {
        $notifications = Notification::getLastElementsIdsByType('customer', $this->getLastCustomerId());

        $this->assertSame(0, (int) $notifications['total']);
        $this->assertSame([], $notifications['results']);
    }

    /**
     * Every type builds its own list and count statements; this makes sure all of
     * them are valid SQL and agree with each other.
     *
     * @dataProvider provideNotificationTypes
     */
    public function testEveryTypeReturnsATotalConsistentWithItsResults(string $type): void
    {
        $notifications = Notification::getLastElementsIdsByType($type, 0);

        $this->assertIsNumeric($notifications['total']);
        $this->assertLessThanOrEqual(self::DISPLAYED_ELEMENTS, count($notifications['results']));
        $this->assertLessThanOrEqual((int) $notifications['total'], count($notifications['results']));
    }

    public static function provideNotificationTypes(): array
    {
        return [
            'order' => ['order'],
            'customer message' => ['customer_message'],
            'customer' => ['customer'],
        ];
    }

    private function getLastCustomerId(): int
    {
        return (int) Db::getInstance()->getValue(
            'SELECT MAX(`id_customer`) FROM `' . _DB_PREFIX_ . 'customer`'
        );
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
