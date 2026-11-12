<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\Classes;

use Cookie;
use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\Http\CookieOptions;
use ReflectionClass;
use ReflectionMethod;
use ReflectionProperty;

/**
 * A browser discards a SameSite=None cookie that is not also Secure. For the employee cookie that means
 * the session is lost and the next request fails its token check with "security compromised", so None
 * must never be emitted on a cookie that is not secure - including as a fallback for an unrecognised
 * configured value, which is what an upgraded shop can end up with.
 */
class CookieSameSiteTest extends TestCase
{
    /**
     * @dataProvider provideUnrecognisedValues
     *
     * @param mixed $configured
     */
    public function testAnUnrecognisedValueFallsBackToLaxRatherThanNone($configured): void
    {
        $this->assertSame(
            CookieOptions::SAMESITE_LAX,
            $this->resolve($configured, false),
            'an unusable configured value must not become the one attribute browsers refuse'
        );
    }

    public static function provideUnrecognisedValues(): iterable
    {
        yield 'missing configuration' => [false];
        yield 'empty string' => [''];
        yield 'null' => [null];
        yield 'wrong case' => ['lax'];
        yield 'nonsense' => ['whatever'];
    }

    public function testNoneIsDowngradedOnAnInsecureCookie(): void
    {
        $this->assertSame(CookieOptions::SAMESITE_LAX, $this->resolve(CookieOptions::SAMESITE_NONE, false));
    }

    public function testNoneIsKeptOnASecureCookie(): void
    {
        $this->assertSame(CookieOptions::SAMESITE_NONE, $this->resolve(CookieOptions::SAMESITE_NONE, true));
    }

    /**
     * @dataProvider provideRecognisedValues
     */
    public function testRecognisedValuesAreKept(string $configured): void
    {
        $this->assertSame($configured, $this->resolve($configured, false));
    }

    public static function provideRecognisedValues(): iterable
    {
        yield 'lax' => [CookieOptions::SAMESITE_LAX];
        yield 'strict' => [CookieOptions::SAMESITE_STRICT];
    }

    /**
     * @param mixed $configured
     */
    private function resolve($configured, bool $secure): string
    {
        $cookie = (new ReflectionClass(Cookie::class))->newInstanceWithoutConstructor();

        $sameSite = new ReflectionProperty(Cookie::class, '_sameSite');
        $sameSite->setAccessible(true);
        $sameSite->setValue($cookie, $configured);

        $secureProperty = new ReflectionProperty(Cookie::class, '_secure');
        $secureProperty->setAccessible(true);
        $secureProperty->setValue($cookie, $secure);

        $method = new ReflectionMethod(Cookie::class, 'getSameSiteAttribute');
        $method->setAccessible(true);

        return $method->invoke($cookie);
    }
}
