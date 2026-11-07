<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Integration\Classes\Controller;

use CategoryController;
use Configuration;
use PHPUnit\Framework\TestCase;
use ReflectionClass;
use ReflectionMethod;

class MaxProductsPerPageTest extends TestCase
{
    private const CONFIGURATION_KEYS = ['PS_PRODUCTS_PER_PAGE', 'PS_PRODUCTS_PER_PAGE_MAX'];

    /** @var array<string, string|false> */
    private array $savedConfiguration = [];

    protected function setUp(): void
    {
        foreach (self::CONFIGURATION_KEYS as $key) {
            $this->savedConfiguration[$key] = Configuration::get($key);
        }
    }

    protected function tearDown(): void
    {
        foreach ($this->savedConfiguration as $key => $value) {
            if (false === $value) {
                Configuration::deleteByName($key);
            } else {
                Configuration::updateValue($key, $value);
            }
        }
    }

    /**
     * @dataProvider provideConfiguration
     */
    public function testMaxProductsPerPage(int $productsPerPage, ?int $maxProductsPerPage, int $expectedMax): void
    {
        Configuration::updateValue('PS_PRODUCTS_PER_PAGE', $productsPerPage);
        if (null === $maxProductsPerPage) {
            Configuration::deleteByName('PS_PRODUCTS_PER_PAGE_MAX');
        } else {
            Configuration::updateValue('PS_PRODUCTS_PER_PAGE_MAX', $maxProductsPerPage);
        }

        $controller = (new ReflectionClass(CategoryController::class))->newInstanceWithoutConstructor();
        $method = new ReflectionMethod(CategoryController::class, 'getMaxProductsPerPage');

        self::assertSame($expectedMax, $method->invoke($controller));
    }

    public static function provideConfiguration(): iterable
    {
        yield 'configured maximum' => [12, 30, 30];
        yield 'missing configuration falls back to 96' => [12, null, 96];
        yield 'never below products per page' => [50, 30, 50];
        yield '0 removes the limit' => [12, 0, 0];
        yield 'negative removes the limit' => [12, -1, 0];
    }
}
