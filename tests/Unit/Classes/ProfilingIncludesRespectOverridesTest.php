<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\Classes;

use PHPUnit\Framework\TestCase;

/**
 * Every file in tools/profiling/ that declares a bare class name claims the same
 * `X extends XCore` inheritance slot an override/classes/X.php declares. Including one unconditionally
 * means the autoloader is never asked for that class, so the override never loads and any call to an
 * override-only method dies with `Call to undefined method`. config/config.inc.php therefore has to
 * include each of them behind an override check, and has to keep doing so for any profiling class
 * added later.
 */
class ProfilingIncludesRespectOverridesTest extends TestCase
{
    private const PROFILING_DIR = _PS_ROOT_DIR_ . '/tools/profiling/';

    private const BOOTSTRAP = _PS_ROOT_DIR_ . '/config/config.inc.php';

    /**
     * @return string the `if (_PS_DEBUG_PROFILING_) { ... }` block, so a failure prints that rather
     *                than the whole bootstrap
     */
    private function getProfilingBlock(): string
    {
        $bootstrap = file_get_contents(self::BOOTSTRAP);
        $start = strpos($bootstrap, 'if (_PS_DEBUG_PROFILING_) {');
        self::assertNotFalse($start, 'the profiling block is gone from ' . self::BOOTSTRAP);

        $end = strpos($bootstrap, "\n}\n", $start);
        self::assertNotFalse($end);

        return substr($bootstrap, $start, $end - $start);
    }

    /**
     * @return string[] the profiling files that declare a class an override can also declare
     */
    private function getOverridableProfilingClasses(): array
    {
        $classes = [];
        foreach (glob(self::PROFILING_DIR . '*.php') as $file) {
            $source = file_get_contents($file);
            if (!preg_match('/^(?:abstract )?class ([A-Za-z0-9_]+) extends ([A-Za-z0-9_]+)/m', $source, $matches)) {
                continue;
            }
            // Only a class extending its own Core counterpart competes with an override.
            if ($matches[2] !== $matches[1] . 'Core') {
                continue;
            }
            $classes[] = $matches[1];
        }

        sort($classes);

        return $classes;
    }

    public function testTheProfilingDirectoryStillHoldsClassesAnOverrideCanClaim(): void
    {
        // Guards the two tests below against passing vacuously if the directory is ever restructured.
        $this->assertSame(
            ['Controller', 'Db', 'Hook', 'Module', 'ObjectModel', 'Tools'],
            $this->getOverridableProfilingClasses()
        );
    }

    public function testNoProfilingClassIsIncludedUnconditionally(): void
    {
        $block = $this->getProfilingBlock();

        foreach ($this->getOverridableProfilingClasses() as $class) {
            $this->assertStringNotContainsString(
                "include_once _PS_TOOL_DIR_ . 'profiling/" . $class . ".php';",
                $block,
                sprintf(
                    'profiling/%s.php is included by name, which silently disables override/classes/%s.php.',
                    $class,
                    $class
                )
            );
        }
    }

    public function testTheBootstrapGuardsEveryOverridableProfilingClass(): void
    {
        $block = $this->getProfilingBlock();

        $this->assertMatchesRegularExpression(
            '/file_exists\(_PS_OVERRIDE_DIR_ \. \'classes\/\' \. \$\w+ \. \'\.php\'\)/',
            $block,
            'the profiling includes are no longer guarded by an override check'
        );

        // The guarded list has to name every class the directory offers, or a newly added one is
        // included unconditionally again.
        $this->assertSame(1, preg_match('/foreach \(\[([^\]]+)\] as \$\w+\) \{/', $block, $matches));

        $guarded = array_map(
            static fn (string $name): string => trim($name, " \t\n'"),
            explode(',', $matches[1])
        );
        sort($guarded);

        $this->assertSame($this->getOverridableProfilingClasses(), $guarded);
    }
}
