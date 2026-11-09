<?php
/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/OSL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to https://devdocs.prestashop.com/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

declare(strict_types=1);

namespace Tests\Integration\Adapter\Order;

use Configuration;
use Db;
use DbPDO;
use Order;
use PaymentModuleCore;
use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Adapter\Order\OrderCreationLock;
use PrestaShopException;
use ReflectionProperty;
use Tests\Integration\Utility\PaymentModuleFake;

class OrderCreationLockTest extends TestCase
{
    private const CART_ID = 4242;
    private const OTHER_CART_ID = 4243;

    /**
     * Each worker gets its own connection, as separate PHP-FPM processes would.
     *
     * @var OrderCreationLock
     */
    private $firstWorker;

    /**
     * @var OrderCreationLock
     */
    private $secondWorker;

    protected function setUp(): void
    {
        parent::setUp();

        $this->firstWorker = new OrderCreationLock(Db::getInstance(), _DB_NAME_, _DB_PREFIX_, 0);
        $this->secondWorker = new OrderCreationLock(new DbPDO(_DB_SERVER_, _DB_USER_, _DB_PASSWD_, _DB_NAME_), _DB_NAME_, _DB_PREFIX_, 0);
    }

    protected function tearDown(): void
    {
        $this->firstWorker->release(self::CART_ID);
        $this->secondWorker->release(self::CART_ID);
        $this->secondWorker->release(self::OTHER_CART_ID);

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
        $orderCreationLock = new ReflectionProperty(PaymentModuleCore::class, 'orderCreationLock');
        $orderCreationLock->setAccessible(true);
        $orderCreationLock->setValue($paymentModule, $this->firstWorker);

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
