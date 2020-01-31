<?php

declare(strict_types=1);

namespace Tests\Core\Grid\Action\Row\AccessibilityChecker;

use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\Grid\Action\Row\AccessibilityChecker\DeleteCartAccessibilityChecker;

class DeleteCartAccessibilityCheckerTest extends TestCase
{
    private const CART_RECORD_STATUS_NOT_PLACED = [
        'id_cart' => '15',
        'date_add' => '2020-01-31 03:20:54',
        'status' => 'Not placed',
        'id_order' => null,
        'customer_name' => 'J. DOE',
        'carrier_name' => '--',
        'id_guest' => '0',
        'online' => 'No',
        'cart_total' => '$0.00',
        'is_order_placed' => false,
        'shop_name' => 'testadas',
    ];

    private const CART_RECORD_STATUS_ORDER_ID = [
        'id_cart' => '11',
        'date_add' => '2020-01-22 10:21:43',
        'status' => '8',
        'id_order' => '8',
        'customer_name' => 'T. Davidsonas',
        'carrier_name' => '--',
        'id_guest' => '0',
        'online' => 'No',
        'cart_total' => '$11.90',
        'is_order_placed' => true,
        'shop_name' => 'testadas',
    ];

    /** @var DeleteCartAccessibilityChecker */
    private $deleteCartAccessibilityChecker;

    protected function setUp()
    {
        $this->deleteCartAccessibilityChecker = new DeleteCartAccessibilityChecker();
    }

    public function testIsGrantedWhenRecordStatusIsNotPlaced()
    {
        $this->assertTrue($this->deleteCartAccessibilityChecker->isGranted(self::CART_RECORD_STATUS_NOT_PLACED));
    }

    public function testIsGrantedWhenRecordStatusIsOrderId()
    {
        $this->assertFalse($this->deleteCartAccessibilityChecker->isGranted(self::CART_RECORD_STATUS_ORDER_ID));
    }
}
