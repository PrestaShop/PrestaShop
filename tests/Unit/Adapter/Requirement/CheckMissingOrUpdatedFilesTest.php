<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\Adapter\Requirement;

use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Adapter\Requirement\CheckMissingOrUpdatedFiles;
use SimpleXMLElement;

class CheckMissingOrUpdatedFilesTest extends TestCase
{
    public static function setUpBeforeClass(): void
    {
        if (!defined('_PS_ADMIN_DIR_')) {
            define('_PS_ADMIN_DIR_', _PS_ROOT_DIR_ . '/admin-dev');
        }
    }

    public function testAnUnreachableChecksumListIsReportedAsAnError(): void
    {
        $result = $this->buildChecker(null)->getListOfUpdatedFiles();

        // Without this the page tells the merchant "No change has been detected in your files"
        // after comparing nothing at all, which is the one answer it must never give.
        $this->assertTrue($result['error']);
        $this->assertSame([], $result['missing']);
        $this->assertSame([], $result['updated']);
    }

    public function testAReadableChecksumListIsNotReportedAsAnError(): void
    {
        $result = $this->buildChecker($this->checksumList([
            'composer.json' => md5_file(_PS_ROOT_DIR_ . '/composer.json'),
        ]))->getListOfUpdatedFiles();

        $this->assertFalse($result['error']);
        $this->assertSame([], $result['missing']);
        $this->assertSame([], $result['updated']);
    }

    public function testAFileAbsentFromTheInstallIsReportedAsMissing(): void
    {
        $result = $this->buildChecker($this->checksumList([
            'this-file-is-not-part-of-the-install.php' => 'd41d8cd98f00b204e9800998ecf8427e',
        ]))->getListOfUpdatedFiles();

        $this->assertSame(['this-file-is-not-part-of-the-install.php'], $result['missing']);
        $this->assertSame([], $result['updated']);
        $this->assertFalse($result['error']);
    }

    public function testAFileWhoseContentDiffersIsReportedAsUpdated(): void
    {
        $result = $this->buildChecker($this->checksumList([
            'composer.json' => 'd41d8cd98f00b204e9800998ecf8427e',
        ]))->getListOfUpdatedFiles();

        $this->assertSame(['composer.json'], $result['updated']);
        $this->assertSame([], $result['missing']);
        $this->assertFalse($result['error']);
    }

    public function testExcludedPathsAreStillSkipped(): void
    {
        $result = $this->buildChecker($this->checksumList([
            'themes/this-theme-does-not-exist/config.yml' => 'd41d8cd98f00b204e9800998ecf8427e',
            'img/this-image-does-not-exist.png' => 'd41d8cd98f00b204e9800998ecf8427e',
        ]))->getListOfUpdatedFiles();

        $this->assertSame([], $result['missing']);
        $this->assertSame([], $result['updated']);
    }

    /**
     * @param array<string, string> $files relative path => expected md5
     */
    private function checksumList(array $files): SimpleXMLElement
    {
        $xml = new SimpleXMLElement('<ps_root_dir version="test"/>');

        foreach ($files as $path => $md5) {
            $node = $xml;
            $segments = explode('/', $path);
            $name = array_pop($segments);

            foreach ($segments as $segment) {
                $node = $node->addChild('dir');
                $node->addAttribute('name', $segment);
            }

            $file = $node->addChild('md5file', $md5);
            $file->addAttribute('name', $name);
        }

        return $xml;
    }

    private function buildChecker(?SimpleXMLElement $checksumList): CheckMissingOrUpdatedFiles
    {
        return new class($checksumList) extends CheckMissingOrUpdatedFiles {
            /**
             * @var SimpleXMLElement|null
             */
            private $checksumList;

            public function __construct(?SimpleXMLElement $checksumList)
            {
                $this->checksumList = $checksumList;
            }

            protected function loadChecksumList()
            {
                return $this->checksumList;
            }
        };
    }
}
