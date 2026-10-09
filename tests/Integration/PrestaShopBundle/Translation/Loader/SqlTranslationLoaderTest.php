<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Integration\PrestaShopBundle\Translation\Loader;

use Db;
use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Adapter\Module\Repository\ModuleRepository;
use PrestaShop\PrestaShop\Core\Addon\Theme\Theme;
use PrestaShopBundle\Translation\Loader\SqlTranslationLoader;
use PrestaShopBundle\Translation\TranslatorComponent;
use PrestaShopBundle\Translation\TranslatorLanguageLoader;
use Symfony\Component\Translation\Loader\XliffFileLoader;
use Symfony\Component\Translation\MessageCatalogue;
use Tests\Resources\DatabaseDump;

/**
 * Integration regression suite for SqlTranslationLoader (issue #41232).
 *
 * Scenarios covered:
 *   1. Core (theme IS NULL) translations always loaded
 *   2. Theme-exclusive translations loaded without theme context (PS9 Symfony-container path —
 *      setTheme() is never called; this is the primary cold-cache bug)
 *   3. Theme-exclusive translations loaded when theme is explicitly set
 *   4. Shop-theme override wins over core entry for the same key, no theme context
 *   5. Shop-theme override wins over core entry for the same key, theme set
 *   6. Multishop: all active shop themes loaded in a single query regardless of theme context
 *   7. Inactive shop theme rows are never included in the catalogue
 *   8. A translator queries the database once per catalogue build, after the files, and only when
 *      the language loader was asked to load the database translations
 *
 * Run:
 *   docker compose exec prestashop-git php ./vendor/phpunit/phpunit/phpunit \
 *     -c tests/Integration/phpunit.xml \
 *     tests/Integration/PrestaShopBundle/Translation/Loader/SqlTranslationLoaderTest.php
 */
class SqlTranslationLoaderTest extends TestCase
{
    private const DOMAIN_GLOBAL = 'Shop.Theme.Global';
    private const DOMAIN_CHECKOUT = 'Shop.Theme.Checkout';

    // Unique keys — prefixed to avoid any collision with real fixture data
    private const KEY_CORE_ONLY = '__test_sql_loader__core_only__';
    private const KEY_WITH_OVERRIDE = '__test_sql_loader__with_override__';
    private const KEY_THEME_A_EXCLUSIVE = '__test_sql_loader__theme_a_exclusive__';
    private const KEY_THEME_B_EXCLUSIVE = '__test_sql_loader__theme_b_exclusive__';
    private const KEY_INACTIVE_THEME = '__test_sql_loader__inactive_theme__';
    private const KEY_FILE_ONLY = '__test_sql_loader__file_only__';

    private const THEME_B_NAME = '__test_sql_loader_theme_b__';
    private const INACTIVE_THEME_NAME = '__test_sql_loader_inactive__';

    private static int $langId;
    private static string $locale;
    private static string $themeNameA;

    /** @var string[] */
    private array $xlfFiles = [];

    protected function tearDown(): void
    {
        foreach ($this->xlfFiles as $file) {
            @unlink($file);
        }
        parent::tearDown();
    }

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();

        $lang = Db::getInstance()->getRow(
            'SELECT `id_lang`, `locale` FROM `' . _DB_PREFIX_ . 'lang` WHERE `active` = 1'
        );
        self::$langId = (int) $lang['id_lang'];
        self::$locale = $lang['locale'];

        $shop = Db::getInstance()->getRow(
            'SELECT `theme_name`, `id_shop_group`, `id_category`
             FROM `' . _DB_PREFIX_ . 'shop`
             WHERE `active` = 1'
        );
        self::$themeNameA = $shop['theme_name'];

        // Second active shop with a different theme — used in multishop scenario
        Db::getInstance()->insert('shop', [
            'id_shop_group' => (int) $shop['id_shop_group'],
            'name' => 'Test Shop B (SqlTranslationLoaderTest)',
            'color' => '',
            'id_category' => (int) $shop['id_category'],
            'theme_name' => self::THEME_B_NAME,
            'active' => 1,
            'deleted' => 0,
        ]);

