<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Integration\Adapter\Order;

use Configuration;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DriverManager;
use Order;
use PaymentModuleCore;
use PrestaShop\PrestaShop\Adapter\Order\OrderCreationLock;
use PrestaShopException;
use ReflectionProperty;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Tests\Integration\Utility\PaymentModuleFake;

class OrderCreationLockTest extends KernelTestCase
{
    private const CART_ID = 4242;
    private const OTHER_CART_ID = 4243;

    /**
     * Each worker gets its own connection, as separate PHP-FPM processes would.
     */
    private OrderCreationLock $firstWorker;
    private OrderCreationLock $secondWorker;
    private Connection $secondConnection;

    protected function setUp(): void
    {
        parent::setUp();
        self::bootKernel();

        /** @var Connection $firstConnection */
        $firstConnection = self::getContainer()->get('doctrine.dbal.default_connection');
        $this->secondConnection = DriverManager::getConnection($firstConnection->getParams());
        $dbPrefix = self::getContainer()->getParameter('database_prefix');

        $this->firstWorker = new OrderCreationLock($firstConnection, $dbPrefix, 0);
        $this->secondWorker = new OrderCreationLock($this->secondConnection, $dbPrefix, 0);
    }

    protected function tearDown(): void
    {
        $this->firstWorker->release(self::CART_ID);
        $this->secondWorker->release(self::CART_ID);
        $this->secondWorker->release(self::OTHER_CART_ID);
        $this->secondConnection->close();

        parent::tearDown();
    }

    public function testConcurrentWorkerIsRefusedWhileCartIsLocked(): void
    {
        self::assertTrue($this->firstWorker->acquire(self::CART_ID));

        self::assertFalse($this->secondWorker->acquire(self::CART_ID));
    }

    public function testLockIsScopedToOneCart(): void
    {
        self::assertTrue($this->firstWorker->acquire(self::CART_ID));

        self::assertTrue($this->secondWorker->acquire(self::OTHER_CART_ID));
    }

    public function testCartIsAvailableAgainOnceReleased(): void
    {
        self::assertTrue($this->firstWorker->acquire(self::CART_ID));
        $this->firstWorker->release(self::CART_ID);

        self::assertTrue($this->secondWorker->acquire(self::CART_ID));
    }

    public function testValidateOrderIsRefusedWhileCartIsLocked(): void
    {
        self::assertTrue($this->secondWorker->acquire(self::CART_ID));

        $paymentModule = new PaymentModuleFake();
        (new ReflectionProperty(PaymentModuleCore::class, 'orderCreationLock'))->setValue($paymentModule, $this->firstWorker);

        try {
            $paymentModule->validateOrder(self::CART_ID, (int) Configuration::get('PS_OS_PAYMENT'), 0);
            self::fail('validateOrder() must not run while another request holds the cart lock');
        } catch (PrestaShopException $e) {
            self::assertStringContainsString('Another order is already being created from this cart', $e->getMessage());
        }

        self::assertFalse(Order::getIdByCartId(self::CART_ID));
        self::assertNull($paymentModule->currentOrder);
    }
}
