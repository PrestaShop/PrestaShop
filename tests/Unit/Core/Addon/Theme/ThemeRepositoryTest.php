<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\Core\Addon\Theme;

use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\Addon\Theme\ThemeRepository;
use PrestaShop\PrestaShop\Core\ConfigurationInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Yaml\Dumper;

class ThemeRepositoryTest extends TestCase
{
    private const THEME_NAME = 'unit-test-theme';
    private const CHILD_NAME = 'unit-test-child-theme';
    private const CUSTOMIZABLE_PAGES = ['index', 'category', 'contact', 'cms'];

    /**
     * @var string
     */
    private $workspace;

    /**
     * @var CountingFilesystem
     */
    private $filesystem;

    protected function setUp(): void
    {
        parent::setUp();

        $this->workspace = sys_get_temp_dir() . '/ThemeRepositoryTest/' . uniqid('', true);
        $this->filesystem = new CountingFilesystem();
        $this->filesystem->mkdir($this->workspace . '/themes/' . self::THEME_NAME . '/config');
        $this->filesystem->mkdir($this->workspace . '/config/themes');
    }

    protected function tearDown(): void
    {
        (new Filesystem())->remove($this->workspace);

        parent::tearDown();
    }

    public function testTheConfigurationIsReadFromTheThemeOnAFirstRead(): void
    {
        $this->writeThemeYml('1.0.0');

        $this->assertSame('1.0.0', $this->getRepository()->getInstanceByName(self::THEME_NAME)->get('version'));
        $this->assertFileExists($this->jsonConfPath());
    }

    public function testTheConfigurationIsReadAgainAfterTheThemeFilesAreReplaced(): void
    {
        $this->writeThemeYml('1.0.0');
        $this->getRepository()->getInstanceByName(self::THEME_NAME);

        // What an update, a re-imported archive or a deployment does: the theme's own files are
        // replaced, and nothing touches the copy left under config/themes.
        $this->writeThemeYml('2.0.0');

        $theme = $this->getRepository()->getInstanceByName(self::THEME_NAME);

        $this->assertSame('2.0.0', $theme->get('version'));
        $this->assertSame('2.0.0', json_decode((string) file_get_contents($this->jsonConfPath()), true)['version']);
    }

    public function testTheCopyIsNotRewrittenWhileTheThemeIsUnchanged(): void
    {
        $this->writeThemeYml('1.0.0');

        $this->getRepository()->getInstanceByName(self::THEME_NAME);
        $writesAfterFirstRead = $this->filesystem->dumpFileCalls;
        $this->getRepository()->getInstanceByName(self::THEME_NAME);

        $this->assertSame(1, $writesAfterFirstRead);
        $this->assertSame(1, $this->filesystem->dumpFileCalls, 'the parsed copy must still be used as a cache');
    }

    public function testThePageLayoutCustomizationSurvivesTheRefresh(): void
    {
        $this->writeThemeYml('1.0.0');
        $this->getRepository()->getInstanceByName(self::THEME_NAME);
        $this->customizeSavedLayouts(['category' => 'layout-left-column']);

        // The new theme declares a different layout for a page the merchant never touched, so the
        // two halves can be told apart: one value has to come from the copy, the other from the yml.
        $this->writeThemeYml('2.0.0', null, ['category' => 'layout-full-width', 'contact' => 'layout-right-column']);

        $layouts = $this->getRepository()->getInstanceByName(self::THEME_NAME)->getPageLayouts();

        $this->assertSame('layout-left-column', $layouts['category'], 'the customized layout must survive');
        $this->assertSame('layout-right-column', $layouts['contact'], 'an untouched page must follow the new theme');
    }

    public function testACustomizedLayoutTheNewThemeDroppedIsNotCarriedOver(): void
    {
        $this->writeThemeYml('1.0.0');
        $this->getRepository()->getInstanceByName(self::THEME_NAME);
        $this->customizeSavedLayouts(['category' => 'layout-left-column']);

        $this->writeThemeYml('2.0.0', ['layout-full-width' => ['name' => 'Full width']]);

        $layouts = $this->getRepository()->getInstanceByName(self::THEME_NAME)->getPageLayouts();

        $this->assertSame('layout-full-width', $layouts['category']);
    }

