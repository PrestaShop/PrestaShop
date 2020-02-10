<?php
/**
 * 2007-2019 PrestaShop and Contributors
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
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
 * needs please refer to https://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2019 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

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
        $this->assertTrue($this->deleteCartAccessibilityChecker->isGranted([self::STATUS_PROPERTY => '12notready']));
        $this->assertTrue($this->deleteCartAccessibilityChecker->isGranted([self::STATUS_PROPERTY => '-12zef210']));
        $this->assertTrue($this->deleteCartAccessibilityChecker->isGranted([self::STATUS_PROPERTY => '-12.02e1f']));

        $this->assertFalse($this->deleteCartAccessibilityChecker->isGranted([self::STATUS_PROPERTY => '8']));
        $this->assertFalse($this->deleteCartAccessibilityChecker->isGranted([self::STATUS_PROPERTY => -10]));
        $this->assertFalse($this->deleteCartAccessibilityChecker->isGranted([self::STATUS_PROPERTY => 2.1]));
        $this->assertFalse($this->deleteCartAccessibilityChecker->isGranted([self::STATUS_PROPERTY => 2]));
        $this->assertFalse($this->deleteCartAccessibilityChecker->isGranted([self::STATUS_PROPERTY => 6463165813]));
    }
}
