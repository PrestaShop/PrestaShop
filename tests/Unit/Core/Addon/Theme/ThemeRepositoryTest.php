<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\Core\Addon\Theme;

use ErrorException;
use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\Addon\Theme\ThemeRepository;
use PrestaShop\PrestaShop\Core\ConfigurationInterface;
use Symfony\Component\Filesystem\Filesystem;

class ThemeRepositoryTest extends TestCase
{
    private string $workDirectory;

    protected function setUp(): void
    {
        $this->workDirectory = sys_get_temp_dir() . '/ThemeRepositoryTest' . uniqid();
        $filesystem = new Filesystem();
        $filesystem->dumpFile($this->workDirectory . '/themes/named/config/theme.yml', "name: named\ndisplay_name: Named\n");
        $filesystem->dumpFile($this->workDirectory . '/themes/nameless/config/theme.yml', "display_name: Nameless\n");
    }

    protected function tearDown(): void
    {
        (new Filesystem())->remove($this->workDirectory);
    }

    public function testGetListSkipsThemesWithoutName(): void
    {
        $this->assertSame(['named'], array_keys($this->getThemeList()));
    }

    public function testThemeIsListedOnceItsNameIsAdded(): void
    {
        $this->getThemeList();
        $this->assertFileDoesNotExist($this->workDirectory . '/config/themes/nameless/theme.json');

        (new Filesystem())->dumpFile($this->workDirectory . '/themes/nameless/config/theme.yml', "name: nameless\ndisplay_name: Nameless\n");

        $this->assertSame(['named', 'nameless'], $this->sortedKeys($this->getThemeList()));
    }

    private function getThemeList(): array
    {
        $configuration = $this->createMock(ConfigurationInterface::class);
        $configuration->method('get')->willReturnMap([
            ['_PS_ALL_THEMES_DIR_', $this->workDirectory . '/themes/'],
            ['_PS_CONFIG_DIR_', $this->workDirectory . '/config/'],
        ]);
        $repository = new ThemeRepository($configuration, new Filesystem());

        set_error_handler(static function (int $severity, string $message): bool {
            throw new ErrorException($message, 0, $severity);
        });
        try {
            return $repository->getList();
        } finally {
            restore_error_handler();
        }
    }

    private function sortedKeys(array $themes): array
    {
        $keys = array_keys($themes);
        sort($keys);

        return $keys;
    }
}
