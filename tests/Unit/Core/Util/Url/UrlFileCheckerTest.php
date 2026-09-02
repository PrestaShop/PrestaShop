<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace Tests\Unit\Core\Util\Url;

use org\bovigo\vfs\vfsStream;
use org\bovigo\vfs\vfsStreamDirectory;
use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\Util\Url\UrlFileChecker;
use PrestaShop\PrestaShop\Core\Util\Url\UrlFileCheckerInterface;

class UrlFileCheckerTest extends TestCase
{
    /**
     * @var vfsStreamDirectory
     */
    private $root;

    protected function setUp(): void
    {
        $this->root = vfsStream::setup('foo');

        vfsStream::newFile('not_writable_files/.htaccess', 0400)->at($this->root);
        vfsStream::newFile('not_writable_files/robots.txt', 0400)->at($this->root);

        vfsStream::newFile('writable_files/.htaccess', 0644)->at($this->root);
        vfsStream::newFile('writable_files/robots.txt', 0644)->at($this->root);
    }

    public function testIsValidImplementation()
    {
        $checker = new UrlFileChecker($this->root->url());

        $this->assertInstanceOf(UrlFileCheckerInterface::class, $checker);
    }

    public function testNotWritableFiles()
    {
        $checker = new UrlFileChecker($this->root->url() . '/not_writable_files');

        $this->assertFalse($checker->isHtaccessFileWritable());
        $this->assertFalse($checker->isRobotsFileWritable());
    }

    public function testWritableFiles()
    {
        $checker = new UrlFileChecker($this->root->url() . '/writable_files');

        $this->assertTrue($checker->isHtaccessFileWritable());
        $this->assertTrue($checker->isRobotsFileWritable());
    }
}
