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
    /**
     * @var string
     */
    private $cacheDir;

    /**
     * @var string
     */
    private $fileName;

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
        $this->assertFalse(file_exists($fileName));
    }

    public function testInstallThrowsWhenTheUrlSchemeIsNotAllowed(): void
    {
        try {
            $this->createThemeManager()->install('ftp://example.com/' . $this->fileName . '.zip');
            $this->fail('A theme which cannot be downloaded should not be reported as installed');
        } catch (ThemeConstraintException $e) {
            $this->assertSame(ThemeConstraintException::CANNOT_DOWNLOAD_FROM_URL, $e->getCode());
        }
    }

    public function testInstallThrowsWhenTheArchiveCannotBeDownloaded(): void
    {
        $url = 'http://example.com/' . $this->fileName . '.zip';
        FakeHttpStreamWrapper::register('', false);

        // fopen() raises a warning on the unreachable URL, it is caught to reach the exception
        $warnings = [];
        set_error_handler(static function (int $level, string $message) use (&$warnings): bool {
            $warnings[] = $message;

            return true;
        }, E_WARNING);
        try {
            $this->createThemeManager()->install($url);
            $this->fail('A theme which cannot be downloaded should not be reported as installed');
        } catch (ThemeConstraintException $e) {
            $this->assertSame(ThemeConstraintException::CANNOT_DOWNLOAD_FROM_URL, $e->getCode());
        } finally {
            restore_error_handler();
        }

        $this->assertCount(1, $warnings);
        $this->assertStringContainsString($url, $warnings[0]);
        $this->assertSame([$url], FakeHttpStreamWrapper::$openedUrls);
    }

    public function testInstallRemovesTheDownloadedArchive(): void
    {
        $fileName = $this->fileName . '.zip';
        FakeHttpStreamWrapper::register('not a valid archive');

        try {
            $this->createThemeManager()->install('http://example.com/' . $fileName);
            $this->fail('An invalid archive should not be installed');
        } catch (ThemeConstraintException $e) {
            $this->assertSame(ThemeConstraintException::MISSING_CONFIGURATION_FILE, $e->getCode());
        }

        $this->assertSame(['http://example.com/' . $fileName], FakeHttpStreamWrapper::$openedUrls);
        $this->assertFalse(file_exists($fileName));
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
