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
 * CommonController::searchGridAction is guarded by
 * `is_granted('read', request.get('_legacy_controller'))`. A route that reaches it without that
 * default resolves an empty permission subject, and Access::isGranted() then rejects the slug it
 * builds - the grid's filter form answers 500 instead of filtering.
 *
 * Nothing else catches this: app/config/config_test.yml swaps PageVoter for a test double whose
 * voteOnAttribute() returns true unconditionally, so an integration test cannot see a broken
 * security expression.
 */
class SearchGridRoutePermissionSubjectTest extends TestCase
{
    private const ROUTING_DIR = __DIR__ . '/../../../../src/PrestaShopBundle/Resources/config/routing';

    private const GUARDED_ACTION = 'CommonController::searchGridAction';

    public function testEverySearchGridRouteDeclaresItsPermissionSubject(): void
    {
        $routes = $this->getRoutesReaching(self::GUARDED_ACTION);

        // Guards the assertion below against passing because the scan found nothing.
        $this->assertGreaterThan(
            30,
            count($routes),
            'the routing scan found almost no search-grid routes, so it is not looking where it should'
        );

        $withoutSubject = [];
        foreach ($routes as $name => $file) {
            if (!isset($this->routeDefaults($file, $name)['_legacy_controller'])) {
                $withoutSubject[] = sprintf('%s (%s)', $name, $file);
            }
        }

        $this->assertSame(
            [],
            $withoutSubject,
            "these routes reach searchGridAction without a _legacy_controller default, so filtering their grid answers 500:\n"
            . implode("\n", $withoutSubject)
        );
    }

    /**
     * @return array<string, string> route name => routing file, relative to the routing directory
     */
    private function getRoutesReaching(string $action): array
    {
        $found = [];
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
                if (!is_array($definition) || !isset($definition['defaults']['_controller'])) {
                    continue;
                }
                if (str_contains((string) $definition['defaults']['_controller'], $action)) {
                    $found[$name] = $file->getPathname();
                }
            }
        }

        return $found;
    }

    /**
     * @return array<string, mixed>
     */
    private function routeDefaults(string $file, string $routeName): array
    {
        $parsed = Yaml::parseFile($file);

        return $parsed[$routeName]['defaults'] ?? [];
    }
}
