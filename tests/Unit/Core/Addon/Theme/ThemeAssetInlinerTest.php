<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\Core\Addon\Theme;

use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\Addon\Theme\ThemeAssetInliner;
use Symfony\Component\Filesystem\Filesystem;

class ThemeAssetInlinerTest extends TestCase
{
    private const CHILD_SVG = '<svg viewBox="0 0 10 10"><style>.a{fill:red}</style><path d="M0 0"/></svg>';
    private const PARENT_SVG = '<svg viewBox="0 0 20 20"><path d="M1 1"/></svg>';

    private Filesystem $fs;
    private string $root;
    private string $childAssets;
    private string $parentAssets;

    protected function setUp(): void
    {
        parent::setUp();
        $this->fs = new Filesystem();
        $this->root = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'theme-asset-inliner-' . uniqid();
        $this->childAssets = $this->root . '/themes/child/assets';
        $this->parentAssets = $this->root . '/themes/parent/assets';

        $this->fs->dumpFile($this->childAssets . '/img/icon.svg', self::CHILD_SVG);
        $this->fs->dumpFile($this->parentAssets . '/img/icon.svg', self::PARENT_SVG);
        $this->fs->dumpFile($this->parentAssets . '/img/parent-only.svg', self::PARENT_SVG);
        $this->fs->dumpFile($this->childAssets . '/img/not-an-image.txt', 'plain');
        $this->fs->dumpFile($this->root . '/themes/child/config/theme.yml', 'name: child');
        $this->fs->dumpFile($this->root . '/outside.svg', '<svg id="outside"/>');
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        $this->fs->remove($this->root);
    }

    private function inliner(): ThemeAssetInliner
    {
        return new ThemeAssetInliner([$this->childAssets, $this->parentAssets]);
    }

    public function testItReadsAnSvgVerbatim(): void
    {
        // verbatim matters: the CSS braces are exactly what makes {include} throw
        $this->assertSame(self::CHILD_SVG, $this->inliner()->inline('img/icon.svg'));
    }

    public function testItFallsBackToTheNextRoot(): void
    {
        $this->assertSame(self::PARENT_SVG, $this->inliner()->inline('img/parent-only.svg'));
    }

    public function testTheFirstRootWins(): void
    {
        $this->assertSame(self::CHILD_SVG, $this->inliner()->inline('img/icon.svg'));
        $this->assertSame(self::PARENT_SVG, (new ThemeAssetInliner([$this->parentAssets]))->inline('img/icon.svg'));
    }

    public function testItAcceptsAnUppercaseExtension(): void
    {
        $this->fs->dumpFile($this->childAssets . '/img/upper.SVG', self::CHILD_SVG);
        $this->assertSame(self::CHILD_SVG, $this->inliner()->inline('img/upper.SVG'));
    }

    public function testItResolvesADotSegmentThatStaysInside(): void
    {
        $this->assertSame(self::CHILD_SVG, $this->inliner()->inline('img/../img/icon.svg'));
    }

    /**
     * @dataProvider provideRejectedPaths
     */
    public function testItRejects(string $relativePath): void
    {
        $this->assertNull($this->inliner()->inline($relativePath));
    }

    public static function provideRejectedPaths(): array
    {
        return [
            'empty' => [''],
            'blank' => ['   '],
            'missing file' => ['img/nope.svg'],
            'a directory' => ['img'],
            'another extension' => ['img/not-an-image.txt'],
            'no extension' => ['img/icon'],
            'a theme file that is not an asset' => ['../config/theme.yml'],
            'climbing out of the root' => ['../../../outside.svg'],
            'climbing out with a leading slash' => ['/../../../outside.svg'],
            'an absolute path' => ['/etc/hosts.svg'],
            'a null byte' => ["img/icon.svg\0.txt"],
            'a backslash prefix' => ['\\..\\..\\..\\outside.svg'],
        ];
    }

    public function testItRejectsASymlinkPointingOutsideTheRoot(): void
    {
        if (!@symlink($this->root . '/outside.svg', $this->childAssets . '/img/linked.svg')) {
            $this->markTestSkipped('symlinks are not available here');
        }

        $this->assertNull($this->inliner()->inline('img/linked.svg'));
    }

    public function testItIgnoresARootThatDoesNotExist(): void
    {
        $inliner = new ThemeAssetInliner([$this->root . '/themes/gone/assets', $this->childAssets]);

        $this->assertSame(self::CHILD_SVG, $inliner->inline('img/icon.svg'));
    }

    public function testItReturnsNullWithNoRootsAtAll(): void
    {
        $this->assertNull((new ThemeAssetInliner([]))->inline('img/icon.svg'));
    }
}
