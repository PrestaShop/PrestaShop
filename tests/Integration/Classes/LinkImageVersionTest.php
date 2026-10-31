<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Integration\Classes;

use Link;
use ReflectionMethod;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class LinkImageVersionTest extends KernelTestCase
{
    private const MODIFIED_AT = 1700000000;

    /**
     * @var string
     */
    private $imageFile;

    protected function setUp(): void
    {
        parent::setUp();
        self::bootKernel();

        $this->imageFile = tempnam(sys_get_temp_dir(), 'ps_image_') ?: '';
        file_put_contents($this->imageFile, 'not really a jpeg');
        touch($this->imageFile, self::MODIFIED_AT);
        clearstatcache(true, $this->imageFile);
    }

    protected function tearDown(): void
    {
        if ($this->imageFile !== '' && file_exists($this->imageFile)) {
            unlink($this->imageFile);
        }
        parent::tearDown();
    }

    public function testTheVersionIsTheMomentTheFileWasLastWritten(): void
    {
        $this->assertSame(
            'https://shop.test/1-home_default/product.jpg?v=' . self::MODIFIED_AT,
            $this->addImageVersion('https://shop.test/1-home_default/product.jpg', $this->imageFile)
        );
    }

    /**
     * A media server URL or an override may already carry parameters, and the stamp must not turn
     * them into a second question mark.
     */
    public function testAnExistingQueryStringIsExtendedRatherThanReplaced(): void
    {
        $this->assertSame(
            'https://cdn.test/1-home_default/product.jpg?token=abc&v=' . self::MODIFIED_AT,
            $this->addImageVersion('https://cdn.test/1-home_default/product.jpg?token=abc', $this->imageFile)
        );
    }

    /**
     * The file is not always reachable from the shop - a CDN origin, for instance. The URL is then
     * left as it was rather than stamped with an invented value.
     */
    public function testAnUnreachableFileLeavesTheUrlAlone(): void
    {
        $url = 'https://shop.test/1-home_default/product.jpg';

        $this->assertSame($url, $this->addImageVersion($url, '/does/not/exist/product.jpg'));
    }

    private function addImageVersion(string $url, string $filePath): string
    {
        $method = new ReflectionMethod(Link::class, 'addImageVersion');
        $method->setAccessible(true);

        return $method->invoke(new Link(), $url, $filePath);
    }
}
