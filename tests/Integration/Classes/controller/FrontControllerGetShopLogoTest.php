<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */
declare(strict_types=1);

namespace Tests\Integration\Classes\Controller;

use Configuration;
use Context;
use FrontControllerCore;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class FrontControllerGetShopLogoTest extends KernelTestCase
{
    /**
     * @var string[]
     */
    private $createdFiles = [];

    /**
     * @var mixed
     */
    private $previousLogo;

    protected function setUp(): void
    {
        self::bootKernel();
        Context::getContext()->container = self::getContainer();
        $this->previousLogo = Configuration::get('PS_LOGO');
    }

    protected function tearDown(): void
    {
        Configuration::updateValue('PS_LOGO', (string) $this->previousLogo);
        foreach ($this->createdFiles as $file) {
            @unlink($file);
        }
    }

    /**
     * getimagesize() cannot read an SVG and returns false. getShopLogo() must therefore
     * skip the width/height keys for an SVG logo (rather than feeding false into list(),
     * which yields null dimensions and, on stricter PHP versions, a warning).
     */
    public function testGetShopLogoOmitsDimensionsForAnSvgLogo(): void
    {
        $this->useLogo('test-shop-logo-41466.svg', '<svg xmlns="http://www.w3.org/2000/svg" width="120" height="40"></svg>');

        $logo = $this->makeController()->getShopLogo();

        $this->assertArrayHasKey('src', $logo);
        $this->assertArrayNotHasKey('width', $logo);
        $this->assertArrayNotHasKey('height', $logo);
    }

    /**
     * A regular raster logo (here a 1x1 PNG) must keep returning its dimensions.
     */
    public function testGetShopLogoKeepsDimensionsForARasterLogo(): void
    {
        $png = base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mNk+M8AAAMBAQDJ/pLvAAAAAElFTkSuQmCC');
        $this->useLogo('test-shop-logo-41466.png', $png);

        $logo = $this->makeController()->getShopLogo();

        $this->assertSame(1, $logo['width']);
        $this->assertSame(1, $logo['height']);
    }

    private function useLogo(string $fileName, string $contents): void
    {
        $path = _PS_IMG_DIR_ . $fileName;
        file_put_contents($path, $contents);
        $this->createdFiles[] = $path;
        Configuration::updateValue('PS_LOGO', $fileName);
    }

    private function makeController(): FrontControllerCore
    {
        return new class() extends FrontControllerCore {
            public function __construct()
            {
                $this->context = Context::getContext();
                $this->urls = ['img_ps_url' => ''];
            }
        };
    }
}