    public function testACopyWrittenBeforeTheBaselineExistedKeepsItsLayouts(): void
    {
        // Every shop already on disk has a copy like this one: no record of what it was made from.
        $this->writeThemeYml('1.0.0');
        (new Filesystem())->dumpFile($this->jsonConfPath(), (string) json_encode([
            'name' => self::THEME_NAME,
            'version' => '0.9.0',
            'theme_settings' => ['default_layout' => 'layout-full-width', 'layouts' => ['category' => 'layout-left-column']],
        ]));

        $theme = $this->getRepository()->getInstanceByName(self::THEME_NAME);

        $this->assertSame('1.0.0', $theme->get('version'));
        $this->assertSame('layout-left-column', $theme->getPageLayouts()['category']);
    }

    public function testTheCopyIsKeptWhenTheThemeConfigurationIsGone(): void
    {
        $this->writeThemeYml('1.0.0');
        $this->getRepository()->getInstanceByName(self::THEME_NAME);
        (new Filesystem())->remove($this->themeYmlPath());

        $this->assertSame('1.0.0', $this->getRepository()->getInstanceByName(self::THEME_NAME)->get('version'));
    }

    public function testAPageTheNewThemeStartsDeclaringFollowsItAfterTheLayoutsFormWasSaved(): void
    {
        $this->writeThemeYml('1.0.0');
        $this->saveLayoutsForm(['category' => 'layout-left-column']);

        // The form saved every customizable page, the undeclared ones with the default layout
        $this->writeThemeYml('2.0.0', null, ['category' => 'layout-full-width', 'contact' => 'layout-full-width', 'cms' => 'layout-right-column']);

        $layouts = $this->getRepository()->getInstanceByName(self::THEME_NAME)->getPageLayouts();

        $this->assertSame('layout-left-column', $layouts['category'], 'the customized layout must survive');
        $this->assertSame('layout-right-column', $layouts['cms'], 'a page the new theme declares must follow it');
    }

    public function testANewDefaultLayoutAppliesToTheUndeclaredPagesAfterTheLayoutsFormWasSaved(): void
    {
        $this->writeThemeYml('1.0.0');
        $this->saveLayoutsForm(['category' => 'layout-left-column']);

        $this->writeThemeYml('2.0.0', null, null, 'default', 'layout-right-column');

        $theme = $this->getRepository()->getInstanceByName(self::THEME_NAME);

        $this->assertSame('layout-left-column', $theme->getLayoutNameForPage('category'));
        $this->assertSame('layout-right-column', $theme->getLayoutNameForPage('index'));
    }

    public function testAChildThemePicksUpAnUpdateOfItsParent(): void
    {
        $this->writeChildTheme('1.0.0');
        $this->writeThemeYml('1.0.0', null, null, 'old');
        $this->enableAndCustomize(['contact' => 'layout-right-column']);

        // The update replaces the parent's files; the child, which the merchant customizes, is untouched.
        $this->writeThemeYml('2.0.0', null, null, 'new');

        $theme = $this->getRepository()->getInstanceByName(self::CHILD_NAME);

        $this->assertSame('new', $theme->get('global_settings.configuration.PS_UNIT_TEST_SETTING'));
        $this->assertSame('layout-right-column', $theme->getPageLayouts()['contact']);
    }

    public function testAChildThemeKeepsItsCustomizedLayoutWhenItIsUpdated(): void
    {
        $this->writeChildTheme('1.0.0');
        $this->writeThemeYml('1.0.0');
        $this->enableAndCustomize(['contact' => 'layout-right-column']);

        $this->writeChildTheme('1.1.0');

        $theme = $this->getRepository()->getInstanceByName(self::CHILD_NAME);

        $this->assertSame('1.1.0', $theme->get('version'));
        $this->assertSame('layout-right-column', $theme->getPageLayouts()['contact']);
    }

    public function testAChildThemeCopyWrittenBeforeTheBaselineExistedKeepsItsLayouts(): void
    {
        $this->writeChildTheme('1.0.0');
        $this->writeThemeYml('1.0.0');
        $this->enableAndCustomize(['contact' => 'layout-right-column']);

        // What every child theme copy on disk looks like before the upgrade: saveTheme() output, no record.
        $saved = json_decode((string) file_get_contents($this->jsonConfPath(self::CHILD_NAME)), true);
        unset($saved['_source']);
        (new Filesystem())->dumpFile($this->jsonConfPath(self::CHILD_NAME), (string) json_encode($saved));

        $this->assertSame('layout-right-column', $this->getRepository()->getInstanceByName(self::CHILD_NAME)->getPageLayouts()['contact']);
    }

