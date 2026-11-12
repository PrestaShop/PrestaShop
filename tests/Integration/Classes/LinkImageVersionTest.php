<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Integration\Classes;

use Configuration;
use Image;
use Link;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

final class LinkImageVersionTest extends KernelTestCase
{
    private const MODIFIED_AT = 1700000000;
    private const ENTITY_ID = 99999991;

    /**
     * @var string[]
     */
    private array $createdFiles = [];

    /**
     * @var string[]
     */
    private array $createdDirectories = [];

    private string $rewritingSettings;

    protected function setUp(): void
    {
        parent::setUp();
        self::bootKernel();
        $this->rewritingSettings = (string) Configuration::get('PS_REWRITING_SETTINGS');
    }

    protected function tearDown(): void
    {
        foreach ($this->createdFiles as $file) {
            @unlink($file);
        }
        foreach (array_reverse($this->createdDirectories) as $directory) {
            @rmdir($directory);
        }
        Configuration::updateValue('PS_REWRITING_SETTINGS', $this->rewritingSettings);
        parent::tearDown();
    }

    /**
     * @dataProvider provideFriendlyUrls
     */
    public function testAProductImageIsStamped(bool $friendlyUrls): void
    {
        $this->createImage(_PS_PRODUCT_IMG_DIR_ . Image::getImgFolderStatic(self::ENTITY_ID) . self::ENTITY_ID . '-home_default.jpg');

        $url = $this->createLink($friendlyUrls)->getImageLink('product', (string) self::ENTITY_ID, 'home_default');

        $this->assertStringEndsWith('.jpg?v=' . self::MODIFIED_AT, $url);
    }

    /**
     * @dataProvider provideFriendlyUrls
     */
    public function testADefaultProductImageIsStamped(bool $friendlyUrls): void
    {
        $this->createImage(_PS_PRODUCT_IMG_DIR_ . 'zz-default-home_default.jpg');

        $url = $this->createLink($friendlyUrls)->getImageLink('product', 'zz-default', 'home_default');

        $this->assertStringEndsWith('/zz-default-home_default.jpg?v=' . self::MODIFIED_AT, $url);
    }

    public function testAMissingProductImageIsNotStamped(): void
    {
        $url = $this->createLink(true)->getImageLink('product', (string) self::ENTITY_ID, 'home_default');

        $this->assertStringNotContainsString('v=', $url);
    }

    /**
     * @dataProvider provideFriendlyUrls
     */
    public function testACategoryImageIsStamped(bool $friendlyUrls): void
    {
        $this->createImage(_PS_CAT_IMG_DIR_ . self::ENTITY_ID . '-category_default.jpg');

        $url = $this->createLink($friendlyUrls)->getCatImageLink('category', self::ENTITY_ID, 'category_default');

        $this->assertStringEndsWith('.jpg?v=' . self::MODIFIED_AT, $url);
    }

    /**
     * @dataProvider provideEntityImageLinks
     */
    public function testAnEntityImageIsStamped(string $directory, callable $getLink): void
    {
        $this->createImage($directory . self::ENTITY_ID . '-small_default.jpg');

        $url = $getLink($this->createLink(true));

        $this->assertStringEndsWith('/' . self::ENTITY_ID . '-small_default.jpg?v=' . self::MODIFIED_AT, $url);
    }

    public function testAnExistingQueryStringIsExtended(): void
    {
        $this->createImage(_PS_CAT_IMG_DIR_ . self::ENTITY_ID . '-category_default.jpg');
        $link = new class() extends Link {
            public function getMediaLink($filepath)
            {
                return parent::getMediaLink($filepath) . '?token=abc';
            }
        };

        $url = $link->getCatImageLink('category', self::ENTITY_ID, 'category_default');

        $this->assertStringEndsWith('.jpg?token=abc&v=' . self::MODIFIED_AT, $url);
    }

    public static function provideFriendlyUrls(): iterable
    {
        yield 'friendly URLs' => [true];
        yield 'plain URLs' => [false];
    }

    public static function provideEntityImageLinks(): iterable
    {
        yield 'brand' => [_PS_MANU_IMG_DIR_, static fn (Link $link) => $link->getManufacturerImageLink(self::ENTITY_ID, 'small_default')];
        yield 'supplier' => [_PS_SUPP_IMG_DIR_, static fn (Link $link) => $link->getSupplierImageLink(self::ENTITY_ID, 'small_default')];
        yield 'store' => [_PS_STORE_IMG_DIR_, static fn (Link $link) => $link->getStoreImageLink('store', self::ENTITY_ID, 'small_default')];
    }

    private function createLink(bool $friendlyUrls): Link
    {
        Configuration::updateValue('PS_REWRITING_SETTINGS', (int) $friendlyUrls);

        return new Link();
    }

    private function createImage(string $file): void
    {
        $missing = [];
        for ($directory = dirname($file); !is_dir($directory); $directory = dirname($directory)) {
            $missing[] = $directory;
        }
        foreach (array_reverse($missing) as $directory) {
            mkdir($directory);
            $this->createdDirectories[] = $directory;
        }
        file_put_contents($file, 'not really a jpeg');
        touch($file, self::MODIFIED_AT);
        clearstatcache(true, $file);
        $this->createdFiles[] = $file;
    }
}
