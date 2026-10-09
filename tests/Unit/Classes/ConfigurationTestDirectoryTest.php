<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\Classes;

use ConfigurationTest;
use PHPUnit\Framework\TestCase;
use ReflectionMethod;

class ConfigurationTestDirectoryTest extends TestCase
{
    /**
     * @var string
     */
    private $directory;

    protected function setUp(): void
    {
        parent::setUp();

        $this->directory = sys_get_temp_dir() . DIRECTORY_SEPARATOR . uniqid('ps_dir_', true);
        mkdir($this->directory);
    }

    protected function tearDown(): void
    {
        if (is_dir($this->directory)) {
            rmdir($this->directory);
        }

        parent::tearDown();
    }

    public function testAWritableDirectoryIsAccepted(): void
    {
        $this->assertTrue($this->isDirectoryWritable($this->directory));
    }

    /**
     * Neither the permission bits nor a write can answer for a path that is not there, and the check
     * has to say no rather than throw.
     */
    public function testAMissingDirectoryIsRejected(): void
    {
        $this->assertFalse($this->isDirectoryWritable($this->directory . DIRECTORY_SEPARATOR . 'absent'));
    }

    /**
     * The check must leave nothing behind: the write probe only runs to confirm a refusal, and when
     * it does run it removes its own file.
     */
    public function testTheCheckLeavesNoFileBehind(): void
    {
        $this->isDirectoryWritable($this->directory);

        $this->assertSame([], array_values(array_diff(scandir($this->directory) ?: [], ['.', '..'])));
    }

    private function isDirectoryWritable(string $directory): bool
    {
        $method = new ReflectionMethod(ConfigurationTest::class, 'isDirectoryWritable');
        $method->setAccessible(true);

        return $method->invoke(null, $directory);
    }
}
