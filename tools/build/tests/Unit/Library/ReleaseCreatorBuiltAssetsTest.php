<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

use PHPUnit\Framework\TestCase;

/**
 * Tests for ReleaseCreator::checkBuiltAssets(): an asset build that failed must stop the release.
 */
class ReleaseCreatorBuiltAssetsTest extends TestCase
{
    private const BUILT_FILES = [
        'admin-dev/themes/default/public/theme.css',
        'admin-dev/themes/new-theme/public/theme.css',
        'themes/core.js',
        'themes/classic/assets/css/theme.css',
        'themes/hummingbird/assets/css/theme.css',
    ];

    /** @var string */
    private $projectPath;

    protected function setUp(): void
    {
        $this->projectPath = sys_get_temp_dir() . '/release-creator-assets-' . uniqid();
        foreach (self::BUILT_FILES as $file) {
            $path = "{$this->projectPath}/{$file}";
            if (!is_dir(dirname($path))) {
                mkdir(dirname($path), 0777, true);
            }
            touch($path);
        }
        mkdir("{$this->projectPath}/themes/classic/_dev", 0777, true);
    }

    protected function tearDown(): void
    {
        exec('rm -rf ' . escapeshellarg($this->projectPath));
    }

    public function testCompleteBuildPasses(): void
    {
        $this->checkBuiltAssets();
        $this->addToAssertionCount(1);
    }

    public function testLeftoverBuildLockFails(): void
    {
        touch("{$this->projectPath}/admin-dev/themes/new-theme/buildLock");

        $this->expectException(BuildException::class);
        $this->expectExceptionMessage('admin-dev/themes/new-theme/buildLock is still present');
        $this->checkBuiltAssets();
    }

    public function testMissingBuiltFileFails(): void
    {
        unlink("{$this->projectPath}/admin-dev/themes/new-theme/public/theme.css");

        $this->expectException(BuildException::class);
        $this->expectExceptionMessage('admin-dev/themes/new-theme/public/theme.css is missing');
        $this->checkBuiltAssets();
    }

    private function checkBuiltAssets(): void
    {
        $class = new ReflectionClass(ReleaseCreator::class);
        $releaseCreator = $class->newInstanceWithoutConstructor();
        $property = $class->getProperty('tempProjectPath');
        $property->setValue($releaseCreator, $this->projectPath);
        $method = $class->getMethod('checkBuiltAssets');
        $method->invoke($releaseCreator);
    }
}
