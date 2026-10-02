<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Integration\Classes;

use Language;
use PHPUnit\Framework\TestCase;

/**
 * Copying a language between themes listed the source theme's lang/<iso>.php whether or not it was
 * there. No theme shipped since translations moved to XLIFF has that directory, so the copy always
 * had one source it could not read and reported it as a failure.
 */
class LanguageFilesListTest extends TestCase
{
    public function testItDoesNotListAThemeLanguageFileThatIsNotThere(): void
    {
        $missing = _PS_ROOT_DIR_ . '/themes/classic/lang/cs.php';
        $this->assertFileDoesNotExist($missing, 'the shipped theme is expected to have no lang directory');

        $files = Language::getFilesList('cs', 'classic', 'cs', 'hummingbird', false, false, true);

        $this->assertArrayNotHasKey($missing, $files, 'a source that is not there must not be listed');
    }

    /**
     * Everything listed has to be readable, otherwise the copier reports a failure per entry.
     */
    public function testEverythingListedForACopyExists(): void
    {
        $files = Language::getFilesList('en', 'classic', 'en', 'hummingbird', false, false, true);

        $unreadable = array_values(array_filter(
            array_keys($files),
            static function (string $source): bool {
                return !file_exists($source);
            }
        ));

        $this->assertSame([], $unreadable, 'the copy list must only contain sources that exist');
    }
}
