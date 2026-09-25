<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\Core\Addon\Theme;

use Employee;
use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\Addon\Theme\Theme;
use PrestaShop\PrestaShop\Core\Addon\Theme\ThemeManager;
use PrestaShop\PrestaShop\Core\Addon\Theme\ThemeRepository;
use PrestaShop\PrestaShop\Core\Addon\Theme\ThemeValidator;
use PrestaShop\PrestaShop\Core\ConfigurationInterface;
use PrestaShop\PrestaShop\Core\Image\ImageTypeRepository;
use PrestaShop\PrestaShop\Core\Module\HookConfigurator;
use Shop;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * The parsed theme configuration is cached per shop under config/themes/<name>, outside the theme
 * directory, and the repository prefers it over config/theme.yml. Uninstalling has to take it with the
 * theme, or a later install of the same theme serves the previous version's data. See #39792.
 */
class ThemeManagerUninstallTest extends TestCase
{
    public function testUninstallAlsoRemovesTheCachedThemeConfiguration(): void
    {
        $themeDirectory = '/var/www/html/themes/hummingbird';
        $configDirectory = '/var/www/html/config/';

        $employee = $this->createMock(Employee::class);
        $employee->method('can')->willReturn(true);

        $configuration = $this->createMock(ConfigurationInterface::class);
        $configuration->method('get')->willReturnCallback(
            static fn (string $key) => '_PS_CONFIG_DIR_' === $key ? $configDirectory : null
        );

        $theme = $this->createMock(Theme::class);
        $theme->method('getDirectory')->willReturn($themeDirectory);

        $themeRepository = $this->createMock(ThemeRepository::class);
        $themeRepository->method('getInstanceByName')->willReturn($theme);

        $removed = [];
        $filesystem = $this->createMock(Filesystem::class);
        $filesystem->method('remove')->willReturnCallback(function ($path) use (&$removed): void {
            $removed[] = (string) $path;
        });

        $manager = new ThemeManager(
            $this->createMock(Shop::class),
            $configuration,
            $this->createMock(ThemeValidator::class),
            $this->createMock(TranslatorInterface::class),
            $employee,
            $filesystem,
            $this->createMock(Finder::class),
            $this->createMock(HookConfigurator::class),
            $themeRepository,
            $this->createMock(ImageTypeRepository::class)
        );

        self::assertTrue($manager->uninstall('hummingbird'));

        self::assertContains($themeDirectory, $removed, 'the theme directory is removed');
        self::assertContains(
            $configDirectory . 'themes/hummingbird',
            $removed,
            'the cached per-shop configuration is removed with it'
        );
    }
}
