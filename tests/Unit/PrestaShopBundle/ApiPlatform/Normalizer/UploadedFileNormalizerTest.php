<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\PrestaShopBundle\ApiPlatform\Normalizer;

use PHPUnit\Framework\TestCase;
use PrestaShopBundle\ApiPlatform\Normalizer\UploadedFileNormalizer;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class UploadedFileNormalizerTest extends TestCase
{
    private string $path;

    protected function setUp(): void
    {
        parent::setUp();

        $this->path = tempnam(sys_get_temp_dir(), 'uploaded-file-normalizer-');
        file_put_contents($this->path, "name;price\nChair;12.5\n");
    }

    protected function tearDown(): void
    {
        @unlink($this->path);

        parent::tearDown();
    }

    /**
     * pathName is PHP's random temp name; a command that reports on the file needs the name the
     * client gave it, mapped from here in the resource's CQRSCommandMapping.
     */
    public function testAnUploadExposesTheNameTheClientGaveIt(): void
    {
        $normalized = (new UploadedFileNormalizer())->normalize(
            new UploadedFile($this->path, 'Catalogue 2026.csv', 'text/csv', null, true)
        );

        $this->assertSame($this->path, $normalized['pathName']);
        $this->assertSame('Catalogue 2026.csv', $normalized['clientOriginalName']);
    }

    public function testAPlainFileHasNoClientName(): void
    {
        $normalized = (new UploadedFileNormalizer())->normalize(new File($this->path));

        $this->assertSame($this->path, $normalized['pathName']);
        $this->assertArrayHasKey('clientOriginalName', $normalized, 'The key is always there, so a mapping never dereferences a missing one');
        $this->assertNull($normalized['clientOriginalName']);
    }
}
