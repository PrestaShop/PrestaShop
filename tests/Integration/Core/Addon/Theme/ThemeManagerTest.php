<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Integration\Core\Addon\Theme;

use FilesystemIterator;
use PrestaShop\PrestaShop\Adapter\Configuration;
use PrestaShop\PrestaShop\Core\Addon\Theme\ThemeManager;
use PrestaShop\PrestaShop\Core\Addon\Theme\ThemeRepository;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Filesystem\Filesystem;
use Tests\Integration\Utility\ContextMockerTrait;
use ZipArchive;

class ThemeManagerTest extends KernelTestCase
{
    use ContextMockerTrait;

    private const THEME_NAME = 'themeUpgradeFixture';

    private ThemeManager $themeManager;

    private ThemeRepository $themeRepository;

    private Filesystem $filesystem;

    private string $themesDir;

    private string $themeConfigDir;

    protected function setUp(): void
    {
        parent::setUp();

        $this->filesystem = new Filesystem();
        $this->themeManager = self::getContainer()->get('prestashop.core.addon.theme.theme_manager');

        $configuration = new Configuration();
        $configuration->restrictUpdatesTo(self::getContext()->shop);
        $this->themeRepository = new ThemeRepository($configuration, $this->filesystem, self::getContext()->shop);

        $this->themesDir = (string) $configuration->get('_PS_ALL_THEMES_DIR_') . self::THEME_NAME;
        $this->themeConfigDir = (string) $configuration->get('_PS_CONFIG_DIR_') . 'themes/' . self::THEME_NAME;

        $this->cleanUpFixtures();
    }

    protected function tearDown(): void
    {
        $this->cleanUpFixtures();
        parent::tearDown();
    }

    /**
     * Reproduces #39792: re-importing an upgraded theme left the stale per-shop config cache
     * (config/themes/<name>/shop<id>.json) in place, so the Theme & Logo page kept showing the
     * old version. The refresh must update the version while preserving the merchant's saved
     * page layouts (the cache is updated, not deleted).
     */
    public function testReimportRefreshesTheCachedVersionWhilePreservingPageLayouts(): void
    {
        // The merchant previously installed v1.0.0 and customised the page layouts, leaving an
        // orphaned cache behind after uninstalling the theme.
        $customLayouts = ['index' => 'layout-custom-marker'];
        $this->filesystem->dumpFile(
            $this->themeConfigDir . '/shop' . self::getContext()->shop->id . '.json',
            json_encode([
                'name' => self::THEME_NAME,
                'version' => '1.0.0',
                'theme_settings' => [
                    'default_layout' => 'layout-full-width',
                    'layouts' => $customLayouts,
                ],
            ])
        );

        // Re-import the same theme in v2.0.0.
        // ThemeManager::importTranslationToDatabase() resolves services through the global $kernel
        // and only checks that it is a KernelInterface, not that it is still booted; a test running
        // earlier in the suite can leave that global pointing at a kernel it already shut down.
        // Point it at this test's booted kernel just for the call, then put it straight back.
        global $kernel;
        $previousKernel = $kernel;
        $kernel = self::$kernel;

        try {
            $this->themeManager->install($this->buildThemeZip('2.0.0'));
        } finally {
            $kernel = $previousKernel;
        }

        $theme = $this->themeRepository->getInstanceByName(self::THEME_NAME);

        $this->assertSame('2.0.0', $theme->get('version'), 'The cached theme version must be refreshed on re-import.');
        $this->assertSame($customLayouts, $theme->get('theme_settings.layouts'), 'The merchant page layouts must be preserved on re-import.');
    }

    /**
     * Builds a zip of the minimal valid theme fixture renamed to self::THEME_NAME at the given version.
     */
    private function buildThemeZip(string $version): string
    {
        $sourceDir = _PS_ROOT_DIR_ . '/tests/Resources/themes/minimal-valid-theme';
        $stagingDir = sys_get_temp_dir() . '/' . self::THEME_NAME . '-' . $version;
        $this->filesystem->remove($stagingDir);
        $this->filesystem->mirror($sourceDir, $stagingDir);

        $yml = (string) file_get_contents($stagingDir . '/config/theme.yml');
        $yml = preg_replace('/^name:.*/m', 'name: ' . self::THEME_NAME, $yml);
        $yml = preg_replace('/^version:.*/m', 'version: ' . $version, $yml);
        $this->filesystem->dumpFile($stagingDir . '/config/theme.yml', (string) $yml);

        $zipPath = sys_get_temp_dir() . '/' . self::THEME_NAME . '-' . $version . '.zip';
        $this->filesystem->remove($zipPath);

        $zip = new ZipArchive();
        $zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE);
        $files = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($stagingDir, FilesystemIterator::SKIP_DOTS)
        );
        foreach ($files as $file) {
            /* @var \SplFileInfo $file */
            $zip->addFile($file->getPathname(), substr($file->getPathname(), strlen($stagingDir) + 1));
        }
        $zip->close();

        $this->filesystem->remove($stagingDir);

        return $zipPath;
    }

    private function cleanUpFixtures(): void
    {
        $this->filesystem->remove($this->themesDir);
        $this->filesystem->remove($this->themeConfigDir);
        $this->filesystem->remove(glob(sys_get_temp_dir() . '/' . self::THEME_NAME . '-*') ?: []);
    }
}
