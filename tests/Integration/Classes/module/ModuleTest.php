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

namespace Tests\Integration\Classes\module;

use Context;
use Module;
use PHPUnit\Framework\TestCase;

class ModuleTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        // Disable modules for removing rows in module_shop
        $module = new TestModule();
        $module->disable();
    }

    public function testDevice(): void
    {
        $module = new TestModule();
        $module->enableDevice(Context::DEVICE_MOBILE);

        self::assertFalse($module->isDeviceEnabled(Context::DEVICE_COMPUTER));
        self::assertFalse($module->isDeviceEnabled(Context::DEVICE_TABLET));
        self::assertTrue($module->isDeviceEnabled(Context::DEVICE_MOBILE));

        $module->enableDevice(Context::DEVICE_TABLET);

        self::assertFalse($module->isDeviceEnabled(Context::DEVICE_COMPUTER));
        self::assertTrue($module->isDeviceEnabled(Context::DEVICE_TABLET));
        self::assertTrue($module->isDeviceEnabled(Context::DEVICE_MOBILE));

        $module->enableDevice(Context::DEVICE_COMPUTER);

        self::assertTrue($module->isDeviceEnabled(Context::DEVICE_COMPUTER));
        self::assertTrue($module->isDeviceEnabled(Context::DEVICE_TABLET));
        self::assertTrue($module->isDeviceEnabled(Context::DEVICE_MOBILE));

        $module->disableDevice(Context::DEVICE_MOBILE);

        self::assertTrue($module->isDeviceEnabled(Context::DEVICE_COMPUTER));
        self::assertTrue($module->isDeviceEnabled(Context::DEVICE_TABLET));
        self::assertFalse($module->isDeviceEnabled(Context::DEVICE_MOBILE));

        $module->disableDevice(Context::DEVICE_TABLET);

        self::assertTrue($module->isDeviceEnabled(Context::DEVICE_COMPUTER));
        self::assertFalse($module->isDeviceEnabled(Context::DEVICE_TABLET));
        self::assertFalse($module->isDeviceEnabled(Context::DEVICE_MOBILE));

        $module->disableDevice(Context::DEVICE_COMPUTER);

        self::assertFalse($module->isDeviceEnabled(Context::DEVICE_COMPUTER));
        self::assertFalse($module->isDeviceEnabled(Context::DEVICE_TABLET));
        self::assertFalse($module->isDeviceEnabled(Context::DEVICE_MOBILE));
    }

    public function testDisable(): void
    {
        $module = new TestModule();
        $module->enableDevice(Context::DEVICE_MOBILE);
        $module->enableDevice(Context::DEVICE_MOBILE);
        $module->enableDevice(Context::DEVICE_COMPUTER);
        $module->disable();

        self::assertFalse($module->isDeviceEnabled(Context::DEVICE_COMPUTER));
        self::assertFalse($module->isDeviceEnabled(Context::DEVICE_TABLET));
        self::assertFalse($module->isDeviceEnabled(Context::DEVICE_MOBILE));
    }
}

class TestModule extends Module
{
    public function __construct()
    {
        $this->name = 'test_module';
        $this->tab = 'front_office_features';
        $this->version = 1.0;
        $this->author = 'PrestaShop Maintainer';
        $this->need_instance = 0;
        parent::__construct();
        $this->displayName = 'Test module';
        $this->description = 'A module to test Module';
    }
}
