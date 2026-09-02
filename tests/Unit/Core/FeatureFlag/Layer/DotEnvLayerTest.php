<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace Tests\Unit\Core\FeatureFlag\Layer;

use Generator;
use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Adapter\Environment;
use PrestaShop\PrestaShop\Core\FeatureFlag\Layer\DotEnvLayer;

class DotEnvLayerTest extends TestCase
{
    private const FEATURE_FLAG_TEST = 'feature_flag_test';
    private const VAR_FEATURE_FLAG_TEST = 'PS_FF_FEATURE_FLAG_TEST';
    private const DOTENV_PATH = _PS_ROOT_DIR_ . '/tests/Resources/env/.env.unit.local';
    public static $save_dotenv_vars = null;

    public static function setUpBeforeClass(): void
    {
        static::$save_dotenv_vars = $_ENV['SYMFONY_DOTENV_VARS'] ?? '';
    }

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
        $layer = $this->createLayer();
        $this->assertFalse($layer->isReadonly());
    }

    public function testGetTypeName()
    {
        $layer = $this->createLayer();
        $this->assertEquals('dotenv', $layer->getTypeName());
    }

    public function testGetConstName()
    {
        $layer = $this->createLayer();
        $this->assertEquals(
            self::VAR_FEATURE_FLAG_TEST,
            $layer->getVarName(self::FEATURE_FLAG_TEST)
        );
    }

    public function testCanBeUsed()
    {
        $this->setEnv(true);
        $layer = $this->createLayer();
        $this->assertTrue($layer->canBeUsed(self::FEATURE_FLAG_TEST));
    }

    public function testCannotBeUsed()
    {
        $layer = $this->createLayer();
        $this->assertFalse($layer->canBeUsed(self::FEATURE_FLAG_TEST));
    }

    /**
     * @dataProvider provideEnabledValues
     */
    public function testIsEnabled(string $enabledValue)
    {
        $this->setEnv($enabledValue);
        $layer = $this->createLayer();
        $this->assertTrue($layer->isEnabled(self::FEATURE_FLAG_TEST));
    }

    /**
     * @dataProvider provideDisabledValues
     */
    public function testIsDisabled(string $disabledValue)
    {
        $this->setEnv($disabledValue);
        $layer = $this->createLayer();
        $this->assertFalse($layer->isEnabled(self::FEATURE_FLAG_TEST));
    }

    /**
     * @dataProvider provideEnabledValues
     */
    public function testEnable(string $enabledValue)
    {
        file_put_contents(self::DOTENV_PATH, self::VAR_FEATURE_FLAG_TEST . "={$enabledValue}");
        $layer = $this->createLayer();
        $layer->enable(self::FEATURE_FLAG_TEST);
        $this->assertEquals(
            self::VAR_FEATURE_FLAG_TEST . '=true',
            file_get_contents(self::DOTENV_PATH)
        );
    }

    /**
     * @dataProvider provideDisabledValues
     */
    public function testDisable(string $disabledValue)
    {
        $this->setEnv(true);
        file_put_contents(self::DOTENV_PATH, self::VAR_FEATURE_FLAG_TEST . "={$disabledValue}");
        $layer = $this->createLayer();
        $layer->disable(self::FEATURE_FLAG_TEST);
        $this->assertEquals(
            self::VAR_FEATURE_FLAG_TEST . '=false',
            file_get_contents(self::DOTENV_PATH)
        );
    }

    private function resetEnv(): void
    {
        unset($_ENV[self::VAR_FEATURE_FLAG_TEST]);
        $_ENV['SYMFONY_DOTENV_VARS'] = static::$save_dotenv_vars;
        @unlink(self::DOTENV_PATH);
    }

    private function setEnv($status): void
    {
        $_ENV[self::VAR_FEATURE_FLAG_TEST] = $status;
        $_ENV['SYMFONY_DOTENV_VARS'] .= ',' . self::VAR_FEATURE_FLAG_TEST;
    }

    private function createLayer(): DotEnvLayer
    {
        return new DotEnvLayer(
            new Environment(false, 'unit'),
            dirname(self::DOTENV_PATH)
        );
    }
}
