<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\PrestaShopBundle\Routing;

use PHPUnit\Framework\TestCase;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use Symfony\Component\Yaml\Yaml;

/**
 * The OWASP Core Rule Set matches `\bdelete\b\W*?\bfrom\b` against REQUEST_FILENAME at paranoia level 1
 * (rule 959075, "SQL Injection Attack"). A route path containing that shape is refused with a 403 by any
 * ModSecurity install running the default rules, which merchants on managed hosting often cannot change.
 * The path text carries no meaning of its own - route names are what the application generates URLs from
 * - so it costs nothing to stay clear of it.
 */
class RoutePathsAvoidWafSqlPatternTest extends TestCase
{
    private const ROUTING_DIR = __DIR__ . '/../../../../src/PrestaShopBundle/Resources/config/routing';

    /**
     * The literal CRS 959075 pattern.
     */
    private const CRS_SQL_PATTERN = '/\bdelete\b\W*?\bfrom\b/i';

    public function testNoRoutePathLooksLikeAnSqlDeleteStatement(): void
    {
        $paths = $this->getAllRoutePaths();

        $this->assertGreaterThan(
            200,
            count($paths),
            'the routing scan found almost no paths, so it is not looking where it should'
        );

        $offending = array_filter(
            $paths,
            fn (string $path): bool => preg_match(self::CRS_SQL_PATTERN, $path) === 1
        );

        $this->assertSame(
            [],
            $offending,
            "these route paths match the OWASP CRS 959075 SQL-injection pattern and answer 403 behind default ModSecurity rules:\n"
            . implode("\n", $offending)
        );
    }

    /**
     * @return array<string, string> route name => path
     */
    private function getAllRoutePaths(): array
    {
        $paths = [];
        $files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator(self::ROUTING_DIR));
        foreach ($files as $file) {
            if ($file->isDir() || $file->getExtension() !== 'yml') {
                continue;
            }
            $parsed = Yaml::parseFile($file->getPathname());
            if (!is_array($parsed)) {
                continue;
            }
            foreach ($parsed as $name => $definition) {
                if (is_array($definition) && isset($definition['path']) && is_string($definition['path'])) {
                    $paths[(string) $name] = $definition['path'];
                }
            }
        }

        return $paths;
    }
}
