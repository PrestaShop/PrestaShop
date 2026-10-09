<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\Core\Translation;

use PHPUnit\Framework\TestCase;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

/**
 * OrderController::getErrorMessages() renders OrderException and InvoiceException with
 * $this->trans($e->getMessage()), so the wording never appears as a literal in a trans() call and the
 * extractor cannot see it. classes/lang/KeysReference/OrderExceptionLang.php exists to reference those
 * literals; this test keeps it in step with the code that throws them.
 */
class OrderExceptionKeysReferenceTest extends TestCase
{
    private const EXCEPTIONS = ['OrderException', 'InvoiceException'];

    private const KEYS_REFERENCE = 'classes/lang/KeysReference/OrderExceptionLang.php';

    private const SCANNED_DIRECTORIES = ['src', 'classes'];

    public function testEveryThrownLiteralIsReferencedForTranslation(): void
    {
        $referenced = $this->referencedKeys();
        $thrown = $this->thrownLiterals();

        $this->assertNotEmpty($thrown, 'No literal exception message was found, the scanner is broken.');

        foreach ($thrown as $message => $origins) {
            $this->assertContains(
                $message,
                $referenced,
                sprintf(
                    '"%s" is thrown at %s but is not referenced in %s, so it cannot be translated.',
                    $message,
                    implode(', ', $origins),
                    self::KEYS_REFERENCE
                )
            );
        }
    }

    public function testNoKeyIsReferencedForAMessageThatIsNoLongerThrown(): void
    {
        $thrown = array_keys($this->thrownLiterals());

        foreach ($this->referencedKeys() as $key) {
            $this->assertContains(
                $key,
                $thrown,
                sprintf('"%s" is referenced in %s but no longer thrown.', $key, self::KEYS_REFERENCE)
            );
        }
    }

    /**
     * @return string[]
     */
    private function referencedKeys(): array
    {
        $keys = [];
        foreach ($this->stringCalls(file_get_contents($this->rootDir() . '/' . self::KEYS_REFERENCE), 'trans') as $call) {
            $keys[] = $call;
        }

        return $keys;
    }

    /**
     * Messages thrown with a single literal as the first constructor argument. An interpolated message
     * (sprintf, concatenation) is deliberately skipped: the placeholder is already substituted before
     * trans() sees it, so referencing it would produce one catalogue key per order.
     *
     * @return array<string, string[]> message => files it is thrown from
     */
    private function thrownLiterals(): array
    {
        $found = [];
        foreach (self::SCANNED_DIRECTORIES as $directory) {
            $iterator = new RecursiveIteratorIterator(
                new RecursiveDirectoryIterator($this->rootDir() . '/' . $directory)
            );
            foreach ($iterator as $file) {
                if (!$file->isFile() || $file->getExtension() !== 'php') {
                    continue;
                }
                $contents = file_get_contents($file->getPathname());
                foreach (self::EXCEPTIONS as $exception) {
                    if (!str_contains($contents, 'new ' . $exception . '(')) {
                        continue;
                    }
                    foreach ($this->stringCalls($contents, $exception, 'new') as $message) {
                        $found[$message][] = $directory . '/' . $file->getFilename();
                    }
                }
            }
        }

        return $found;
    }

    /**
     * First argument of every `$name(` call whose first argument is a single quoted string.
     *
     * @return string[]
     */
    private function stringCalls(string $contents, string $name, ?string $precededBy = null): array
    {
        $tokens = array_values(array_filter(
            token_get_all($contents),
            static fn ($token) => !is_array($token) || !in_array($token[0], [T_WHITESPACE, T_COMMENT, T_DOC_COMMENT], true)
        ));

        $messages = [];
        foreach ($tokens as $i => $token) {
            if (!is_array($token) || $token[0] !== T_STRING || $token[1] !== $name) {
                continue;
            }
            if ($precededBy !== null && (!isset($tokens[$i - 1]) || !is_array($tokens[$i - 1]) || $tokens[$i - 1][1] !== $precededBy)) {
                continue;
            }
            if (($tokens[$i + 1] ?? null) !== '(') {
                continue;
            }
            $argument = $tokens[$i + 2] ?? null;
            $after = $tokens[$i + 3] ?? null;
            if (!is_array($argument) || $argument[0] !== T_CONSTANT_ENCAPSED_STRING) {
                continue;
            }
            if ($after !== ',' && $after !== ')') {
                continue;
            }
            $messages[] = stripslashes(substr($argument[1], 1, -1));
        }

        return $messages;
    }

    private function rootDir(): string
    {
        return dirname(__DIR__, 4);
    }
}