        self::seedTranslations();
    }

    public static function tearDownAfterClass(): void
    {
        parent::tearDownAfterClass();
        DatabaseDump::restoreTables(['translation', 'shop']);
    }

    // ── scenario 1 ────────────────────────────────────────────────────────────

    public function testCoreTranslationAlwaysLoadedWithoutThemeContext(): void
    {
        $loader = new SqlTranslationLoader();
        $catalogue = $loader->load('', self::$locale);

        $this->assertTrue(
            $catalogue->defines(self::KEY_CORE_ONLY, self::DOMAIN_GLOBAL),
            'Core (theme IS NULL) translation must always be present'
        );
        $this->assertSame('Core only value', $catalogue->get(self::KEY_CORE_ONLY, self::DOMAIN_GLOBAL));
    }

    public function testCoreTranslationAlwaysLoadedWithThemeContext(): void
    {
        $loader = (new SqlTranslationLoader())->setTheme($this->mockTheme());
        $catalogue = $loader->load('', self::$locale);

        $this->assertTrue($catalogue->defines(self::KEY_CORE_ONLY, self::DOMAIN_GLOBAL));
        $this->assertSame('Core only value', $catalogue->get(self::KEY_CORE_ONLY, self::DOMAIN_GLOBAL));
    }

    // ── scenario 2 — primary cold-cache bug ───────────────────────────────────

    public function testThemeExclusiveKeyLoadedWithoutThemeContext(): void
    {
        $loader = new SqlTranslationLoader();
        $catalogue = $loader->load('', self::$locale);

        $this->assertTrue(
            $catalogue->defines(self::KEY_THEME_A_EXCLUSIVE, self::DOMAIN_CHECKOUT),
            'Theme-exclusive key must be in the catalogue even when setTheme() was never called — '
            . 'in PS9 the Symfony container is always used and setTheme() is never invoked; '
            . 'failing here means a cold-cache request will serve English fallbacks to all visitors'
        );
        $this->assertSame('Theme A only value', $catalogue->get(self::KEY_THEME_A_EXCLUSIVE, self::DOMAIN_CHECKOUT));
    }

    // ── scenario 3 — Bug A (wrong column name) ────────────────────────────────

    public function testThemeExclusiveKeyLoadedWithThemeSet(): void
    {
        $loader = (new SqlTranslationLoader())->setTheme($this->mockTheme());
        $catalogue = $loader->load('', self::$locale);

        $this->assertTrue(
            $catalogue->defines(self::KEY_THEME_A_EXCLUSIVE, self::DOMAIN_CHECKOUT),
            'Theme-exclusive key must be present when setTheme() is called — '
            . 'the IN subquery must reference ps_shop.theme_name, not the non-existent ps_shop.theme column'
        );
        $this->assertSame('Theme A only value', $catalogue->get(self::KEY_THEME_A_EXCLUSIVE, self::DOMAIN_CHECKOUT));
    }

    // ── scenario 4 & 5 — override precedence ─────────────────────────────────

    public function testThemeOverrideWinsOverCoreWithoutThemeContext(): void
    {
        $loader = new SqlTranslationLoader();
        $catalogue = $loader->load('', self::$locale);

        $this->assertSame(
            'Theme A override',
            $catalogue->get(self::KEY_WITH_OVERRIDE, self::DOMAIN_GLOBAL),
            'When the same key exists in both core (theme IS NULL) and a shop theme, '
            . 'the shop-theme value must win regardless of theme context'
        );
    }

    public function testThemeOverrideWinsOverCoreWithThemeSet(): void
    {
        $loader = (new SqlTranslationLoader())->setTheme($this->mockTheme());
        $catalogue = $loader->load('', self::$locale);

        $this->assertSame(
            'Theme A override',
            $catalogue->get(self::KEY_WITH_OVERRIDE, self::DOMAIN_GLOBAL)
        );
    }

    // ── scenario 6 — multishop ────────────────────────────────────────────────

    public function testAllActiveShopThemesLoadedWithoutThemeContext(): void
    {
        $loader = new SqlTranslationLoader();
        $catalogue = $loader->load('', self::$locale);

        $this->assertTrue(
            $catalogue->defines(self::KEY_THEME_A_EXCLUSIVE, self::DOMAIN_CHECKOUT),
            'Theme A translations must be present (multishop)'
        );
        $this->assertTrue(
            $catalogue->defines(self::KEY_THEME_B_EXCLUSIVE, self::DOMAIN_CHECKOUT),
            'Theme B translations must be present even though theme B is a different active shop — '
            . 'the loader must cover all active shop themes in one query'
        );
    }

    public function testAllActiveShopThemesLoadedWithThemeSet(): void
    {
        $loader = (new SqlTranslationLoader())->setTheme($this->mockTheme());
        $catalogue = $loader->load('', self::$locale);

        $this->assertTrue($catalogue->defines(self::KEY_THEME_A_EXCLUSIVE, self::DOMAIN_CHECKOUT));
        $this->assertTrue(
            $catalogue->defines(self::KEY_THEME_B_EXCLUSIVE, self::DOMAIN_CHECKOUT),
            'Even when a specific theme is set the loader must still include all active shop themes; '
            . 'different shops in the same install may run different themes'
        );
    }

    // ── scenario 7 — inactive shop theme excluded ─────────────────────────────

    public function testInactiveShopThemeRowsNotLoaded(): void
    {
        $loader = new SqlTranslationLoader();
        $catalogue = $loader->load('', self::$locale);

        $this->assertFalse(
            $catalogue->defines(self::KEY_INACTIVE_THEME, self::DOMAIN_GLOBAL),
            'Translations for a theme that belongs to no active shop must not be loaded'
        );
    }

    // ── scenario 8 — one query per catalogue build ────────────────────────────

    public function testTranslatorQueriesTheDatabaseOnceAfterTheFiles(): void
    {
        $loader = new QueryCountingSqlTranslationLoader();
        $translator = new TranslatorComponent(self::$locale);
        $translator->addLoader('xlf', new XliffFileLoader());
        $translator->addLoader('db', $loader);
        $translator->addResource('xlf', $this->createXlfFile([self::KEY_WITH_OVERRIDE => 'File value', self::KEY_FILE_ONLY => 'File only value']), self::$locale, self::DOMAIN_GLOBAL);
        $translator->addResource('xlf', $this->createXlfFile([self::KEY_FILE_ONLY => 'File only value']), self::$locale, self::DOMAIN_CHECKOUT);

        $catalogue = $translator->getCatalogue(self::$locale);

        $this->assertSame(1, $loader->loads, 'Every domain comes from a single query');
        $this->assertSame('Theme A override', $catalogue->get(self::KEY_WITH_OVERRIDE, self::DOMAIN_GLOBAL), 'Database translations override the files');
        $this->assertSame('File only value', $catalogue->get(self::KEY_FILE_ONLY, self::DOMAIN_GLOBAL));
        $this->assertSame('Theme A only value', $catalogue->get(self::KEY_THEME_A_EXCLUSIVE, self::DOMAIN_CHECKOUT), 'Database-only keys are added');
    }

    public function testLanguageLoaderLoadsTheDatabaseTranslationsOnDemand(): void
    {
        $languageLoader = new TranslatorLanguageLoader(new ModuleRepository(_PS_ROOT_DIR_, _PS_MODULE_DIR_));

        $translator = new TranslatorComponent(self::$locale);
        $languageLoader->loadLanguage($translator, self::$locale);
        $this->assertSame('Core only value', $translator->getCatalogue(self::$locale)->get(self::KEY_CORE_ONLY, self::DOMAIN_GLOBAL));

        $translator = new TranslatorComponent(self::$locale);
        $languageLoader->loadLanguage($translator, self::$locale, false);
        $this->assertFalse($translator->getCatalogue(self::$locale)->defines(self::KEY_CORE_ONLY, self::DOMAIN_GLOBAL));
    }

    // ── helpers ───────────────────────────────────────────────────────────────

    /**
     * @param array<string, string> $messages
     */
    private function createXlfFile(array $messages): string
    {
        $units = '';
        foreach ($messages as $source => $target) {
            $units .= sprintf(
                '<trans-unit id="%s"><source>%s</source><target>%s</target></trans-unit>',
                md5($source),
                htmlspecialchars($source, ENT_XML1),
                htmlspecialchars($target, ENT_XML1)
            );
        }
        $file = tempnam(sys_get_temp_dir(), 'sql-loader-test-') . '.xlf';
        file_put_contents($file, '<?xml version="1.0" encoding="UTF-8"?>
<xliff xmlns="urn:oasis:names:tc:xliff:document:1.2" version="1.2">
  <file original="test.xlf" source-language="en" target-language="en" datatype="plaintext"><body>' . $units . '</body></file>
</xliff>');
        $this->xlfFiles[] = $file;

        return $file;
    }

    private function mockTheme(): Theme
    {
        // buildThemeCondition() only checks $this->theme !== null; getName() is never called
        return $this->createMock(Theme::class);
    }

    private static function seedTranslations(): void
    {
        $rows = [
            // Scenario 1 — core-only entry
            [self::KEY_CORE_ONLY, 'Core only value', self::DOMAIN_GLOBAL, null],

            // Scenarios 4 & 5 — same key in core + theme A; theme A must win
            [self::KEY_WITH_OVERRIDE, 'Core fallback', self::DOMAIN_GLOBAL, null],
            [self::KEY_WITH_OVERRIDE, 'Theme A override', self::DOMAIN_GLOBAL, self::$themeNameA],

            // Scenarios 2, 3, 6 — theme-exclusive entries (no core counterpart)
            [self::KEY_THEME_A_EXCLUSIVE, 'Theme A only value', self::DOMAIN_CHECKOUT, self::$themeNameA],
            [self::KEY_THEME_B_EXCLUSIVE, 'Theme B only value', self::DOMAIN_CHECKOUT, self::THEME_B_NAME],

            // Scenario 7 — inactive-shop theme must be silently excluded
            [self::KEY_INACTIVE_THEME, 'Should never appear', self::DOMAIN_GLOBAL, self::INACTIVE_THEME_NAME],
        ];

        $db = Db::getInstance();
        $prefix = _DB_PREFIX_;

        foreach ($rows as [$key, $translation, $domain, $theme]) {
            $themeSQL = $theme !== null ? '"' . $db->escape($theme) . '"' : 'NULL';
            $db->execute(
                'INSERT INTO `' . $prefix . 'translation`
                    (`id_lang`, `key`, `translation`, `domain`, `theme`)
                 VALUES
                    (' . self::$langId . ',
                     "' . $db->escape($key) . '",
                     "' . $db->escape($translation) . '",
                     "' . $db->escape($domain) . '",
                     ' . $themeSQL . ')'
            );
        }
    }
}

final class QueryCountingSqlTranslationLoader extends SqlTranslationLoader
{
    public int $loads = 0;

    public function load($resource, $locale, $domain = 'messages'): MessageCatalogue
    {
        ++$this->loads;

        return parent::load($resource, $locale, $domain);
    }
}
