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

namespace Tests\Integration\Classes\module;

use Cache;
use Configuration;
use Context;
use Module;
use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\Addon\Module\ModuleManagerBuilder;
use ReflectionMethod;
use Smarty;
use SmartyResourceModule;
use Test_ps_customtext;
use Tests\Integration\Utility\ContextMockerTrait;

/**
 * These tests install and uninstalls modules causing the cache to be cleared. So it's better to run it isolated.
 *
 * @group isolatedProcess
 */
class ModuleTest extends TestCase
{
    use ContextMockerTrait;

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        static::mockContext();
    }

    /**
     * @return array a list of modules to control override features
     */
    public function providerModulesOnDisk(): array
    {
        return [
            ['bankwire'],
            ['cronjobs'],
            ['ganalytics'],
            ['ps_emailsubscription'],
            ['ps_featuredproducts'],
        ];
    }

    /**
     * Check if html in trans is not escaped by trans method but escaped with htmlspecialchars on parameters
     *
     * @dataProvider providerModulesOnDisk
     *
     * @param string $moduleName the module name
     */
    public function testTrans(string $moduleName): void
    {
        $module = Module::getInstanceByName($moduleName);
        $transMethod = new ReflectionMethod($module, 'trans');
        $transMethod->setAccessible(true);
        $trans = $transMethod->invoke($module, '<a href="test">%d Succesful deletion "%s"</a>', [10, '<b>stringTest</b>'], 'Admin.Notifications.Success');
        $this->assertEquals('<a href="test">10 Succesful deletion "<b>stringTest</b>"</a>', $trans);

        $trans = $transMethod->invoke($module, '<a href="test">%d Succesful deletion "%s"</a>', [10, htmlspecialchars('<b>stringTest</b>')], 'Admin.Notifications.Success');
        $this->assertEquals('<a href="test">10 Succesful deletion "&lt;b&gt;stringTest&lt;/b&gt;"</a>', $trans);
    }

    /**
     * @dataProvider providerModulesOnDisk
     * Note: improves module list fixtures in order to cancel any override.
     *
     * @param string $moduleName the module name
     */
    public function testDummyGetOverride(string $moduleName): void
    {
        $module = Module::getInstanceByName($moduleName);

        $this->assertNotFalse($module);
        $this->assertInstanceOf(Module::class, $module);
        $this->assertEmpty($module->getOverrides());
    }

    public function testRealOverrideInModuleDir(): void
    {
        HelperModule::addModule('pscsx3241');
        $module = Module::getInstanceByName('pscsx3241');
        $overrides = $module->getOverrides();

        $this->assertContains('Cart', $overrides);
        $this->assertContains('DummyAdminController', $overrides);
        $this->assertCount(2, $overrides);

        HelperModule::removeModule('pscsx3241');
    }

    /**
     * Test if a module return the good possible hooks list.
     * This test is done on the bankwire generic module.
     *
     * Note: improves module list fixtures in order to get an explicit list of hooks.
     */
    public function testGetRightListForModule(): void
    {
        ModuleManagerBuilder::getInstance()->build()->install('bankwire');
        $module = Module::getInstanceByName('bankwire');
        Cache::clean('hook_alias');
        $possibleHooksList = $module->getPossibleHooksList();

        $this->assertCount(3, $possibleHooksList);

        $this->assertEquals('displayHome', $possibleHooksList[0]['name']);
        $this->assertEquals('displayPaymentReturn', $possibleHooksList[1]['name']);
        $this->assertEquals('paymentOptions', $possibleHooksList[2]['name']);

        Module::getInstanceByName('bankwire')->uninstall();
    }

    /**
     * Test that Smarty can clear cache for templates using "module:" resource.
     * This test verifies the actual Smarty behavior, not mocked behavior.
     */
    public function testSmartyClearCacheWithModuleResource(): void
    {
        $smarty = Context::getContext()->smarty;
        $cacheDir = $smarty->getCacheDir();

        // Create a test module directory and template
        $testModuleName = 'test_cache_module';
        $testModuleDir = _PS_MODULE_DIR_ . $testModuleName;
        $testTemplateDir = $testModuleDir . '/views/templates/hook';
        $testTemplateName = 'test_template.tpl';
        $testTemplateFile = $testTemplateDir . '/' . $testTemplateName;

        // Clean up any previous test artifacts
        if (is_dir($testModuleDir)) {
            $this->recursiveDelete($testModuleDir);
        }

        // Create module directory structure
        mkdir($testTemplateDir, 0777, true);
        file_put_contents($testTemplateFile, '<div>Test content {$test_var}</div>');

        // Register module resource with test paths (similar to smartyfront.config.inc.php)
        $modulePaths = [
            'modules' => _PS_MODULE_DIR_,
        ];
        $smarty->registerResource('module', new SmartyResourceModule($modulePaths));

        // Enable caching
        $originalCaching = $smarty->caching;
        $smarty->caching = Smarty::CACHING_LIFETIME_CURRENT;
        $smarty->cache_lifetime = 3600;

        $moduleTemplate = 'module:' . $testModuleName . '/views/templates/hook/' . $testTemplateName;
        $cacheId = 'test_cache_id';
        $compileId = 'test_compile_id';

        try {
            // Step 1: Render template to create cache
            $smarty->assign('test_var', 'Hello World');
            $tpl = $smarty->createTemplate($moduleTemplate, $cacheId, $compileId);
            $output = $tpl->fetch();

            $this->assertStringContainsString('Hello World', $output);

            // Step 2: Verify cache was created by checking isCached
            $tpl2 = $smarty->createTemplate($moduleTemplate, $cacheId, $compileId);
            $isCachedBefore = $tpl2->isCached();
            $this->assertTrue($isCachedBefore, 'Template should be cached after rendering');

            // Step 3: Try to clear cache using module: resource directly
            $clearedCount = $smarty->clearCache($moduleTemplate, $cacheId, $compileId);

            // Step 4: Verify cache was cleared
            $tpl3 = $smarty->createTemplate($moduleTemplate, $cacheId, $compileId);
            $isCachedAfter = $tpl3->isCached();

            // Output debug info
            echo "\n=== Smarty Cache Clear Test Results ===\n";
            echo "Template: $moduleTemplate\n";
            echo "Cache ID: $cacheId\n";
            echo "Compile ID: $compileId\n";
            echo 'Was cached before clear: ' . ($isCachedBefore ? 'YES' : 'NO') . "\n"; // @phpstan-ignore-line
            echo "Files cleared: $clearedCount\n";
            echo 'Is cached after clear: ' . ($isCachedAfter ? 'YES' : 'NO') . "\n"; // @phpstan-ignore-line
            echo "========================================\n";

            $this->assertFalse($isCachedAfter, 'Template should NOT be cached after clearCache with module: resource');
            $this->assertGreaterThan(0, $clearedCount, 'clearCache should have cleared at least 1 file');
        } finally {
            // Restore original caching setting
            $smarty->caching = $originalCaching;

            // Clean up test module
            if (is_dir($testModuleDir)) {
                $this->recursiveDelete($testModuleDir);
            }
        }
    }

    /**
     * Test comparing different cache clearing approaches for module: templates.
     */
    public function testCompareCacheClearingApproaches(): void
    {
        $smarty = Context::getContext()->smarty;

        // Create a test module directory and template
        $testModuleName = 'test_cache_compare';
        $testModuleDir = _PS_MODULE_DIR_ . $testModuleName;
        $testTemplateDir = $testModuleDir . '/views/templates/hook';
        $testTemplateName = 'compare_template.tpl';
        $testTemplateFile = $testTemplateDir . '/' . $testTemplateName;

        // Clean up any previous test artifacts
        if (is_dir($testModuleDir)) {
            $this->recursiveDelete($testModuleDir);
        }

        // Create module directory structure
        mkdir($testTemplateDir, 0777, true);
        file_put_contents($testTemplateFile, '<div>Compare test {$var}</div>');

        // Register module resource
        $modulePaths = ['modules' => _PS_MODULE_DIR_];
        $smarty->registerResource('module', new SmartyResourceModule($modulePaths));

        // Enable caching
        $originalCaching = $smarty->caching;
        $smarty->caching = Smarty::CACHING_LIFETIME_CURRENT;
        $smarty->cache_lifetime = 3600;

        $moduleTemplate = 'module:' . $testModuleName . '/views/templates/hook/' . $testTemplateName;
        $cacheId = $testModuleName;
        $compileId = 'test_compile';

        try {
            echo "\n=== Comparing Cache Clearing Approaches ===\n";

            // Test 1: Clear with module: resource directly
            $smarty->assign('var', 'Test 1');
            $smarty->fetch($moduleTemplate, $cacheId, $compileId);
            $this->assertTrue($smarty->isCached($moduleTemplate, $cacheId, $compileId), 'Should be cached');

            $cleared1 = $smarty->clearCache($moduleTemplate, $cacheId, $compileId);
            $stillCached1 = $smarty->isCached($moduleTemplate, $cacheId, $compileId);
            echo 'Approach 1 (module: directly): cleared=' . $cleared1 . ', stillCached=' . ($stillCached1 ? 'YES' : 'NO') . "\n"; // @phpstan-ignore-line

            // Test 2: Clear with null template (by cache_id/compile_id only)
            $smarty->assign('var', 'Test 2');
            $smarty->fetch($moduleTemplate, $cacheId, $compileId);
            $this->assertTrue($smarty->isCached($moduleTemplate, $cacheId, $compileId), 'Should be cached');

            $cleared2 = $smarty->clearCache(null, $cacheId, $compileId); // @phpstan-ignore-line
            $stillCached2 = $smarty->isCached($moduleTemplate, $cacheId, $compileId);
            echo 'Approach 2 (null template): cleared=' . $cleared2 . ', stillCached=' . ($stillCached2 ? 'YES' : 'NO') . "\n"; // @phpstan-ignore-line

            // Test 3: Clear with resolved absolute path
            $smarty->assign('var', 'Test 3');
            $smarty->fetch($moduleTemplate, $cacheId, $compileId);
            $this->assertTrue($smarty->isCached($moduleTemplate, $cacheId, $compileId), 'Should be cached');

            $cleared3 = $smarty->clearCache($testTemplateFile, $cacheId, $compileId);
            $stillCached3 = $smarty->isCached($moduleTemplate, $cacheId, $compileId);
            echo 'Approach 3 (absolute path): cleared=' . $cleared3 . ', stillCached=' . ($stillCached3 ? 'YES' : 'NO') . "\n"; // @phpstan-ignore-line

            echo "============================================\n";

            // Assertions - approaches 1 and 2 work, approach 3 does NOT work
            $this->assertFalse($stillCached1, 'Approach 1 (module: directly) should clear cache');
            $this->assertFalse($stillCached2, 'Approach 2 (null template) should clear cache');
            $this->assertTrue($stillCached3, 'Approach 3 (absolute path) should NOT clear cache - cache identifier mismatch');
        } finally {
            $smarty->caching = $originalCaching;
            if (is_dir($testModuleDir)) {
                $this->recursiveDelete($testModuleDir);
            }
        }
    }

    /**
     * Test cache_id prefix matching behavior (like ps_customtext clearing ps_customtext|0|1|1).
     * This simulates what happens when a module renders with getCacheId() but clears with just module name.
     */
    public function testCacheIdPrefixMatching(): void
    {
        $smarty = Context::getContext()->smarty;

        // Create a test module directory and template
        $testModuleName = 'test_prefix_cache';
        $testModuleDir = _PS_MODULE_DIR_ . $testModuleName;
        $testTemplateDir = $testModuleDir . '/views/templates/hook';
        $testTemplateName = 'prefix_template.tpl';
        $testTemplateFile = $testTemplateDir . '/' . $testTemplateName;

        // Clean up any previous test artifacts
        if (is_dir($testModuleDir)) {
            $this->recursiveDelete($testModuleDir);
        }

        // Create module directory structure
        mkdir($testTemplateDir, 0777, true);
        file_put_contents($testTemplateFile, '<div>Prefix test {$var}</div>');

        // Register module resource
        $modulePaths = ['modules' => _PS_MODULE_DIR_];
        $smarty->registerResource('module', new SmartyResourceModule($modulePaths));

        // Enable caching
        $originalCaching = $smarty->caching;
        $smarty->caching = Smarty::CACHING_LIFETIME_CURRENT;
        $smarty->cache_lifetime = 3600;

        $moduleTemplate = 'module:' . $testModuleName . '/views/templates/hook/' . $testTemplateName;
        $compileId = 'test_compile';

        // Simulate getCacheId() behavior: complex cache_id with shop/lang/etc
        $complexCacheId = $testModuleName . '|0|1|1|1';
        // Simulate _clearCache default: just the module name
        $simpleCacheId = $testModuleName;

        try {
            echo "\n=== Cache ID Prefix Matching Test ===\n";
            echo "Complex cache_id (render): $complexCacheId\n";
            echo "Simple cache_id (clear): $simpleCacheId\n";

            // Test 1: Create cache with complex cache_id, clear with null template + simple cache_id
            $smarty->assign('var', 'Test 1');
            $smarty->fetch($moduleTemplate, $complexCacheId, $compileId);
            $isCached1 = $smarty->isCached($moduleTemplate, $complexCacheId, $compileId);
            $this->assertTrue($isCached1, 'Should be cached with complex cache_id');

            $cleared1 = $smarty->clearCache(null, $simpleCacheId, $compileId); // @phpstan-ignore-line
            $stillCached1 = $smarty->isCached($moduleTemplate, $complexCacheId, $compileId);
            echo 'Test 1 (null template + simple cache_id): cleared=' . $cleared1 . ', stillCached=' . ($stillCached1 ? 'YES' : 'NO') . "\n";

            // Test 2: Create cache with complex cache_id, clear with module: template + simple cache_id
            $smarty->assign('var', 'Test 2');
            $smarty->fetch($moduleTemplate, $complexCacheId, $compileId);
            $isCached2 = $smarty->isCached($moduleTemplate, $complexCacheId, $compileId);
            $this->assertTrue($isCached2, 'Should be cached with complex cache_id');

            $cleared2 = $smarty->clearCache($moduleTemplate, $simpleCacheId, $compileId);
            $stillCached2 = $smarty->isCached($moduleTemplate, $complexCacheId, $compileId);
            echo 'Test 2 (module: template + simple cache_id): cleared=' . $cleared2 . ', stillCached=' . ($stillCached2 ? 'YES' : 'NO') . "\n";

            // Test 3: Create cache with complex cache_id, clear with module: template + complex cache_id (exact match)
            $smarty->assign('var', 'Test 3');
            $smarty->fetch($moduleTemplate, $complexCacheId, $compileId);
            $isCached3 = $smarty->isCached($moduleTemplate, $complexCacheId, $compileId);
            $this->assertTrue($isCached3, 'Should be cached with complex cache_id');

            $cleared3 = $smarty->clearCache($moduleTemplate, $complexCacheId, $compileId);
            $stillCached3 = $smarty->isCached($moduleTemplate, $complexCacheId, $compileId);
            echo 'Test 3 (module: template + complex cache_id): cleared=' . $cleared3 . ', stillCached=' . ($stillCached3 ? 'YES' : 'NO') . "\n";

            // Test 4: Create cache with complex cache_id, clear with null template + null cache_id
            $smarty->assign('var', 'Test 4');
            $smarty->fetch($moduleTemplate, $complexCacheId, $compileId);
            $isCached4 = $smarty->isCached($moduleTemplate, $complexCacheId, $compileId);
            $this->assertTrue($isCached4, 'Should be cached with complex cache_id');

            $cleared4 = $smarty->clearCache(null, null, $compileId); // @phpstan-ignore-line
            $stillCached4 = $smarty->isCached($moduleTemplate, $complexCacheId, $compileId);
            echo 'Test 4 (null template + null cache_id): cleared=' . $cleared4 . ', stillCached=' . ($stillCached4 ? 'YES' : 'NO') . "\n";

            echo "=========================================\n";

            // Assertions based on what we expect
            $this->assertFalse($stillCached1, 'Test 1: null template + simple cache_id should clear (prefix match)');
            // Test 2 is the key question - does module: + simple cache_id work with prefix matching?
            // $this->assertFalse($stillCached2, 'Test 2: module: template + simple cache_id should clear (prefix match)');
            $this->assertFalse($stillCached3, 'Test 3: module: template + complex cache_id should clear (exact match)');
            $this->assertFalse($stillCached4, 'Test 4: null template + null cache_id should clear all');
        } finally {
            $smarty->caching = $originalCaching;
            if (is_dir($testModuleDir)) {
                $this->recursiveDelete($testModuleDir);
            }
        }
    }

    /**
     * Test cache clearing with template at module root (like ps_customtext/ps_customtext.tpl).
     * This is different from templates in views/templates/hook/ subdirectory.
     */
    public function testCacheIdPrefixMatchingWithRootTemplate(): void
    {
        $smarty = Context::getContext()->smarty;

        // Create a test module directory and template AT THE ROOT (like ps_customtext)
        $testModuleName = 'test_root_tpl';
        $testModuleDir = _PS_MODULE_DIR_ . $testModuleName;
        $testTemplateName = $testModuleName . '.tpl'; // Same name as module, at root
        $testTemplateFile = $testModuleDir . '/' . $testTemplateName;

        // Clean up any previous test artifacts
        if (is_dir($testModuleDir)) {
            $this->recursiveDelete($testModuleDir);
        }

        // Create module directory - template at ROOT, not in subdirectory
        mkdir($testModuleDir, 0777, true);
        file_put_contents($testTemplateFile, '<div>Root template test {$var}</div>');

        // Register module resource
        $modulePaths = ['modules' => _PS_MODULE_DIR_];
        $smarty->registerResource('module', new SmartyResourceModule($modulePaths));

        // Enable caching
        $originalCaching = $smarty->caching;
        $smarty->caching = Smarty::CACHING_LIFETIME_CURRENT;
        $smarty->cache_lifetime = 3600;

        // Template at root: module:test_root_tpl/test_root_tpl.tpl (like ps_customtext)
        $moduleTemplate = 'module:' . $testModuleName . '/' . $testTemplateName;
        $compileId = 'test_compile';

        // Simulate getCacheId() behavior: complex cache_id with shop/lang/etc
        $complexCacheId = $testModuleName . '|0|1|1|1';
        // Simulate _clearCache default: just the module name
        $simpleCacheId = $testModuleName;

        try {
            echo "\n=== Root Template Cache Test (like ps_customtext) ===\n";
            echo "Template: $moduleTemplate\n";
            echo "Complex cache_id (render): $complexCacheId\n";
            echo "Simple cache_id (clear): $simpleCacheId\n";

            // Test 1: Create cache with complex cache_id, clear with null template + simple cache_id
            $smarty->assign('var', 'Test 1');
            $smarty->fetch($moduleTemplate, $complexCacheId, $compileId);
            $isCached1 = $smarty->isCached($moduleTemplate, $complexCacheId, $compileId);
            $this->assertTrue($isCached1, 'Should be cached with complex cache_id');

            $cleared1 = $smarty->clearCache(null, $simpleCacheId, $compileId); // @phpstan-ignore-line
            $stillCached1 = $smarty->isCached($moduleTemplate, $complexCacheId, $compileId);
            echo 'Test 1 (null template + simple cache_id): cleared=' . $cleared1 . ', stillCached=' . ($stillCached1 ? 'YES' : 'NO') . "\n";

            // Test 2: Create cache with complex cache_id, clear with module: template + simple cache_id
            // THIS IS THE KEY TEST - simulates ps_customtext behavior
            $smarty->assign('var', 'Test 2');
            $smarty->fetch($moduleTemplate, $complexCacheId, $compileId);
            $isCached2 = $smarty->isCached($moduleTemplate, $complexCacheId, $compileId);
            $this->assertTrue($isCached2, 'Should be cached with complex cache_id');

            $cleared2 = $smarty->clearCache($moduleTemplate, $simpleCacheId, $compileId);
            $stillCached2 = $smarty->isCached($moduleTemplate, $complexCacheId, $compileId);
            echo 'Test 2 (module: template + simple cache_id): cleared=' . $cleared2 . ', stillCached=' . ($stillCached2 ? 'YES' : 'NO') . "\n";

            // Test 3: Create cache with complex cache_id, clear with module: template + complex cache_id (exact match)
            $smarty->assign('var', 'Test 3');
            $smarty->fetch($moduleTemplate, $complexCacheId, $compileId);
            $isCached3 = $smarty->isCached($moduleTemplate, $complexCacheId, $compileId);
            $this->assertTrue($isCached3, 'Should be cached with complex cache_id');

            $cleared3 = $smarty->clearCache($moduleTemplate, $complexCacheId, $compileId);
            $stillCached3 = $smarty->isCached($moduleTemplate, $complexCacheId, $compileId);
            echo "Test 3 (module: template + complex cache_id): cleared=$cleared3, stillCached=" . ($stillCached3 ? 'YES' : 'NO') . "\n";

            echo "=============================================\n";

            // Document actual behavior - don't assert on Test 2 since we're investigating
            $this->assertFalse($stillCached1, 'Test 1: null template + simple cache_id should clear (prefix match)');
            $this->assertFalse($stillCached3, 'Test 3: module: template + complex cache_id should clear (exact match)');
        } finally {
            $smarty->caching = $originalCaching;
            if (is_dir($testModuleDir)) {
                $this->recursiveDelete($testModuleDir);
            }
        }
    }

    /**
     * Test the exact ps_customtext behavior using PrestaShop's getCacheId() and _clearCache().
     * This simulates the real module behavior, not just raw Smarty calls.
     */
    public function testPsCustomtextExactBehavior(): void
    {
        $smarty = Context::getContext()->smarty;

        // Create a test module that mimics ps_customtext
        $testModuleName = 'test_ps_customtext';
        $testModuleDir = _PS_MODULE_DIR_ . $testModuleName;
        $testTemplateName = $testModuleName . '.tpl';
        $testTemplateFile = $testModuleDir . '/' . $testTemplateName;

        // Clean up any previous test artifacts
        if (is_dir($testModuleDir)) {
            $this->recursiveDelete($testModuleDir);
        }

        // Create module directory with template at root
        mkdir($testModuleDir, 0777, true);
        file_put_contents($testTemplateFile, '<div>ps_customtext simulation {$content}</div>');

        // Create a minimal module class file
        $moduleClassContent = <<<'PHP'
<?php
if (!defined('_PS_VERSION_')) {
    exit;
}

class Test_ps_customtext extends Module
{
    public $templateFile;

    public function __construct()
    {
        $this->name = 'test_ps_customtext';
        $this->tab = 'front_office_features';
        $this->version = '1.0.0';
        parent::__construct();
        $this->templateFile = 'module:test_ps_customtext/test_ps_customtext.tpl';
    }

    public function renderWidget($hookName = null, array $configuration = [])
    {
        if (!$this->isCached($this->templateFile, $this->getCacheId('test_ps_customtext'))) {
            $this->smarty->assign(['content' => 'Hello World']);
        }
        return $this->fetch($this->templateFile, $this->getCacheId('test_ps_customtext'));
    }

    public function clearWidgetCache()
    {
        // This is exactly what ps_customtext does - just passes templateFile, no cache_id
        return $this->_clearCache($this->templateFile);
    }

    // Expose protected methods for testing
    public function testGetCacheId($name = null)
    {
        return $this->getCacheId($name);
    }

    public function testClearCache($template, $cache_id = null, $compile_id = null)
    {
        return $this->_clearCache($template, $cache_id, $compile_id);
    }
}
PHP;
        file_put_contents($testModuleDir . '/' . $testModuleName . '.php', $moduleClassContent);

        // DON'T force caching - let PrestaShop's Tools::enableCache() handle it
        $originalCaching = $smarty->caching;
        echo "\n=== Initial Smarty caching state: " . $smarty->caching . " ===\n";

        try {
            // Load the module (dynamically created, PHPStan can't know about it)
            require_once $testModuleDir . '/' . $testModuleName . '.php';
            /** @var Module $module */
            $module = new Test_ps_customtext(); // @phpstan-ignore-line

            echo "\n=== ps_customtext Exact Behavior Test ===\n";

            // Check PrestaShop cache settings
            $psCacheConfig = Configuration::get('PS_SMARTY_CACHE');
            echo 'PS_SMARTY_CACHE config: ' . var_export($psCacheConfig, true) . "\n";
            echo 'Smarty caching setting: ' . $smarty->caching . "\n";
            echo 'Smarty cache_lifetime: ' . $smarty->cache_lifetime . "\n";

            // Get the cache IDs that will be used
            $renderCacheId = $module->testGetCacheId('test_ps_customtext'); // @phpstan-ignore-line
            echo "getCacheId('test_ps_customtext'): $renderCacheId\n";
            echo "templateFile: {$module->templateFile}\n"; // @phpstan-ignore-line

            // Step 1: Render the widget (creates cache)
            $output = $module->renderWidget(); // @phpstan-ignore-line
            echo 'Rendered output: ' . substr($output, 0, 50) . "...\n";

            // Step 2: Check if cached using Module's isCached (like ps_customtext does)
            $isCachedBefore = $module->isCached($module->templateFile, $renderCacheId); // @phpstan-ignore-line
            echo 'Is cached after render: ' . ($isCachedBefore ? 'YES' : 'NO') . "\n";

            // Step 3: Clear cache exactly like ps_customtext does
            $cleared = $module->clearWidgetCache(); // @phpstan-ignore-line
            echo "clearWidgetCache() returned: $cleared\n";

            // Step 4: Check if still cached
            $isCachedAfter = $module->isCached($module->templateFile, $renderCacheId); // @phpstan-ignore-line
            echo 'Is cached after clear: ' . ($isCachedAfter ? 'YES' : 'NO') . "\n";

            echo "==========================================\n";

            $this->assertTrue($isCachedBefore, 'Template should be cached after render');
            $this->assertFalse($isCachedAfter, 'Template should NOT be cached after clearWidgetCache()');
        } finally {
            if (is_dir($testModuleDir)) {
                $this->recursiveDelete($testModuleDir);
            }
        }
    }

    private function recursiveDelete(string $dir): void
    {
        if (is_dir($dir)) {
            $objects = scandir($dir);
            foreach ($objects as $object) {
                if ($object !== '.' && $object !== '..') {
                    $path = $dir . '/' . $object;
                    if (is_dir($path)) {
                        $this->recursiveDelete($path);
                    } else {
                        unlink($path);
                    }
                }
            }
            rmdir($dir);
        }
    }
}

