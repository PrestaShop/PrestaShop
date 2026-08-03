<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Integration\Core\Import\Engine;

use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\Import\Engine\Exception\FileDownloadException;
use PrestaShop\PrestaShop\Core\Import\Engine\ImageDownloader;
use Tests\Resources\DummyFileUploader;

class ImageDownloaderTest extends TestCase
{
    private ImageDownloader $downloader;

    protected function setUp(): void
    {
        parent::setUp();
        $this->downloader = new ImageDownloader(new class() implements \PrestaShop\PrestaShop\Core\ConfigurationInterface {
            public function get($key, $default = null, ?\PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopConstraint $shopConstraint = null)
            {
                return sys_get_temp_dir();
            }

            public function set($key, $value, ?\PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopConstraint $shopConstraint = null, array $options = [])
            {
                return $this;
            }

            public function has($key)
            {
                return true;
            }

            public function remove($key)
            {
                return $this;
            }
        });
    }

    public function testLocalPathIsCopiedToATemporaryFile(): void
    {
        $sourcePath = DummyFileUploader::getDummyFilePath('logo.jpg');

        $temporaryPath = $this->downloader->download($sourcePath);

        $this->assertFileExists($temporaryPath);
        $this->assertSame(md5_file($sourcePath), md5_file($temporaryPath));
        @unlink($temporaryPath);
    }

    public function testMissingLocalFileIsRejected(): void
    {
        $this->expectException(FileDownloadException::class);
        $this->downloader->download('/nowhere/does-not-exist.jpg');
    }

    public function testUnsupportedSchemeIsRejected(): void
    {
        $this->expectException(FileDownloadException::class);
        $this->downloader->download('file:///etc/hosts');
    }
}
