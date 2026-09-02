<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace Tests\Unit\Core\FeatureFlag\Layer;

use Generator;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\FeatureFlag\Layer\EnvLayer;

class EnvLayerTest extends TestCase
{
    private const FEATURE_FLAG_TEST = 'feature_flag_test';
    private const VAR_FEATURE_FLAG_TEST = 'PS_FF_FEATURE_FLAG_TEST';

    public function setUp(): void
    {
        $this->resetEnv();
    }

    public function tearDown(): void
    {
        $this->resetEnv();
    }

    public function provideEnabledValues(): Generator
    {
        yield ['1'];
        yield ['true'];
        yield ['TRUE'];
        yield ['on'];
        yield ['yes'];
    }

    public function provideDisabledValues(): Generator
    {
        yield ['0'];
        yield ['false'];
        yield ['FALSE'];
        yield ['off'];
        yield ['no'];
    }

    public function testIsReadonly()
    {
        $layer = new EnvLayer();
        $this->assertTrue($layer->isReadonly());
    }

    public function testGetTypeName()
    {
        $layer = new EnvLayer();
        $this->assertEquals('env', $layer->getTypeName());
    }

    public function testGetConstName()
    {
        $layer = new EnvLayer();
        $this->assertEquals(
            self::VAR_FEATURE_FLAG_TEST,
            $layer->getConstName(self::FEATURE_FLAG_TEST)
        );
    }

    public function testCanBeUsed()
    {
        $this->setEnv('true');
        $layer = new EnvLayer();
        $this->assertTrue($layer->canBeUsed(self::FEATURE_FLAG_TEST));
    }

    public function testCannotBeUsed()
    {
        $layer = new EnvLayer();
        $this->assertFalse($layer->canBeUsed(self::FEATURE_FLAG_TEST));
    }

    /**
     * @dataProvider provideEnabledValues
     */
    public function testIsEnabled(string $enabledValue)
    {
        $this->setEnv($enabledValue);
        $layer = new EnvLayer();
        $this->assertTrue($layer->isEnabled(self::FEATURE_FLAG_TEST));
    }

    /**
     * @dataProvider provideDisabledValues
     */
    public function testIsDisabled(string $disabledValue)
    {
        $this->setEnv($disabledValue);
        $layer = new EnvLayer();
        $this->assertFalse($layer->isEnabled(self::FEATURE_FLAG_TEST));
    }

    public function testEnable()
    {
        $layer = new EnvLayer();
        $this->expectException(InvalidArgumentException::class);
        $layer->enable(self::FEATURE_FLAG_TEST);
    }

    public function testDisable()
    {
        $layer = new EnvLayer();
        $this->expectException(InvalidArgumentException::class);
        $layer->disable(self::FEATURE_FLAG_TEST);
    }

    private function resetEnv(): void
    {
        putenv(self::VAR_FEATURE_FLAG_TEST);
    }

    private function setEnv(string $status): void
    {
        putenv(self::VAR_FEATURE_FLAG_TEST . "={$status}");
    }
}
