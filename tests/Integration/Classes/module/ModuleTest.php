<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace Tests\Integration\Classes\module;

use Cache;
use Db;
use Language;
use Module;
use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\Addon\Module\ModuleManagerBuilder;
use ReflectionMethod;
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

    public function testDeclaredControllersAreInstalledWithTheirMetaAndRemovedOnUninstall(): void
    {
        $frenchId = $this->addLanguage('fr');
        $module = $this->getTestModule([
            'plain',
            ['name' => 'single', 'title' => 'Single title', 'url_rewrite' => 'single-slug'],
            ['name' => 'mapped', 'url_rewrite' => ['en' => 'order-confirmed', 'fr' => 'commande-confirmee']],
        ]);

        try {
            $this->assertTrue($module->install());

            $this->assertSame([1 => ['', ''], $frenchId => ['', '']], $this->getPageMeta('plain'));
            $this->assertSame([1 => ['Single title', 'single-slug'], $frenchId => ['Single title', 'single-slug']], $this->getPageMeta('single'));
            $this->assertSame([1 => ['', 'order-confirmed'], $frenchId => ['', 'commande-confirmee']], $this->getPageMeta('mapped'));

            $this->assertTrue($module->uninstall());
            $this->assertSame(0, $this->countPages());
        } finally {
            $this->removeTestModule($module);
            $this->removeLanguage($frenchId);
        }
    }

    public function testAnInvalidDeclarationInstallsNoPage(): void
    {
        $module = $this->getTestModule([
            ['name' => 'valid', 'url_rewrite' => 'valid-slug'],
            ['name' => 'invalid', 'url_rewrite' => 'not a slug!'],
        ]);

        try {
            $this->assertFalse($module->install());
            $this->assertContains('Could not install module controllers.', $module->getErrors());
            $this->assertSame(0, $this->countPages());
        } finally {
            $this->removeTestModule($module);
        }
    }

    private function getTestModule(array $controllers): Module
    {
        // Module translations are unrelated, and updating them needs the Symfony container
        Module::updateTranslationsAfterInstall(false);
        HelperModule::addModule('testnooverride');
        $module = Module::getInstanceByName('testnooverride');
        $module->controllers = $controllers;

        return $module;
    }

    private function removeTestModule(Module $module): void
    {
        if (Module::isInstalled($module->name)) {
            $module->uninstall();
        }
        Db::getInstance()->execute(
            'DELETE m, ml FROM ' . _DB_PREFIX_ . 'meta m LEFT JOIN ' . _DB_PREFIX_ . 'meta_lang ml ON ml.id_meta = m.id_meta'
            . ' WHERE m.page LIKE "module-testnooverride-%"'
        );
        HelperModule::removeModule('testnooverride');
        Module::resetStaticCache();
        Module::updateTranslationsAfterInstall(true);
    }

    /**
     * @return array<int, array{0: string, 1: string}> title and url_rewrite by language id
     */
    private function getPageMeta(string $controller): array
    {
        $rows = Db::getInstance()->executeS(
            'SELECT ml.id_lang, ml.title, ml.url_rewrite FROM ' . _DB_PREFIX_ . 'meta m'
            . ' INNER JOIN ' . _DB_PREFIX_ . 'meta_lang ml ON ml.id_meta = m.id_meta'
            . ' WHERE m.page = "module-testnooverride-' . pSQL($controller) . '" AND ml.id_shop = 1 ORDER BY ml.id_lang'
        );
        $meta = [];
        foreach ($rows as $row) {
            $meta[(int) $row['id_lang']] = [$row['title'], $row['url_rewrite']];
        }

        return $meta;
    }

    private function countPages(): int
    {
        return (int) Db::getInstance()->getValue('SELECT COUNT(*) FROM ' . _DB_PREFIX_ . 'meta WHERE page LIKE "module-testnooverride-%"');
    }

    private function addLanguage(string $isoCode): int
    {
        Db::getInstance()->insert('lang', [
            'name' => 'Probe ' . $isoCode,
            'active' => 1,
            'iso_code' => $isoCode,
            'language_code' => $isoCode,
            'locale' => $isoCode . '-' . strtoupper($isoCode),
            'date_format_lite' => 'Y-m-d',
            'date_format_full' => 'Y-m-d H:i:s',
            'is_rtl' => 0,
        ]);
        $languageId = (int) Db::getInstance()->Insert_ID();
        Db::getInstance()->insert('lang_shop', ['id_lang' => $languageId, 'id_shop' => 1]);
        Language::resetStaticCache();

        return $languageId;
    }

    private function removeLanguage(int $languageId): void
    {
        Db::getInstance()->delete('lang_shop', 'id_lang = ' . $languageId);
        Db::getInstance()->delete('lang', 'id_lang = ' . $languageId);
        Language::resetStaticCache();
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