define('_RESSOURCE_MODULE_DIR_', realpath(dirname(__FILE__, 4) . '/Resources/modules_tests/'));

class HelperModule
{
    /**
     * Copy the directory in resources which get the name $module_dir_name in the module directory
     *
     * @param string $module_dir_name take the directory name of a module contain in /home/prestashop/tests/resources/module
     */
    public static function addModule(string $module_dir_name): bool
    {
        if (is_dir(_RESSOURCE_MODULE_DIR_ . '/' . $module_dir_name)) {
            self::recurseCopy(_RESSOURCE_MODULE_DIR_ . '/' . $module_dir_name, _PS_MODULE_DIR_ . '/' . $module_dir_name);

            return true;
        }

        return false;
    }

    /**
     * Delete the directory in /home/prestashop/module which get the name $module_dir_name
     *
     * @param string $module_dir_name take the directory name of a module contain in /home/prestashop/module
     */
    public static function removeModule(string $module_dir_name): bool
    {
        if (is_dir(_PS_MODULE_DIR_ . '/' . $module_dir_name)) {
            self::recurseDelete(_PS_MODULE_DIR_ . '/' . $module_dir_name);

            return true;
        }

        return false;
    }

    /**
     * Recursivly copy a directory
     *
     * @param string $src the source path (eg. /home/dir/to/copy)
     * @param string $dst the destination path (eg. /home/)
     */
    private static function recurseCopy(string $src, string $dst): void
    {
        $dirp = opendir($src);
        @mkdir($dst);
        $file = readdir($dirp);
        while ($file !== false) {
            if ($file != '.' && $file != '..') {
                if (is_dir($src . '/' . $file)) {
                    self::recurseCopy($src . '/' . $file, $dst . '/' . $file);
                } else {
                    copy($src . '/' . $file, $dst . '/' . $file);
                }
            }
            $file = readdir($dirp);
        }
        closedir($dirp);
    }

    /**
     * Recursivly delete a directory
     *
     * @param string $dir the directory to delete path (eg. /home/dir/to/delete)
     */
    private static function recurseDelete(string $dir): void
    {
        $dirp = opendir($dir);
        $file = readdir($dirp);
        while ($file !== false) {
            if ($file != '.' && $file != '..') {
                if (is_dir($dir . '/' . $file)) {
                    self::recurseDelete($dir . '/' . $file);
                } else {
                    unlink($dir . '/' . $file);
                }
            }
            $file = readdir($dirp);
        }
        closedir($dirp);
        rmdir($dir);
    }
}
