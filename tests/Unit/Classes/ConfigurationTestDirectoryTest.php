<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\Classes;

use ConfigurationTest;
use PHPUnit\Framework\TestCase;

class ConfigurationTestDirectoryTest extends TestCase
{
    /**
     * @var string
     */
    private $relativeDirectory;

    /**
     * @var string
     */
    private $directory;

    protected function setUp(): void
    {
        parent::setUp();

        $this->relativeDirectory = 'var' . DIRECTORY_SEPARATOR . uniqid('ps_dir_test_', true);
        $this->directory = _PS_ROOT_DIR_ . DIRECTORY_SEPARATOR . $this->relativeDirectory;
        mkdir($this->directory . DIRECTORY_SEPARATOR . 'sub', 0775, true);
    }

    protected function tearDown(): void
    {
        foreach ([$this->directory . DIRECTORY_SEPARATOR . 'sub', $this->directory] as $directory) {
            if (is_dir($directory)) {
                chmod($directory, 0775);
                rmdir($directory);
            }
        }

        parent::tearDown();
    }

    public function testAWritableDirectoryIsAccepted(): void
    {
        $this->assertTrue(ConfigurationTest::test_dir($this->relativeDirectory, true));
        $this->assertSame([], array_values(array_diff(scandir($this->directory) ?: [], ['.', '..', 'sub'])));
    }

    public function testAReadOnlySubdirectoryIsReported(): void
    {
        $subDirectory = $this->directory . DIRECTORY_SEPARATOR . 'sub';
        chmod($subDirectory, 0555);
        if (is_writable($subDirectory)) {
            $this->markTestSkipped('The permission bits are not enforced for this user.');
        }

        $report = null;
        $this->assertFalse(ConfigurationTest::test_dir($this->relativeDirectory, true, $report));
        $this->assertSame(sprintf('Directory %s is not writable', $subDirectory), $report);
    }

    public function testAMissingDirectoryIsReported(): void
    {
        $report = null;
        $this->assertFalse(ConfigurationTest::test_dir($this->relativeDirectory . DIRECTORY_SEPARATOR . 'absent', false, $report));
        $this->assertStringContainsString('does not exist', (string) $report);
    }
}