    private function getRepository(): ThemeRepository
    {
        $configuration = $this->createMock(ConfigurationInterface::class);
        $configuration->method('get')->willReturnMap([
            ['_PS_ALL_THEMES_DIR_', $this->workspace . '/themes/'],
            ['_PS_CONFIG_DIR_', $this->workspace . '/config/'],
        ]);

        return new ThemeRepository($configuration, $this->filesystem, null);
    }

    private function jsonConfPath(string $name = self::THEME_NAME): string
    {
        return $this->workspace . '/config/themes/' . $name . '/theme.json';
    }

    private function writeChildTheme(string $version): void
    {
        $file = $this->workspace . '/themes/' . self::CHILD_NAME . '/config/theme.yml';
        (new Filesystem())->dumpFile($file, (new Dumper())->dump([
            'parent' => self::THEME_NAME,
            'name' => self::CHILD_NAME,
            'display_name' => 'Unit test child theme',
            'version' => $version,
        ], 4));
    }

    /**
     * Replays ThemeManager::enable() - a first read, then saveTheme() writing the merged attributes -
     * followed by a page layout customization, which is saveTheme() again.
     *
     * @param array<string, string> $layouts
     */
    private function enableAndCustomize(array $layouts): void
    {
        $theme = $this->getRepository()->getInstanceByName(self::CHILD_NAME);
        $theme->setPageLayouts(array_merge($theme->getPageLayouts(), $layouts));
        (new Filesystem())->dumpFile($this->jsonConfPath(self::CHILD_NAME), (string) json_encode($theme->get(null)));
    }

    /**
     * Replays the Choose layouts form: PageLayoutCustomizationFormFactory fills every customizable page,
     * the ones the theme does not declare with the default layout, and ThemePageLayoutsCustomizer saves
     * the whole set with saveTheme().
     *
     * @param array<string, string> $changes
     */
    private function saveLayoutsForm(array $changes): void
    {
        $theme = $this->getRepository()->getInstanceByName(self::THEME_NAME);
        $layouts = [];
        foreach (self::CUSTOMIZABLE_PAGES as $page) {
            $layouts[$page] = $theme->getPageLayouts()[$page] ?? $theme->getDefaultLayout()['key'];
        }
        $theme->setPageLayouts(array_merge($layouts, $changes));
        (new Filesystem())->dumpFile($this->jsonConfPath(), (string) json_encode($theme->get(null)));
    }

    private function themeYmlPath(): string
    {
        return $this->workspace . '/themes/' . self::THEME_NAME . '/config/theme.yml';
    }

    /**
     * @param array<string, array<string, string>>|null $availableLayouts
     * @param array<string, string>|null $layouts
     */
    private function writeThemeYml(string $version, ?array $availableLayouts = null, ?array $layouts = null, string $setting = 'default', string $defaultLayout = 'layout-full-width'): void
    {
        $configuration = [
            'name' => self::THEME_NAME,
            'display_name' => 'Unit test theme',
            'version' => $version,
            'global_settings' => ['configuration' => ['PS_UNIT_TEST_SETTING' => $setting]],
            'meta' => ['available_layouts' => $availableLayouts ?? [
                'layout-full-width' => ['name' => 'Full width'],
                'layout-left-column' => ['name' => 'Left column'],
                'layout-right-column' => ['name' => 'Right column'],
            ]],
            'theme_settings' => [
                'default_layout' => $defaultLayout,
                'layouts' => $layouts ?? ['category' => 'layout-full-width', 'contact' => 'layout-full-width'],
            ],
        ];

        (new Filesystem())->dumpFile($this->themeYmlPath(), (new Dumper())->dump($configuration, 4));
    }

    /**
     * Replays what ThemeManager::saveTheme() writes when a merchant customizes the page layouts.
     *
     * @param array<string, string> $layouts
     */
    private function customizeSavedLayouts(array $layouts): void
    {
        $saved = json_decode((string) file_get_contents($this->jsonConfPath()), true);
        $saved['theme_settings']['layouts'] = array_merge($saved['theme_settings']['layouts'], $layouts);

        (new Filesystem())->dumpFile($this->jsonConfPath(), (string) json_encode($saved));
    }
}

class CountingFilesystem extends Filesystem
{
    /**
     * @var int
     */
    public $dumpFileCalls = 0;

    public function dumpFile(string $filename, $content): void
    {
        ++$this->dumpFileCalls;

        parent::dumpFile($filename, $content);
    }
}
