<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\PrestaShopBundle\DependencyInjection;

use PHPUnit\Framework\TestCase;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

/**
 * Referencing one of our own deprecated aliases from a service definition raises an E_USER_DEPRECATED
 * every time the container is compiled, from ResolveReferencesToAliasesPass. Those notices are ours,
 * not the framework's, and nothing but this test stops them from creeping back.
 */
class DeprecatedServiceAliasReferenceTest extends TestCase
{
    private const CONFIG_DIRECTORIES = [
        __DIR__ . '/../../../../src/PrestaShopBundle/Resources/config',
        __DIR__ . '/../../../../app/config',
    ];

    public function testNoServiceDefinitionReferencesADeprecatedAlias(): void
    {
        $deprecatedAliases = $this->findDeprecatedAliases();
        $this->assertNotEmpty($deprecatedAliases, 'no deprecated aliases were found at all, so this test would pass vacuously');

        $offenders = [];
        foreach ($this->configFiles() as $file) {
            foreach (file($file) as $number => $line) {
                if (preg_match('#^\s*alias:\s#', $line)) {
                    // the alias declaration itself, not a reference to one
                    continue;
                }

                foreach ($deprecatedAliases as $alias) {
                    if (preg_match('#@\??' . preg_quote($alias, '#') . '(?![\w.\\\\])#', $line)) {
                        $offenders[] = sprintf('%s:%d references @%s', basename($file), $number + 1, $alias);
                    }
                }
            }
        }

        $this->assertSame([], $offenders, "Use the class these aliases point at instead:\n" . implode("\n", $offenders));
    }

    /**
     * @return array<string>
     */
    private function findDeprecatedAliases(): array
    {
        $aliases = [];

        foreach ($this->configFiles() as $file) {
            $lines = file($file, FILE_IGNORE_NEW_LINES);

            foreach ($lines as $index => $line) {
                if (!preg_match('#^  ([A-Za-z0-9_.\\\\]+):\s*$#', $line, $matches)) {
                    continue;
                }

                $isAlias = false;
                $isDeprecated = false;

                for ($i = $index + 1; $i < count($lines) && (str_starts_with($lines[$i], '    ') || '' === trim($lines[$i])); ++$i) {
                    if (preg_match('#^    alias:\s#', $lines[$i])) {
                        $isAlias = true;
                    }
                    if (preg_match('#^    deprecated:#', $lines[$i])) {
                        $isDeprecated = true;
                    }
                }

                if ($isAlias && $isDeprecated) {
                    $aliases[] = $matches[1];
                }
            }
        }

        return array_values(array_unique($aliases));
    }

    /**
     * @return array<string>
     */
    private function configFiles(): array
    {
        static $files = null;

        if (null !== $files) {
            return $files;
        }

        $files = [];
        foreach (self::CONFIG_DIRECTORIES as $directory) {
            if (!is_dir($directory)) {
                continue;
            }

            $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($directory));
            foreach ($iterator as $file) {
                if ($file->isFile() && 'yml' === $file->getExtension()) {
                    $files[] = $file->getPathname();
                }
            }
        }

        sort($files);

        return $files;
    }
}
