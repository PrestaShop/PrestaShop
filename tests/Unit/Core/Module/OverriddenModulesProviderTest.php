<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\Core\Module;

use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\Module\OverriddenModulesProvider;

class OverriddenModulesProviderTest extends TestCase
{
    private string $overrideDir;

    protected function setUp(): void
    {
        $this->overrideDir = dirname(__DIR__, 3) . '/Resources/module_overrides_tests/';
    }

    /**
     * @dataProvider provideModuleNames
     */
    public function testIsOverridden(string $moduleName, bool $expectedResult): void
    {
        $provider = new OverriddenModulesProvider($this->overrideDir);

        $this->assertSame($expectedResult, $provider->isOverridden($moduleName));
    }

    public static function provideModuleNames(): iterable
    {
        yield 'overridden main module class' => ['overriddenmoduleclass', true];
        yield 'overridden front controllers' => ['overriddenfrontcontroller', true];
        yield 'override folder without any php file' => ['notoverridden', false];
        yield 'module without any override folder' => ['unknownmodule', false];
    }

    public function testGetOverriddenFilesReturnsSortedRelativePaths(): void
    {
        $provider = new OverriddenModulesProvider($this->overrideDir);

        $this->assertSame(
            ['overriddenmoduleclass.php'],
            $provider->getOverriddenFiles('overriddenmoduleclass')
        );
        $this->assertSame(
            ['controllers/front/ajax.php', 'controllers/front/display.php'],
            $provider->getOverriddenFiles('overriddenfrontcontroller')
        );
        $this->assertSame([], $provider->getOverriddenFiles('unknownmodule'));
    }

    public function testGetOverriddenFilePathsReturnsPathsRelativeToTheShopRoot(): void
    {
        $provider = new OverriddenModulesProvider($this->overrideDir);

        $this->assertSame(
            ['override/modules/overriddenmoduleclass/overriddenmoduleclass.php'],
            $provider->getOverriddenFilePaths('overriddenmoduleclass')
        );
        $this->assertSame(
            [
                'override/modules/overriddenfrontcontroller/controllers/front/ajax.php',
                'override/modules/overriddenfrontcontroller/controllers/front/display.php',
            ],
            $provider->getOverriddenFilePaths('overriddenfrontcontroller')
        );
        $this->assertSame([], $provider->getOverriddenFilePaths('unknownmodule'));
    }

    /**
     * The blank index.php guard files PrestaShop generates in every folder are not overrides,
     * and neither are non-PHP leftovers.
     */
    public function testGetAllOverriddenFilesIgnoresGuardFilesAndNonPhpFiles(): void
    {
        $provider = new OverriddenModulesProvider($this->overrideDir);

        $this->assertSame(
            [
                'overriddenfrontcontroller' => ['controllers/front/ajax.php', 'controllers/front/display.php'],
                'overriddenmoduleclass' => ['overriddenmoduleclass.php'],
            ],
            $provider->getAllOverriddenFiles()
        );
    }

    /**
     * @dataProvider provideEmptyOverrideDirs
     */
    public function testItReturnsNothingWhenTheModulesOverrideFolderIsMissing(string $overrideDir): void
    {
        $provider = new OverriddenModulesProvider($overrideDir);

        $this->assertSame([], $provider->getAllOverriddenFiles());
        $this->assertFalse($provider->isOverridden('overriddenmoduleclass'));
    }

    public static function provideEmptyOverrideDirs(): iterable
    {
        // A brand new shop has an override folder holding no `modules` subfolder at all
        yield 'override folder without modules subfolder' => [
            dirname(__DIR__, 3) . '/Resources/module_overrides_tests/modules/overriddenmoduleclass/',
        ];
        yield 'missing override folder' => [
            dirname(__DIR__, 3) . '/Resources/module_overrides_tests/this-folder-does-not-exist/',
        ];
    }
}
