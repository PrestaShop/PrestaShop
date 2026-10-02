<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\Classes\Module;

use Module;
use PHPUnit\Framework\TestCase;
use ReflectionMethod;

class ModuleDeclaredControllersTest extends TestCase
{
    /**
     * The historical form. A module declaring names only must keep producing exactly what it did,
     * with no meta attached, so its pages keep the URLs they have today.
     */
    public function testNamesAloneCarryNoMeta(): void
    {
        $this->assertSame(
            [
                ['name' => 'payment', 'title' => [], 'url_rewrite' => []],
                ['name' => 'validation', 'title' => [], 'url_rewrite' => []],
            ],
            $this->declaredControllers(['payment', 'validation'])
        );
    }

    public function testASingleValueAppliesToEveryLanguage(): void
    {
        $this->assertSame(
            [[
                'name' => 'validation',
                'title' => [Module::CONTROLLER_META_ALL_LANGUAGES => 'Payment validation'],
                'url_rewrite' => [Module::CONTROLLER_META_ALL_LANGUAGES => 'payment-validation'],
            ]],
            $this->declaredControllers([
                ['name' => 'validation', 'title' => 'Payment validation', 'url_rewrite' => 'payment-validation'],
            ])
        );
    }

    public function testAMapIsKeptPerLanguage(): void
    {
        $this->assertSame(
            [[
                'name' => 'confirm',
                'title' => [],
                'url_rewrite' => ['en' => 'order-confirmed', 'fr' => 'commande-confirmee'],
            ]],
            $this->declaredControllers([
                ['name' => 'confirm', 'url_rewrite' => ['en' => 'order-confirmed', 'fr' => 'commande-confirmee']],
            ])
        );
    }

    /**
     * Both forms in one declaration, which is what a module gains a controller in mid-life.
     */
    public function testBothFormsCanBeMixed(): void
    {
        $this->assertSame(
            [
                ['name' => 'payment', 'title' => [], 'url_rewrite' => []],
                ['name' => 'validation', 'title' => [], 'url_rewrite' => [Module::CONTROLLER_META_ALL_LANGUAGES => 'paid']],
            ],
            $this->declaredControllers(['payment', ['name' => 'validation', 'url_rewrite' => 'paid']])
        );
    }

    /**
     * A definition with no name cannot produce a page, and silently installing `module-x-` would be
     * worse than skipping it.
     */
    public function testADefinitionWithoutANameIsSkipped(): void
    {
        $this->assertSame(
            [['name' => 'payment', 'title' => [], 'url_rewrite' => []]],
            $this->declaredControllers([['url_rewrite' => 'orphan'], 'payment'])
        );
    }

    /**
     * @param array<int, mixed> $controllers
     *
     * @return array<int, array<string, mixed>>
     */
    private function declaredControllers(array $controllers): array
    {
        $module = new class() extends Module {
            public function __construct()
            {
                // Module::__construct() reaches for the database and the shop context, neither of
                // which this normalisation needs.
            }
        };
        $module->controllers = $controllers;

        $method = new ReflectionMethod(Module::class, 'getDeclaredControllers');
        $method->setAccessible(true);

        return $method->invoke($module);
    }
}
