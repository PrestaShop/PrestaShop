<?php

/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\Core\Addon\Theme;

use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\Addon\Theme\Theme;
use PrestaShop\PrestaShop\Core\Addon\Theme\ThemeExporter;
use PrestaShop\PrestaShop\Core\ConfigurationInterface;
use PrestaShopBundle\Entity\Repository\LangRepository;
use PrestaShopBundle\Translation\Exporter\ThemeExporter as TranslationsExporter;
use Symfony\Component\Filesystem\Filesystem;
use ZipArchive;

class ThemeExporterDotFilesTest extends TestCase
{
    private const THEME_NAME = 'dotfiles_theme';
    private const MODULE_NAME = 'dotfiles_module';

    /**
     * @var string
     */
    private $workDir;

    /**
     * @var Filesystem
     */
    private $fileSystem;

    protected function setUp(): void
    {
        $this->fileSystem = new Filesystem();
        $this->workDir = sys_get_temp_dir() . '/ThemeExporterDotFilesTest_' . uniqid() . '/';

        $files = [
            'themes/index.php' => '<?php',
            'themes/' . self::THEME_NAME . '/config/theme.yml' => 'name: ' . self::THEME_NAME,
            'themes/' . self::THEME_NAME . '/.eslintrc' => '{}',
            'themes/' . self::THEME_NAME . '/.env.example' => 'API_KEY=',
            'themes/' . self::THEME_NAME . '/.env' => 'API_KEY=secret',
            'themes/' . self::THEME_NAME . '/_dev/.stylelintrc' => '{}',
            'themes/' . self::THEME_NAME . '/.github/workflows/build.yml' => 'on: push',
            'themes/' . self::THEME_NAME . '/node_modules/lib/index.js' => '',
            'modules/' . self::MODULE_NAME . '/' . self::MODULE_NAME . '.php' => '<?php',
            'modules/' . self::MODULE_NAME . '/.eslintrc' => '{}',
            'modules/' . self::MODULE_NAME . '/.env' => 'API_KEY=secret',
        ];
        foreach ($files as $path => $content) {
            $this->fileSystem->dumpFile($this->workDir . $path, $content);
        }
        $this->fileSystem->mkdir($this->workDir . 'cache');
    }

    protected function tearDown(): void
    {
        $this->fileSystem->remove($this->workDir);
    }

    public function testExportKeepsDotFilesExceptEnv(): void
    {
        $zipEntries = $this->getZipEntries($this->createExporter()->export($this->createTheme()));

        // Dot files and dot directories of the theme are exported, and protected by an index.php
        $this->assertContains('.eslintrc', $zipEntries);
        $this->assertContains('.env.example', $zipEntries);
        $this->assertContains('_dev/.stylelintrc', $zipEntries);
        $this->assertContains('.github/workflows/build.yml', $zipEntries);
        $this->assertContains('.github/index.php', $zipEntries);
        $this->assertContains('dependencies/modules/' . self::MODULE_NAME . '/.eslintrc', $zipEntries);

        // .env files may contain secrets, they are never exported
        $this->assertNotContains('.env', $zipEntries);
        $this->assertNotContains('dependencies/modules/' . self::MODULE_NAME . '/.env', $zipEntries);

        // node_modules is still excluded
        $this->assertEmpty(preg_grep('#(^|/)node_modules/#', $zipEntries));
    }

    private function createExporter(): ThemeExporter
    {
        $configuration = $this->createMock(ConfigurationInterface::class);
        $configuration->method('get')->willReturnMap([
            ['_PS_CACHE_DIR_', $this->workDir . 'cache/'],
            ['_PS_ALL_THEMES_DIR_', $this->workDir . 'themes'],
            ['_PS_MODULE_DIR_', $this->workDir . 'modules/'],
        ]);

        // No language: the translations step only creates an empty directory
        $langRepository = $this->createMock(LangRepository::class);
        $langRepository->method('findAll')->willReturn([]);

        return new ThemeExporter(
            $configuration,
            $this->fileSystem,
            $langRepository,
            $this->createMock(TranslationsExporter::class)
        );
    }

    private function createTheme(): Theme
    {
        return new Theme(
            [
                'name' => self::THEME_NAME,
                'directory' => $this->workDir . 'themes/' . self::THEME_NAME,
                'dependencies' => ['modules' => [self::MODULE_NAME]],
            ],
            $this->workDir . 'cache/',
            $this->workDir . 'themes/'
        );
    }

    /**
     * @return string[]
     */
    private function getZipEntries(string $zipFile): array
    {
        $zip = new ZipArchive();
        $this->assertTrue($zip->open($zipFile));

        $entries = [];
        for ($i = 0; $i < $zip->numFiles; ++$i) {
            $entries[] = $zip->getNameIndex($i);
        }
        $zip->close();

        return $entries;
    }
}
