<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\Adapter\Debug;

use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Adapter\Debug\DebugMode;

class DebugModeTest extends TestCase
{
    /**
     * @dataProvider provideValidConfigurations
     */
    public function testCreateDebugModeFromConfiguration(array $configuration, string $expected): void
    {
        $this->assertSame($expected, (new DebugMode())->createDebugModeFromConfiguration($configuration));
    }

    public static function provideValidConfigurations(): iterable
    {
        yield 'debug mode disabled' => [
            ['debug_mode' => false, 'debug_cookie_name' => 'debug', 'debug_cookie_value' => 'A1B2'],
            'false',
        ];
        yield 'debug mode enabled without cookie' => [
            ['debug_mode' => true, 'debug_cookie_name' => '', 'debug_cookie_value' => ''],
            'true',
        ];
        yield 'debug mode enabled with cookie name' => [
            ['debug_mode' => true, 'debug_cookie_name' => 'debug', 'debug_cookie_value' => ''],
            "isset(\$_COOKIE['debug'])",
        ];
        yield 'debug mode enabled with cookie name and value' => [
            ['debug_mode' => true, 'debug_cookie_name' => 'my_debug-1', 'debug_cookie_value' => 'A1B2C3D4'],
            "isset(\$_COOKIE['my_debug-1']) && \$_COOKIE['my_debug-1'] === 'A1B2C3D4'",
        ];
    }

    /**
     * @dataProvider provideInvalidConfigurations
     */
    public function testCreateDebugModeFromConfigurationRejectsUnexpectedCharacters(array $configuration): void
    {
        $this->expectException(InvalidArgumentException::class);

        (new DebugMode())->createDebugModeFromConfiguration($configuration);
    }

    public static function provideInvalidConfigurations(): iterable
    {
        yield 'quote in cookie name' => [
            ['debug_mode' => true, 'debug_cookie_name' => "de'bug", 'debug_cookie_value' => ''],
        ];
        yield 'quote in cookie value' => [
            ['debug_mode' => true, 'debug_cookie_name' => 'debug', 'debug_cookie_value' => "A1'B2"],
        ];
        yield 'space in cookie value' => [
            ['debug_mode' => true, 'debug_cookie_name' => 'debug', 'debug_cookie_value' => 'A1 B2'],
        ];
    }

    /**
     * @dataProvider provideValidDebugModeValues
     */
    public function testIsValidDebugModeValue(string $value): void
    {
        $this->assertTrue(DebugMode::isValidDebugModeValue($value));
    }

    public static function provideValidDebugModeValues(): iterable
    {
        yield ['true'];
        yield ['false'];
        yield ["isset(\$_COOKIE['debug'])"];
        yield ["isset(\$_COOKIE['debug']) && \$_COOKIE['debug'] === 'A1B2'"];
    }

    /**
     * @dataProvider provideInvalidDebugModeValues
     */
    public function testChangePsModeDevValueRejectsUnexpectedValue(string $value): void
    {
        $this->assertFalse(DebugMode::isValidDebugModeValue($value));

        $this->expectException(InvalidArgumentException::class);
        (new DebugMode())->changePsModeDevValue($value);
    }

    public static function provideInvalidDebugModeValues(): iterable
    {
        yield 'empty' => [''];
        yield 'statement after the value' => ['true); echo(1'];
        yield 'cookie names differ' => ["isset(\$_COOKIE['debug']) && \$_COOKIE['other'] === 'A1B2'"];
        yield 'quote in cookie name' => ["isset(\$_COOKIE['de'bug'])"];
    }
}
