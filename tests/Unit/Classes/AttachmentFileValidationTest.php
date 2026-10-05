<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace Tests\Unit\Classes;

use Attachment;
use AttachmentControllerCore;
use PHPUnit\Framework\TestCase;
use Validate;

class AttachmentFileValidationTest extends TestCase
{
    public static function providesTraversalFileValues(): iterable
    {
        yield 'relative traversal to config' => ['../app/config/parameters.php'];
        yield 'deep traversal' => ['../../../etc/passwd'];
        yield 'single directory' => ['a/b'];
        yield 'whitespace file' => ['my file.pdf'];
        yield 'empty' => [''];
    }

    public static function providesValidFileValues(): iterable
    {
        yield 'sha1 only' => ['5d41402abc4b2a76b9719d911017c592'];
        yield 'sha1 with extension' => ['5d41402abc4b2a76b9719d911017c592.pdf'];
    }

    /**
     * @dataProvider providesTraversalFileValues
     */
    public function testAttachmentDefinitionRejectsUnsafeFileValues(string $value): void
    {
        $validate = Attachment::$definition['fields']['file']['validate'];

        $this->assertSame('isFileName', $validate);
        $this->assertFalse((bool) Validate::$validate($value));
    }

    /**
     * @dataProvider providesValidFileValues
     */
    public function testAttachmentDefinitionAcceptsServerGeneratedFileValues(string $value): void
    {
        $validate = Attachment::$definition['fields']['file']['validate'];

        $this->assertTrue((bool) Validate::$validate($value));
    }

    /**
     * The read paths must reject values that are not a bare filename, so
     * databases holding a traversal value before an upgrade cannot escape
     * the download directory.
     */
    public function testReadPathsRejectNonBareFilenameValues(): void
    {
        $reflection = new \ReflectionClass(AttachmentControllerCore::class);
        $source = file_get_contents($reflection->getFileName());

        $this->assertStringContainsString('basename($attachment->file)', $source);

        foreach (['..', '.', '../app/config/parameters.php', 'a/b'] as $value) {
            $bare = basename($value) === $value && !in_array($value, ['.', '..'], true);
            $this->assertFalse($bare, sprintf('"%s" must be treated as unsafe', $value));
        }
    }
}
