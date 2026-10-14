<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Integration\Classes\module;

use Context;
use Module;
use PHPUnit\Framework\TestCase;

/**
 * A module template may branch on Context::getDevice() or isMobile(), and the same shop is reached
 * from phones, tablets and computers. The Smarty cache id has to tell those apart, or the block
 * rendered for the first visitor is served to all of them.
 */
class ModuleCacheIdDeviceTest extends TestCase
{
    public function testTheCacheIdDiffersBetweenDevices(): void
    {
        $computer = $this->cacheIdForDevice(Context::DEVICE_COMPUTER);
        $mobile = $this->cacheIdForDevice(Context::DEVICE_MOBILE);
        $tablet = $this->cacheIdForDevice(Context::DEVICE_TABLET);

        $this->assertNotSame($computer, $mobile);
        $this->assertNotSame($computer, $tablet);
        $this->assertNotSame($mobile, $tablet);
    }

    public function testTheCacheIdIsStableForOneDevice(): void
    {
        $this->assertSame(
            $this->cacheIdForDevice(Context::DEVICE_MOBILE),
            $this->cacheIdForDevice(Context::DEVICE_MOBILE)
        );
    }

    public function testTheCacheIdStillVariesByItsName(): void
    {
        $this->assertNotSame(
            $this->cacheIdForDevice(Context::DEVICE_MOBILE, 'blockA'),
            $this->cacheIdForDevice(Context::DEVICE_MOBILE, 'blockB')
        );
    }

    private function cacheIdForDevice(int $device, ?string $name = null): string
    {
        $context = $this->createMock(Context::class);
        $context->method('getDevice')->willReturn($device);

        $module = new TestableCacheIdModule();
        $module->name = 'testmodule';
        $module->setContext($context);

        return $module->cacheIdFor($name);
    }
}

class TestableCacheIdModule extends Module
{
    public function __construct()
    {
        // The real constructor loads the module from the database, which this test has no use for.
    }

    public function setContext(Context $context): void
    {
        $this->context = $context;
    }

    public function cacheIdFor(?string $name): string
    {
        return $this->getCacheId($name);
    }
}
