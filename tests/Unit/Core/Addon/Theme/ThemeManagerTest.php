<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\Core\Addon\Theme;

use Employee;
use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\Addon\Theme\ThemeManager;
use PrestaShop\PrestaShop\Core\Addon\Theme\ThemeRepository;
use PrestaShop\PrestaShop\Core\Addon\Theme\ThemeValidator;
use PrestaShop\PrestaShop\Core\ConfigurationInterface;
use PrestaShop\PrestaShop\Core\Domain\Theme\Exception\ThemeConstraintException;
use PrestaShop\PrestaShop\Core\Image\ImageTypeRepository;
use PrestaShop\PrestaShop\Core\Module\HookConfigurator;
use Shop;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;
use Symfony\Contracts\Translation\TranslatorInterface;
use Tests\Resources\FakeHttpStreamWrapper;

final class ThemeManagerTest extends TestCase
{
    private string $cacheDir;

    private string $fileName;

    protected function setUp(): void
    {
        $this->cacheDir = sys_get_temp_dir() . DIRECTORY_SEPARATOR . uniqid('theme_manager_test_') . DIRECTORY_SEPARATOR;
        $this->fileName = uniqid('theme_');
        (new Filesystem())->mkdir(_PS_CACHE_DIR_);
    }

    protected function tearDown(): void
    {
        FakeHttpStreamWrapper::unregister();
        (new Filesystem())->remove([$this->cacheDir, $this->fileName . '.txt', $this->fileName . '.zip']);
    }

    public function testInstallThrowsWhenTheUrlIsNotAZipArchive(): void
    {
        $fileName = $this->fileName . '.txt';
        FakeHttpStreamWrapper::register('not an archive');

        try {
            $this->createThemeManager()->install('http://example.com/' . $fileName);
            $this->fail('A URL which is not a zip archive should not be accepted');
        } catch (ThemeConstraintException $e) {
            $this->assertSame(ThemeConstraintException::CANNOT_DOWNLOAD_FROM_URL, $e->getCode());
        }

        $this->assertSame([], FakeHttpStreamWrapper::$openedUrls);
        $this->assertFileDoesNotExist($fileName);
    }

    public function testInstallRemovesTheDownloadedArchive(): void
    {
        $fileName = $this->fileName . '.zip';
        FakeHttpStreamWrapper::register('not a valid archive');

        try {
            $this->createThemeManager()->install('http://example.com/' . $fileName);
            $this->fail('An invalid archive should not be installed');
        } catch (ThemeConstraintException $e) {
            $this->assertSame(ThemeConstraintException::INVALID_DATA, $e->getCode());
        }

        $this->assertSame(['http://example.com/' . $fileName], FakeHttpStreamWrapper::$openedUrls);
        $this->assertFileDoesNotExist($fileName);
        $this->assertSame([], glob(_PS_CACHE_DIR_ . 'sandbox' . DIRECTORY_SEPARATOR . '*' . $fileName));
    }

    private function createThemeManager(): ThemeManager
    {
        $configuration = $this->createMock(ConfigurationInterface::class);
        $configuration->method('get')->willReturnMap([
            ['_PS_CACHE_DIR_', null, null, $this->cacheDir],
        ]);

        return new ThemeManager(
            $this->createMock(Shop::class),
            $configuration,
            $this->createMock(ThemeValidator::class),
            $this->createMock(TranslatorInterface::class),
            $this->createMock(Employee::class),
            new Filesystem(),
            new Finder(),
            $this->createMock(HookConfigurator::class),
            $this->createMock(ThemeRepository::class),
            $this->createMock(ImageTypeRepository::class)
        );
    }
}
