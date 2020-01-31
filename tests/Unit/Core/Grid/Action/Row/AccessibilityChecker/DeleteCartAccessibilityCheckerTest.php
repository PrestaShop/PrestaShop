<?php

declare(strict_types=1);

namespace Tests\Core\Grid\Action\Row\AccessibilityChecker;

use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\Grid\Action\Row\AccessibilityChecker\DeleteCartAccessibilityChecker;

class DeleteCartAccessibilityCheckerTest extends TestCase
{
    private const STATUS_PROPERTY = 'status';

    /** @var DeleteCartAccessibilityChecker */
    private $deleteCartAccessibilityChecker;

    protected function setUp()
    {
        $this->deleteCartAccessibilityChecker = new DeleteCartAccessibilityChecker();
    }

    public function testIsGrantedWhenRecordStatusIsNotPlaced()
    {
        $this->assertTrue($this->deleteCartAccessibilityChecker->isGranted([self::STATUS_PROPERTY => 'Not placed']));
        $this->assertTrue($this->deleteCartAccessibilityChecker->isGranted([self::STATUS_PROPERTY => 'not placed']));
        $this->assertTrue($this->deleteCartAccessibilityChecker->isGranted([self::STATUS_PROPERTY => 'not']));
        // 12notready was unexpected to be true; 0 == $record['status'] gave false
        $this->assertFalse($this->deleteCartAccessibilityChecker->isGranted([self::STATUS_PROPERTY => '12notready']));
        $this->assertFalse($this->deleteCartAccessibilityChecker->isGranted([self::STATUS_PROPERTY => '8']));
        $this->assertFalse($this->deleteCartAccessibilityChecker->isGranted([self::STATUS_PROPERTY => -10]));
        $this->assertFalse($this->deleteCartAccessibilityChecker->isGranted([self::STATUS_PROPERTY => 2.1]));
        $this->assertFalse($this->deleteCartAccessibilityChecker->isGranted([self::STATUS_PROPERTY => 2]));
        $this->assertFalse($this->deleteCartAccessibilityChecker->isGranted([self::STATUS_PROPERTY => 6463165813]));
        $this->assertFalse($this->deleteCartAccessibilityChecker->isGranted([self::STATUS_PROPERTY => '-12zef210']));
        $this->assertFalse($this->deleteCartAccessibilityChecker->isGranted([self::STATUS_PROPERTY => '-12.02e1f']));
    }
}
