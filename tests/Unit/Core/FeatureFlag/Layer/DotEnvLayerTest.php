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
