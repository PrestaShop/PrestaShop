<?php
/**
 * 2007-2018 PrestaShop
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
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2018 PrestaShop SA
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace Tests\Unit\Classes;

use Tests\TestCase\UnitTestCase;

use Hook;

class HookTest extends UnitTestCase
{
    public function test_isDisplayHookName__display_hooks_start_with_display()
    {
        $this->assertTrue(Hook::isDisplayHookName('displaySomething'));
    }

    public function test_isDisplayHookName__display_hooks_cannot_start_with_action()
    {
        $this->assertFalse(Hook::isDisplayHookName('actionDoWeirdStuff'));
    }

    public function test_isDisplayHookName__header_is_not_a_display_hook()
    {
        $this->assertFalse(Hook::isDisplayHookName('header'));
    }
}
