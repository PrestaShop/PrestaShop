<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Integration\Classes;

use Configuration;
use FilesystemIterator;
use ImageType;
use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Adapter\ServiceLocator;
use PrestaShop\PrestaShop\Core\Image\ImageFormatConfiguration;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use ReflectionProperty;
use WebserviceRequest;
use WebserviceSpecificManagementImages;

/**
 * The back office writes every thumbnail in each format of PS_IMAGE_FORMAT; an image written
 * through the webservice has to come out the same, or the extra formats are missing or stale.
 */
class WebserviceImageThumbnailFormatsTest extends TestCase
{
    private string $directory;

    private string $source;

    /** @var string|false */
    private $formatsBefore;

    protected function setUp(): void
    {
        $this->formatsBefore = Configuration::get('PS_IMAGE_FORMAT');
        $this->directory = sys_get_temp_dir() . '/ws-thumbnails-' . uniqid() . '/';
        mkdir($this->directory . '1/2', 0777, true);

        $this->source = $this->directory . 'source.jpg';
        $image = imagecreatetruecolor(300, 200);
        imagefill($image, 0, 0, imagecolorallocate($image, 200, 40, 40));
        imagejpeg($image, $this->source);
        imagedestroy($image);
    }

    protected function tearDown(): void
    {
        if ($this->formatsBefore === false) {
            Configuration::deleteByName('PS_IMAGE_FORMAT');
        } else {
            Configuration::updateValue('PS_IMAGE_FORMAT', $this->formatsBefore);
        }
        $this->forgetMemoisedFormats();

        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($this->directory, FilesystemIterator::SKIP_DOTS),
            RecursiveIteratorIterator::CHILD_FIRST
        );
        foreach ($iterator as $entry) {
            $entry->isDir() ? rmdir($entry->getPathname()) : unlink($entry->getPathname());
        }
        rmdir($this->directory);
    }

    public function testAnEntityImagePutWritesEveryThumbnailInEveryConfiguredFormat(): void
    {
        $this->configureFormats('jpg,webp');
        $imageTypes = ImageType::getImagesTypes('manufacturers');

        $this->buildManager('manufacturers', ['images', 'manufacturers', '7'])
            ->put($this->source, $this->directory . '7.jpg', $imageTypes, $this->directory);

        $this->assertThumbnails($this->directory . '7', $imageTypes, ['jpg', 'webp']);
    }

    public function testAProductImagePutWritesEveryThumbnailInEveryConfiguredFormat(): void
    {
        $this->configureFormats('jpg,webp');
        $imageTypes = ImageType::getImagesTypes('products');

        $this->buildManager('products', ['images', 'products', '3', '12'])
            ->put($this->source, $this->directory . '1/2/12.jpg', $imageTypes, $this->directory);

        $this->assertThumbnails($this->directory . '1/2/12', $imageTypes, ['jpg', 'webp']);
    }

    public function testThreeConfiguredFormatsGiveThreeFilesPerThumbnail(): void
    {
        $this->configureFormats('jpg,png,webp');
        $imageTypes = ImageType::getImagesTypes('manufacturers');

        $this->buildManager('manufacturers', ['images', 'manufacturers', '7'])
            ->put($this->source, $this->directory . '7.jpg', $imageTypes, $this->directory);

        $this->assertThumbnails($this->directory . '7', $imageTypes, ['jpg', 'png', 'webp']);
    }

    public function testOnlyJpgIsWrittenWhenNothingElseIsConfigured(): void
    {
        $this->configureFormats('jpg');
        $imageTypes = ImageType::getImagesTypes('manufacturers');

        $this->buildManager('manufacturers', ['images', 'manufacturers', '7'])
            ->put($this->source, $this->directory . '7.jpg', $imageTypes, $this->directory);

        $this->assertThumbnails($this->directory . '7', $imageTypes, ['jpg']);
        $this->assertSame([], glob($this->directory . '7-*.webp'));
    }

    public function testEveryImageTypeThatCannotBeWrittenIsReportedOnce(): void
    {
        $this->configureFormats('jpg,webp');
        $imageTypes = ImageType::getImagesTypes('manufacturers');

        $failed = $this->buildManager('manufacturers', ['images', 'manufacturers', '7'])
            ->thumbnails($this->directory . 'missing.jpg', $this->directory . '7', $imageTypes);

        $this->assertNotEmpty($imageTypes);
        $this->assertSame(array_map('stripslashes', array_column($imageTypes, 'name')), $failed);
    }

    private function configureFormats(string $formats): void
    {
        Configuration::updateValue('PS_IMAGE_FORMAT', $formats);
        $this->forgetMemoisedFormats();
    }

    /**
     * The service is shared and memoises the formats for the request, which is one process in
     * production; these cases change them within one process.
     */
    private function forgetMemoisedFormats(): void
    {
        $service = ServiceLocator::get(ImageFormatConfiguration::class);
        (new ReflectionProperty($service, 'formatsToGenerate'))->setValue($service, []);
    }

    private function assertThumbnails(string $prefix, array $imageTypes, array $formats): void
    {
        $this->assertNotEmpty($imageTypes);
        $written = glob($prefix . '-*');
        $this->assertCount(count($imageTypes) * count($formats), $written, implode(', ', $written));

        $expectedTypes = ['jpg' => IMAGETYPE_JPEG, 'png' => IMAGETYPE_PNG, 'webp' => IMAGETYPE_WEBP];
        foreach ($imageTypes as $imageType) {
            foreach ($formats as $format) {
                $path = $prefix . '-' . stripslashes($imageType['name']) . '.' . $format;
                $this->assertFileExists($path);
                // PS_IMAGE_QUALITY decides what a .jpg holds, so only the other formats are pinned by content.
                if ($format !== 'jpg') {
                    $this->assertSame($expectedTypes[$format], getimagesize($path)[2], $path);
                }
            }
        }
    }

    private function buildManager(string $imageType, array $urlSegment)
    {
        $manager = new class() extends WebserviceSpecificManagementImages {
            public function put(string $source, string $target, array $imageTypes, string $parentPath): string
            {
                return $this->writeImageOnDisk($source, $target, null, null, $imageTypes, $parentPath);
            }

            public function thumbnails(string $source, string $prefix, array $imageTypes): array
            {
                return $this->writeThumbnails($source, $prefix, $imageTypes);
            }

            public function setImageType(string $imageType): void
            {
                $this->imageType = $imageType;
            }
        };

        $request = new WebserviceRequest();
        $request->urlSegment = $urlSegment;
        $manager->setWsObject($request);
        $manager->setImageType($imageType);

        return $manager;
    }
}
