<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Integration\Classes;

use Meta;
use PHPUnit\Framework\TestCase;

class MetaGetPagesTest extends TestCase
{
    private string $overrideDir;

    /** @var string[] directories created by setUp, to remove (deepest first) in tearDown */
    private array $createdDirs = [];

    /** @var string[] */
    private array $movedFiles = [];

    protected function setUp(): void
    {
        parent::setUp();

        $this->overrideDir = _PS_CORE_DIR_ . DIRECTORY_SEPARATOR . 'override' . DIRECTORY_SEPARATOR . 'controllers' . DIRECTORY_SEPARATOR . 'front' . DIRECTORY_SEPARATOR;

        // Put the override directory into the reported state: present but containing no PHP file
        // (e.g. an override was removed, leaving the folder bare). Any directory this creates and
        // any file it moves aside is restored in tearDown so the checkout is left untouched.
        if (!is_dir($this->overrideDir)) {
            $path = '';
            foreach (explode(DIRECTORY_SEPARATOR, rtrim($this->overrideDir, DIRECTORY_SEPARATOR)) as $segment) {
                $path .= $segment . DIRECTORY_SEPARATOR;
                if ($path !== DIRECTORY_SEPARATOR && !is_dir($path)) {
                    mkdir($path);
                    $this->createdDirs[] = $path;
                }
            }

            return;
        }

        foreach (glob($this->overrideDir . '*.php') ?: [] as $file) {
            rename($file, $file . '.phpunitbak');
            $this->movedFiles[] = $file;
        }
    }

    protected function tearDown(): void
    {
        foreach ($this->movedFiles as $file) {
            if (file_exists($file . '.phpunitbak')) {
                rename($file . '.phpunitbak', $file);
            }
        }
        $this->movedFiles = [];

        foreach (array_reverse($this->createdDirs) as $dir) {
            if (is_dir($dir)) {
                rmdir($dir);
            }
        }
        $this->createdDirs = [];

        parent::tearDown();
    }

    /**
     * An existing but empty override/controllers/front directory must not abort the SEO & URLs page.
     *
     * @see https://github.com/PrestaShop/PrestaShop/issues/43431
     */
    public function testGetPagesDoesNotThrowWhenOverrideDirectoryIsEmpty(): void
    {
        $this->assertSame([], glob($this->overrideDir . '*.php') ?: [], 'precondition: the override directory contains no PHP file');

        $pages = Meta::getPages();

        $this->assertIsArray($pages);
        $this->assertNotEmpty($pages);
        // Non-vacuous: a known core front page is still listed.
        $this->assertArrayHasKey('contact', $pages);
    }
}
