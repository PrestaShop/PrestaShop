<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

use PHPUnit\Framework\TestCase;

/**
 * Tests for the phpstan exclusion pattern used in ReleaseCreator::$patternsRemoveList.
 *
 * Old pattern: 'phpstan(.*)?'
 *   - Matched ANY path containing "phpstan" (binaries, .phar, vendor configs…).
 *
 * New pattern: '^(?!.*vendor).*phpstan.*\.neon'
 *   - ^              — anchors from the start of the string.
 *   - (?!.*vendor)   — negative lookahead: rejects paths that contain "vendor" anywhere.
 *   - .*phpstan.*    — phpstan may appear anywhere after the anchor (supports absolute paths).
 *   - \.neon         — restricts exclusion to .neon config files only.
 */
class ReleaseCreatorTest extends TestCase
{
    private const OLD_PHPSTAN_PATTERN = 'phpstan(.*)?';
    private const NEW_PHPSTAN_PATTERN = '^(?!.*vendor).*phpstan.*\.neon';

    private function patternMatches(string $pattern, string $path): bool
    {
        return preg_match('#' . $pattern . '#', $path) === 1;
    }

    // -------------------------------------------------------------------------
    // New pattern — .neon files OUTSIDE vendor that SHOULD be excluded
    // -------------------------------------------------------------------------

    /**
     * @dataProvider providePhpstanNeonFilesOutsideVendor
     */
    public function testNewPhpstanPatternMatchesNeonFilesOutsideVendor(string $path): void
    {
        $this->assertTrue(
            $this->patternMatches(self::NEW_PHPSTAN_PATTERN, $path),
            "Expected new phpstan pattern to match '{$path}' (should be excluded from release)"
        );
    }

    public static function providePhpstanNeonFilesOutsideVendor(): array
    {
        return [
            'phpstan.neon at root'                              => ['phpstan.neon'],
            'phpstan.neon.dist (partial hit on .neon)'          => ['phpstan.neon.dist'],
            'phpstan-custom.neon'                               => ['phpstan-custom.neon'],
            'phpstan-strict.neon'                               => ['phpstan-strict.neon'],
            'phpstan.neon in subdirectory'                      => ['config/phpstan.neon'],
            'phpstan.neon via absolute path without vendor'     => ['/tmp/build/prestashop/phpstan.neon'],
            'phpstan.neon via absolute path in subdir'          => ['/tmp/build/prestashop/config/phpstan.neon'],
        ];
    }

    // -------------------------------------------------------------------------
    // New pattern — vendor .neon files that SHOULD NOT be excluded
    // -------------------------------------------------------------------------

    /**
     * @dataProvider providePhpstanNeonFilesInsideVendor
     */
    public function testNewPhpstanPatternDoesNotMatchVendorNeonFiles(string $path): void
    {
        $this->assertFalse(
            $this->patternMatches(self::NEW_PHPSTAN_PATTERN, $path),
            "Expected new phpstan pattern NOT to match '{$path}' (vendor files must be kept)"
        );
    }

    public static function providePhpstanNeonFilesInsideVendor(): array
    {
        return [
            'vendor/phpstan/phpstan/phpstan.neon'              => ['vendor/phpstan/phpstan/phpstan.neon'],
            'vendor/phpstan/phpstan-strict-rules/rules.neon'   => ['vendor/phpstan/phpstan-strict-rules/rules.neon'],
            'vendor/phpstan/extension-installer/phpstan.neon'  => ['vendor/phpstan/extension-installer/phpstan.neon'],
            'vendor/foo/bar/phpstan.neon'                      => ['vendor/foo/bar/phpstan.neon'],
            'absolute path with vendor'                        => ['/tmp/build/prestashop/vendor/phpstan/phpstan.neon'],
        ];
    }

    // -------------------------------------------------------------------------
    // New pattern — non-.neon phpstan artifacts that SHOULD NOT be excluded
    // -------------------------------------------------------------------------

    /**
     * @dataProvider provideNonNeonPhpstanFiles
     */
    public function testNewPhpstanPatternDoesNotMatchNonNeonFiles(string $path): void
    {
        $this->assertFalse(
            $this->patternMatches(self::NEW_PHPSTAN_PATTERN, $path),
            "Expected new phpstan pattern NOT to match '{$path}' (only .neon files should be excluded)"
        );
    }

    public static function provideNonNeonPhpstanFiles(): array
    {
        return [
            'phpstan.phar'     => ['phpstan.phar'],
            'phpstan (binary)' => ['phpstan'],
            'phpstan.php'      => ['phpstan.php'],
            'phpstan.xml'      => ['phpstan.xml'],
        ];
    }

    // -------------------------------------------------------------------------
    // Regression: old pattern was too broad
    // -------------------------------------------------------------------------

    /**
     * @dataProvider provideNonNeonPhpstanFiles
     */
    public function testOldPhpstanPatternWronglyMatchedNonNeonFiles(string $path): void
    {
        $this->assertTrue(
            $this->patternMatches(self::OLD_PHPSTAN_PATTERN, $path),
            "Old pattern matched '{$path}' — too broad, this is the bug being fixed"
        );
    }

    /**
     * @dataProvider providePhpstanNeonFilesInsideVendor
     */
    public function testOldPhpstanPatternWronglyMatchedVendorFiles(string $path): void
    {
        $this->assertTrue(
            $this->patternMatches(self::OLD_PHPSTAN_PATTERN, $path),
            "Old pattern matched '{$path}' — vendor files should have been kept"
        );
    }
}
