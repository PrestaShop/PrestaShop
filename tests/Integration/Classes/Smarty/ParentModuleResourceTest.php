<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Integration\Classes\Smarty;

use FilesystemIterator;
use PHPUnit\Framework\TestCase;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use Smarty;
use SmartyResourceModule;

/**
 * A theme template that overrides a module template has no way to extend the file it overrides:
 * the `module` resource searches the theme first, so `{extends file='module:...'}` finds the
 * override itself. The `parent_module` resource resolves the same chain without the current theme.
 */
class ParentModuleResourceTest extends TestCase
{
    private const TEMPLATE = 'mymodule/views/templates/front/view.tpl';

    private string $root;
    private string $themeModulesDir;
    private string $moduleDir;
    private Smarty $smarty;

    protected function setUp(): void
    {
        parent::setUp();

        $this->root = sys_get_temp_dir() . '/ps-parent-module-' . uniqid('', false);
        $this->themeModulesDir = $this->root . '/theme/modules/';
        $this->moduleDir = $this->root . '/modules/';

        // The module ships the original, with a block a theme may want to keep.
        $this->writeTemplate(
            $this->moduleDir,
            '[module-original]{block name="body"}module body{/block}[/module-original]'
        );

        // The theme overrides it, but wants to keep the original around it.
        $this->writeTemplate(
            $this->themeModulesDir,
            '{extends file="parent_module:' . self::TEMPLATE . '"}{block name="body"}theme body{/block}'
        );

        $this->smarty = new Smarty();
        $this->smarty->setCompileDir($this->root . '/compile');
        $this->smarty->setCacheDir($this->root . '/cache');
        mkdir($this->root . '/compile', 0777, true);
        mkdir($this->root . '/cache', 0777, true);

        // Exactly the wiring of config/smartyfront.config.inc.php.
        $moduleResources = [
            'theme' => $this->themeModulesDir,
            'modules' => $this->moduleDir,
        ];
        $parentModuleResources = $moduleResources;
        unset($parentModuleResources['theme']);

        $this->smarty->registerResource('module', new SmartyResourceModule($moduleResources));
        $this->smarty->registerResource('parent_module', new SmartyResourceModule($parentModuleResources));
    }

    protected function tearDown(): void
    {
        $this->removeDirectory($this->root);

        parent::tearDown();
    }

    /**
     * The cause: `module` resolves to the theme's own override.
     */
    public function testTheModuleResourceResolvesToTheThemeOverride(): void
    {
        $resolved = $this->smarty->fetch('module:' . self::TEMPLATE);

        $this->assertStringContainsString('theme body', $resolved);
    }

    /**
     * The fix: `parent_module` skips the theme and reaches the module's own file.
     */
    public function testTheParentModuleResourceReachesTheModulesOwnTemplate(): void
    {
        $resolved = $this->smarty->fetch('parent_module:' . self::TEMPLATE);

        $this->assertStringContainsString('[module-original]', $resolved, 'parent_module did not reach the module template.');
        $this->assertStringContainsString('module body', $resolved, 'parent_module resolved to the theme override instead of the module template.');
    }

    /**
     * The point of the feature: the override keeps the module's markup and replaces only its block.
     */
    public function testAThemeOverrideCanExtendTheTemplateItOverrides(): void
    {
        $rendered = $this->smarty->fetch('module:' . self::TEMPLATE);

        $this->assertStringContainsString('[module-original]', $rendered, 'The module markup was lost, so the override replaced instead of extended.');
        $this->assertStringContainsString('theme body', $rendered, 'The theme block was not applied.');
        $this->assertStringNotContainsString('module body', $rendered, 'The module block should have been replaced by the theme one.');
    }

    /**
     * The tests above wire the resources themselves, so they prove the mechanism but would pass even
     * with nothing registering it. The front Smarty configuration cannot be inspected from here -
     * the integration bootstrap defines `_PS_ADMIN_DIR_`, so `config/smarty.config.inc.php` loads the
     * admin configuration and never the front one - so this reads the file that does the wiring.
     */
    public function testTheFrontConfigurationRegistersTheResource(): void
    {
        $source = file_get_contents(_PS_ROOT_DIR_ . '/config/smartyfront.config.inc.php');

        // Vacuity guard: if the file could not be read, or no longer registers the resource this one
        // is derived from, the assertion below would be meaningless rather than false.
        $this->assertIsString($source);
        $this->assertStringContainsString("registerResource('module'", $source, 'Failed to read the front Smarty configuration.');

        $this->assertStringContainsString(
            "registerResource('parent_module'",
            $source,
            'config/smartyfront.config.inc.php no longer registers the parent_module resource.'
        );
        $this->assertStringContainsString(
            'unset($parent_module_resources[\'theme\'])',
            $source,
            'parent_module is registered but no longer drops the current theme, so it would recurse like module.'
        );
    }

    private function writeTemplate(string $baseDir, string $contents): void
    {
        $path = $baseDir . self::TEMPLATE;
        mkdir(dirname($path), 0777, true);
        file_put_contents($path, $contents);
    }

    private function removeDirectory(string $dir): void
    {
        if (!is_dir($dir)) {
            return;
        }

        $items = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($dir, FilesystemIterator::SKIP_DOTS),
            RecursiveIteratorIterator::CHILD_FIRST
        );

        foreach ($items as $item) {
            $item->isDir() ? rmdir($item->getPathname()) : unlink($item->getPathname());
        }

        rmdir($dir);
    }
}
